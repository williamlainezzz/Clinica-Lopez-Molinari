<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Usuario;
use App\Notifications\CitaNotificacion;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

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

    public function enviarNotificacionCambioEstadoCita(int $codCita, string $estadoNuevo): void
    {
        $cita = $this->buscarCita($codCita);

        if (!$cita) {
            return;
        }

        $estadoLegible = $this->nombreEstadoLegible($estadoNuevo);
        $fecha = $this->formatearFecha($cita->FEC_CITA ?? null);
        $hora = $this->formatearHora($cita->HOR_CITA ?? null);
        $clinica = config('app.name', 'Clinica');

        $this->enviarNotificacionPersonalizada(
            $cita,
            [
                'subject'      => "{$clinica} - Cita {$estadoLegible}",
                'titulo'       => "Cita {$estadoLegible}",
                'tipo'         => 'MANUAL',
                'tipo_legible' => "Cambio de estado a {$estadoLegible}",
                'mensaje'      => "Su cita con {$cita->doctor_nombre} ha sido marcada como {$estadoLegible} para el {$fecha} a las {$hora}.",
            ],
            [
                'subject'      => "{$clinica} - Cita del paciente {$estadoLegible}",
                'titulo'       => "Cita {$estadoLegible}",
                'tipo'         => 'MANUAL',
                'tipo_legible' => "Cambio de estado a {$estadoLegible}",
                'mensaje'      => "La cita con {$cita->paciente_nombre} ha sido marcada como {$estadoLegible} para el {$fecha} a las {$hora}.",
            ],
            "La cita fue actualizada a estado {$estadoLegible}.",
            'MANUAL'
        );
    }

    public function enviarNotificacionReprogramacionCita(int $codCita): void
    {
        $cita = $this->buscarCita($codCita);

        if (!$cita) {
            return;
        }

        $fecha = $this->formatearFecha($cita->FEC_CITA ?? null);
        $hora = $this->formatearHora($cita->HOR_CITA ?? null);
        $clinica = config('app.name', 'Clinica');

        $this->enviarNotificacionPersonalizada(
            $cita,
            [
                'subject'      => "{$clinica} - Cita reprogramada",
                'titulo'       => 'Cita reprogramada',
                'tipo'         => 'MANUAL',
                'tipo_legible' => 'Reprogramacion de cita',
                'mensaje'      => "Su cita con {$cita->doctor_nombre} fue reprogramada para el {$fecha} a las {$hora}.",
            ],
            [
                'subject'      => "{$clinica} - Cita reprogramada con paciente",
                'titulo'       => 'Cita reprogramada',
                'tipo'         => 'MANUAL',
                'tipo_legible' => 'Reprogramacion de cita',
                'mensaje'      => "La cita con {$cita->paciente_nombre} fue reprogramada para el {$fecha} a las {$hora}.",
            ],
            'La cita fue reprogramada correctamente.',
            'MANUAL'
        );
    }

    public function enviarNotificacionActualizacionCita(int $codCita): void
    {
        $cita = $this->buscarCita($codCita);

        if (!$cita) {
            return;
        }

        $fecha = $this->formatearFecha($cita->FEC_CITA ?? null);
        $hora = $this->formatearHora($cita->HOR_CITA ?? null);
        $clinica = config('app.name', 'Clinica');

        $this->enviarNotificacionPersonalizada(
            $cita,
            [
                'subject'      => "{$clinica} - Cita actualizada",
                'titulo'       => 'Cita actualizada',
                'tipo'         => 'MANUAL',
                'tipo_legible' => 'Actualizacion de cita',
                'mensaje'      => "Su cita con {$cita->doctor_nombre} fue actualizada para el {$fecha} a las {$hora}.",
            ],
            [
                'subject'      => "{$clinica} - Cita actualizada con paciente",
                'titulo'       => 'Cita actualizada',
                'tipo'         => 'MANUAL',
                'tipo_legible' => 'Actualizacion de cita',
                'mensaje'      => "La cita con {$cita->paciente_nombre} fue actualizada para el {$fecha} a las {$hora}.",
            ],
            'La cita fue actualizada correctamente.',
            'MANUAL'
        );
    }

    public function contarNoLeidasParaUsuario(?Authenticatable $user): int
    {
        if (
            !$user ||
            !Schema::hasTable('tbl_notificacion') ||
            !Schema::hasTable('tbl_cita')
        ) {
            return 0;
        }

        $userId = (int) ($user?->getAuthIdentifier() ?? 0);

        if ($userId <= 0) {
            return 0;
        }

        if (Schema::hasTable('tbl_notificacion_usuario')) {
            $query = $this->baseNotificacionQuery()
                ->leftJoin('tbl_notificacion_usuario as nu', function ($join) use ($userId) {
                    $join->on('nu.FK_COD_NOTIFICACION', '=', 'n.COD_NOTIFICACION')
                        ->where('nu.FK_COD_USUARIO', '=', $userId);
                })
                ->where(function ($q) {
                    $q->whereNull('nu.COD_NU')
                        ->orWhere('nu.LEIDA', 0);
                });

            $this->aplicarFiltroPorRol($query, $user);

            return (int) $query->count();
        }

        if (!Schema::hasColumn('tbl_notificacion', 'LEIDA')) {
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

    public function citasParaRecordatorio(string $tipo): Collection
    {
        if (!Schema::hasTable('tbl_cita')) {
            return collect();
        }

        $now = now();

        if ($tipo === 'RECORDATORIO_24H') {
            $inicio = $now->copy()->addHours(23)->addMinutes(30);
            $fin = $now->copy()->addHours(24)->addMinutes(30);
        } else {
            $inicio = $now->copy()->addMinutes(45);
            $fin = $now->copy()->addMinutes(75);
        }

        $timestampExpr = DB::raw("TIMESTAMP(c.FEC_CITA, c.HOR_CITA)");

        $citas = DB::table('tbl_cita as c')
            ->leftJoin('tbl_estado_cita as e', 'e.COD_ESTADO', '=', 'c.ESTADO_CITA')
            ->whereBetween($timestampExpr, [$inicio, $fin])
            ->where(function ($query) {
                $query->whereNull('e.NOM_ESTADO')
                    ->orWhereIn(DB::raw('UPPER(TRIM(e.NOM_ESTADO))'), ['PENDIENTE', 'CONFIRMADA']);
            })
            ->select(['c.COD_CITA'])
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

    private function enviarNotificacionCita(int $codCita, string $tipo): void
    {
        if (!Schema::hasTable('tbl_cita') || !Schema::hasTable('tbl_persona')) {
            return;
        }

        $cita = $this->buscarCita($codCita);

        if (!$cita) {
            return;
        }

        $payloadPaciente = $this->construirPayload($cita, $tipo, 'paciente');
        $payloadDoctor = $this->construirPayload($cita, $tipo, 'doctor');

        $this->enviarNotificacionPersonalizada(
            $cita,
            $payloadPaciente,
            $payloadDoctor,
            $payloadPaciente['mensaje'] ?? '',
            $tipo
        );
    }

    private function enviarNotificacionPersonalizada(
        object $cita,
        array $payloadPaciente,
        array $payloadDoctor,
        string $mensajeHistorial,
        string $tipo = 'MANUAL'
    ): void {
        $this->registrarNotificacion((int) $cita->COD_CITA, $mensajeHistorial, $tipo);
        $this->enviarCorreo((int) $cita->paciente_persona_id, $payloadPaciente);
        $this->enviarCorreo((int) $cita->doctor_persona_id, $payloadDoctor);
    }

    private function registrarNotificacion(int $codCita, string $mensaje, string $tipo): void
    {
        if (!Schema::hasTable('tbl_notificacion')) {
            return;
        }

        $payload = [
            'FK_COD_CITA' => $codCita,
            'MSG_NOTIFICACION' => $mensaje,
            'FEC_ENVIO' => now(),
        ];

        if (Schema::hasColumn('tbl_notificacion', 'TIPO_NOTIFICACION')) {
            $payload['TIPO_NOTIFICACION'] = $tipo;
        }

        if (Schema::hasColumn('tbl_notificacion', 'LEIDA')) {
            $payload['LEIDA'] = 0;
        }

        try {
            Notificacion::create($payload);
        } catch (\Throwable $e) {
        }
    }

    private function enviarCorreo(int $personaId, array $payload): void
    {
        if ($personaId <= 0) {
            return;
        }

        $usuario = Usuario::where('FK_COD_PERSONA', $personaId)->first();
        $correo = $this->buscarCorreoPersona($personaId);

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

    private function construirPayload(object $cita, string $tipo, string $destinatario = 'paciente'): array
    {
        $fecha = $this->formatearFecha($cita->FEC_CITA ?? null);
        $hora = $this->formatearHora($cita->HOR_CITA ?? null);
        $tipoLegible = $this->tipoLegible($tipo);
        $clinica = config('app.name', 'Clinica');

        if ($destinatario === 'doctor') {
            $mensaje = match ($tipo) {
                'RECORDATORIO_24H', 'RECORDATORIO_1H' => "Recordatorio: tiene una cita con el paciente {$cita->paciente_nombre} programada para el {$fecha} a las {$hora}.",
                'CREACION' => "Tiene una nueva cita con el paciente {$cita->paciente_nombre} programada para el {$fecha} a las {$hora}.",
                default => "Tiene una cita con el paciente {$cita->paciente_nombre} programada para el {$fecha} a las {$hora}.",
            };

            $subject = match ($tipo) {
                'CREACION' => "{$clinica} - Nueva cita con paciente",
                default => "{$clinica} - {$tipoLegible}",
            };

            $titulo = "{$tipoLegible} (para el doctor)";
        } else {
            $mensaje = match ($tipo) {
                'RECORDATORIO_24H', 'RECORDATORIO_1H' => "Recordatorio: su cita con {$cita->doctor_nombre} esta programada para el {$fecha} a las {$hora}.",
                'CREACION' => "Su cita con {$cita->doctor_nombre} esta programada para el {$fecha} a las {$hora}.",
                default => "Su cita con {$cita->doctor_nombre} esta programada para el {$fecha} a las {$hora}.",
            };

            $subject = "{$clinica} - {$tipoLegible}";
            $titulo = $tipoLegible;
        }

        return [
            'subject' => $subject,
            'titulo' => $titulo,
            'paciente' => $cita->paciente_nombre,
            'doctor' => $cita->doctor_nombre,
            'clinica' => $clinica,
            'fecha' => $fecha,
            'hora' => $hora,
            'tipo' => $tipo,
            'tipo_legible' => $tipoLegible,
            'mensaje' => $mensaje,
            'nota' => $cita->OBSERVACIONES,
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
            'CREACION' => 'Nueva cita',
            'RECORDATORIO_24H' => 'Recordatorio 24 horas antes',
            'RECORDATORIO_1H' => 'Recordatorio 1 hora antes',
            'MANUAL' => 'Notificacion de cita',
            default => 'Notificacion de cita',
        };
    }

    private function nombreEstadoLegible(string $estado): string
    {
        $estado = ucfirst(strtolower(trim($estado)));

        return str_replace('_', ' ', $estado);
    }
}
