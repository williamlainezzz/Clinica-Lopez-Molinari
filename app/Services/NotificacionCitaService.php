<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Usuario;
use App\Notifications\CitaNotificacion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Auth\Authenticatable;

class NotificacionCitaService
{
    public function enviarNotificacionCreacionCita(int $codCita): void
    {
        $this->enviarNotificacionCita($codCita, 'CREACION');
    }

    public function enviarRecordatorio24H(int $codCita): void
    {
        $this->enviarNotificacionCita($codCita, 'RECORDATORIO_24H');
    }

    public function enviarRecordatorio1H(int $codCita): void
    {
        $this->enviarNotificacionCita($codCita, 'RECORDATORIO_1H');
    }

    public function contarNoLeidasParaUsuario(?Authenticatable $user): int
    {
        if (!$user || !Schema::hasTable('tbl_notificacion') || !Schema::hasTable('tbl_cita')) {
            return 0;
        }

        $query = $this->baseNotificacionQuery()
            ->where('n.LEIDA', 0);

        $this->aplicarFiltroPorRol($query, $user);

        return (int) $query->count();
    }

    public function baseNotificacionQuery()
    {
        return DB::table('tbl_notificacion as n')
            ->leftJoin('tbl_cita as c', 'n.FK_COD_CITA', '=', 'c.COD_CITA')
            ->leftJoin('tbl_estado_cita as e', 'c.ESTADO_CITA', '=', 'e.COD_ESTADO')
            ->leftJoin('tbl_persona as p', 'c.FK_COD_PACIENTE', '=', 'p.COD_PERSONA')
            ->leftJoin('tbl_persona as d', 'c.FK_COD_DOCTOR', '=', 'd.COD_PERSONA');
    }

    public function aplicarFiltroPorRol($query, $user): void
    {
        $rolNombre = strtoupper(optional($user->rol)->NOM_ROL ?? '');
        $personaId = (int) ($user->FK_COD_PERSONA ?? 0);

        if ($rolNombre === 'DOCTOR' && $personaId > 0) {
            $query->where('c.FK_COD_DOCTOR', $personaId);
        }

        if ($rolNombre === 'PACIENTE' && $personaId > 0) {
            $query->where('c.FK_COD_PACIENTE', $personaId);
        }
    }

    private function enviarNotificacionCita(int $codCita, string $tipo): void
    {
        if (!Schema::hasTable('tbl_cita') || !Schema::hasTable('tbl_persona')) {
            return;
        }

        $cita = $this->buscarCita($codCita);

        if (!$cita) {
            return;
        }

        $payload = $this->construirPayload($cita, $tipo);
        $mensaje = $payload['mensaje'] ?? '';

        $this->registrarNotificacion($codCita, $mensaje, $tipo);
        $this->enviarCorreo($cita->paciente_persona_id, $payload);
    }

    private function registrarNotificacion(int $codCita, string $mensaje, string $tipo): void
    {
        if (!Schema::hasTable('tbl_notificacion')) {
            return;
        }

        try {
            Notificacion::create([
                'FK_COD_CITA'       => $codCita,
                'MSG_NOTIFICACION'  => $mensaje,
                'FEC_ENVIO'         => now(),
                'TIPO_NOTIFICACION' => $tipo,
                'LEIDA'             => 0,
            ]);
        } catch (\Throwable $e) {
            // Silenciar para no interrumpir el flujo principal.
        }
    }

    private function enviarCorreo(int $pacientePersonaId, array $payload): void
    {
        $usuario = Usuario::where('FK_COD_PERSONA', $pacientePersonaId)->first();
        $correo  = $this->buscarCorreoPersona($pacientePersonaId);

        if ($usuario) {
            $usuario->notify(new CitaNotificacion($payload));
            return;
        }

        if ($correo) {
            Notification::route('mail', $correo)->notify(new CitaNotificacion($payload));
        }
    }

    private function buscarCita(int $codCita): ?object
    {
        return DB::table('tbl_cita as c')
            ->join('tbl_persona as p', 'c.FK_COD_PACIENTE', '=', 'p.COD_PERSONA')
            ->join('tbl_persona as d', 'c.FK_COD_DOCTOR', '=', 'd.COD_PERSONA')
            ->leftJoin('tbl_estado_cita as e', 'c.ESTADO_CITA', '=', 'e.COD_ESTADO')
            ->select([
                'c.COD_CITA',
                'c.FK_COD_DOCTOR',
                'c.FK_COD_PACIENTE',
                'c.FEC_CITA',
                'c.HOR_CITA',
                'c.HOR_FIN',
                'c.MOT_CITA',
                'c.OBSERVACIONES',
                'e.NOM_ESTADO as estado_nombre',
                'p.COD_PERSONA as paciente_persona_id',
                DB::raw("CONCAT(p.PRIMER_NOMBRE,' ',p.PRIMER_APELLIDO) as paciente_nombre"),
                'd.COD_PERSONA as doctor_persona_id',
                DB::raw("CONCAT(d.PRIMER_NOMBRE,' ',d.PRIMER_APELLIDO) as doctor_nombre"),
            ])
            ->where('c.COD_CITA', $codCita)
            ->first();
    }

    private function construirPayload(object $cita, string $tipo): array
    {
        $fecha = $this->formatearFecha($cita->FEC_CITA ?? null);
        $hora  = $this->formatearHora($cita->HOR_CITA ?? null);

        $tipoLegible = $this->tipoLegible($tipo);
        $clinica     = config('app.name', 'Clínica');

        $mensaje = sprintf(
            'Su cita con %s está programada para el %s a las %s.',
            $cita->doctor_nombre,
            $fecha,
            $hora
        );

        return [
            'subject'      => "{$clinica} - {$tipoLegible}",
            'titulo'       => $tipoLegible,
            'paciente'     => $cita->paciente_nombre,
            'doctor'       => $cita->doctor_nombre,
            'clinica'      => $clinica,
            'fecha'        => $fecha,
            'hora'         => $hora,
            'tipo'         => $tipo,
            'tipo_legible' => $tipoLegible,
            'mensaje'      => $mensaje,
            'nota'         => $cita->OBSERVACIONES,
        ];
    }

    private function formatearFecha(?string $fecha): string
    {
        if (!$fecha) {
            return '';
        }

        try {
            return Carbon::parse($fecha)->isoFormat('D [de] MMMM YYYY');
        } catch (\Throwable $e) {
            return (string) $fecha;
        }
    }

    private function formatearHora(?string $hora): string
    {
        if (!$hora) {
            return '';
        }

        try {
            return Carbon::parse($hora)->format('H:i');
        } catch (\Throwable $e) {
            return (string) $hora;
        }
    }

    private function buscarCorreoPersona(int $personaId): ?string
    {
        return DB::table('tbl_correo')
            ->where('FK_COD_PERSONA', $personaId)
            ->orderByDesc('COD_CORREO')
            ->value('CORREO');
    }

    private function tipoLegible(string $tipo): string
    {
        return match ($tipo) {
            'RECORDATORIO_24H' => 'Recordatorio 24 horas antes',
            'RECORDATORIO_1H'  => 'Recordatorio 1 hora antes',
            'MANUAL'           => 'Notificación manual',
            default            => 'Notificación de cita',
        };
    }

    public function citasParaRecordatorio(string $tipo): Collection
    {
        if (!Schema::hasTable('tbl_cita')) {
            return collect();
        }

        $now = now();

        if ($tipo === 'RECORDATORIO_24H') {
            $inicio = $now->copy()->addHours(23)->addMinutes(30);
            $fin    = $now->copy()->addHours(24)->addMinutes(30);
        } else {
            $inicio = $now->copy()->addMinutes(45);
            $fin    = $now->copy()->addMinutes(75);
        }

        $timestampExpr = DB::raw("TIMESTAMP(c.FEC_CITA, c.HOR_CITA)");

        $citas = DB::table('tbl_cita as c')
            ->whereBetween($timestampExpr, [$inicio, $fin])
            ->get();

        $tieneTablaNotificacion = Schema::hasTable('tbl_notificacion');

        $citasSinNotificar = $citas->filter(function ($cita) use ($tipo, $tieneTablaNotificacion) {
            if (!$tieneTablaNotificacion) {
                return true;
            }

            return !DB::table('tbl_notificacion')
                ->where('FK_COD_CITA', $cita->COD_CITA)
                ->where('TIPO_NOTIFICACION', $tipo)
                ->exists();
        });

        return $citasSinNotificar->values();
    }
}
