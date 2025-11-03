<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    // AJUSTA a tu tabla real
    protected $table = 'TBL_CITA';
    protected $primaryKey = 'ID_CITA';
    public $timestamps = false;

    protected $fillable = [
        // ajusta a tus columnas reales
        'ID_CITA',
        'ID_PACIENTE',
        'ID_DOCTOR',
        'ID_ESTADO',
        'FECHA',      // YYYY-MM-DD
        'HORA',       // HH:MM
        'MOTIVO',
    ];

    // Relaciones (ajusta FKs a tus nombres)
    public function paciente()
    {
        return $this->belongsTo(Persona::class, 'ID_PACIENTE', 'ID_PERSONA');
    }

    public function doctor()
    {
        return $this->belongsTo(Persona::class, 'ID_DOCTOR', 'ID_PERSONA');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoCita::class, 'ID_ESTADO', 'ID_ESTADO');
    }

    /* Scopes de filtrado */
    public function scopeEntreFechas($q, $desde = null, $hasta = null)
    {
        if ($desde) $q->where('FECHA', '>=', $desde);
        if ($hasta) $q->where('FECHA', '<=', $hasta);
        return $q;
    }

    public function scopePorEstado($q, $estadoNombre = null)
    {
        if (!$estadoNombre) return $q;
        // si tienes tabla catálogo: join o subquery; aquí un ejemplo simple
        return $q->whereHas('estado', function($qq) use ($estadoNombre) {
            $qq->where('NOMBRE', $estadoNombre); // ajusta columna real (ej.: NOM_ESTADO)
        });
    }

    public function scopePorDoctorNombre($q, $doctorNombre = null)
    {
        if (!$doctorNombre) return $q;
        return $q->whereHas('doctor', function($qq) use ($doctorNombre) {
            $qq->whereRaw('CONCAT(NOMBRE," ",APELLIDO) = ?', [$doctorNombre]); // ajusta a tus columnas
        });
    }
}
