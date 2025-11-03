<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'tbl_cita';
    protected $primaryKey = 'COD_CITA';
    public $timestamps = false;

    protected $fillable = [
        'FK_COD_PACIENTE',
        'FK_COD_DOCTOR',
        'FEC_CITA',
        'HOR_CITA',
        'MOT_CITA',
        'ESTADO_CITA',
    ];

    // relaciones reales
    public function paciente()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_PACIENTE', 'COD_PERSONA');
    }

    public function doctor()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_DOCTOR', 'COD_PERSONA');
    }

    // si tus estados son numéricos en la misma tbl_cita:
    public const ESTADOS = [
        1 => 'Confirmada',
        2 => 'Pendiente',
        3 => 'Cancelada',
    ];

    public function getEstadoNombreAttribute(): string
    {
        return self::ESTADOS[$this->ESTADO_CITA] ?? '—';
    }

    /* Scopes usando columnas reales */
    public function scopeEntreFechas($q, $desde = null, $hasta = null)
    {
        if ($desde) $q->where('FEC_CITA', '>=', $desde);
        if ($hasta) $q->where('FEC_CITA', '<=', $hasta);
        return $q;
    }

    public function scopePorEstado($q, $estadoNombre = null)
    {
        if (!$estadoNombre) return $q;
        $id = array_search($estadoNombre, self::ESTADOS, true);
        if ($id !== false) $q->where('ESTADO_CITA', $id);
        return $q;
    }

    // si filtras por DOCTOR (por id de persona):
    public function scopePorDoctorId($q, $doctorId = null)
    {
        if ($doctorId) $q->where('FK_COD_DOCTOR', $doctorId);
        return $q;
    }
}
