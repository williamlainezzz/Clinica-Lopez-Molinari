<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'tbl_persona';
    protected $primaryKey = 'COD_PERSONA';

    // Tu tabla no maneja created_at / updated_at
    public $timestamps = false;

    // Clave primaria autoincremental entera (ajusta si no aplica)
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'PRIMER_NOMBRE',
        'SEGUNDO_NOMBRE',
        'PRIMER_APELLIDO',
        'SEGUNDO_APELLIDO',
        'TIPO_GENERO',
        // agrega aquí cualquier otro campo real de tbl_persona…
    ];

    // Para exponer "nombre_completo" al serializar
    protected $appends = ['nombre_completo'];

    // Relaciones
    public function telefonos()
    {
        return $this->hasMany(Telefono::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }

    public function correos()
    {
        return $this->hasMany(Correo::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }

    // Accesor: $persona->nombre_completo
    public function getNombreCompletoAttribute()
    {
        $segundoNombre = $this->SEGUNDO_NOMBRE ? (' ' . $this->SEGUNDO_NOMBRE) : '';
        return trim($this->PRIMER_NOMBRE . $segundoNombre . ' ' . $this->PRIMER_APELLIDO);
    }
}
