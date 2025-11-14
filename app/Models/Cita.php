<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Cita extends Model
{
    protected $table = 'tbl_cita';
    protected $primaryKey = 'COD_CITA';

    protected $fillable = [
        'FK_COD_DOCTOR',
        'FK_COD_PACIENTE',
        'FEC_CITA',
        'HORA_CITA',
        'ESTADO_CITA',
        'MOTIVO_CITA',
        'UBICACION',
        'DURACION_MINUTOS',
        'CANAL',
        'NOTAS_CITA',
    ];

    protected $casts = [
        'FEC_CITA' => 'date',
        'HORA_CITA' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'FK_COD_DOCTOR', 'COD_PERSONA');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'FK_COD_PACIENTE', 'COD_PERSONA');
    }

    public function getHoraLabelAttribute(): string
    {
        $hora = $this->resolveHoraAttribute();

        if ($hora instanceof \DateTimeInterface) {
            return $hora->format('H:i');
        }

        if ($hora === null) {
            return '00:00';
        }

        $horaString = is_numeric($hora) ? str_pad((string) $hora, 4, '0', STR_PAD_LEFT) : trim((string) $hora);

        $formats = ['H:i:s', 'H:i'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $horaString)->format('H:i');
            } catch (\Exception $e) {
                // Intentamos el siguiente formato disponible.
            }
        }

        $digits = preg_replace('/[^0-9]/', '', $horaString);
        if (strlen($digits) >= 4) {
            return substr($digits, 0, 2) . ':' . substr($digits, 2, 2);
        }

        return substr($horaString, 0, 5) ?: '00:00';
    }

    private function resolveHoraAttribute(): mixed
    {
        $candidates = [
            'HORA_CITA', 'hora_cita',
            'HORA', 'hora',
            'HOR_CITA', 'hor_cita',
            'HORA_INICIO', 'hora_inicio',
        ];

        foreach ($candidates as $key) {
            if (array_key_exists($key, $this->attributes) && $this->attributes[$key] !== null) {
                return $this->attributes[$key];
            }
        }

        return null;
    }
}
