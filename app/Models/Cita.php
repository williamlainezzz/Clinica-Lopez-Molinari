<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $hora = $this->HORA_CITA;
        if ($this->HORA_CITA instanceof \DateTimeInterface) {
            $hora = $this->HORA_CITA->format('H:i');
        }

        return substr((string) $hora, 0, 5);
    }
}
