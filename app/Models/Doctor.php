<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'tbl_doctor';
    protected $primaryKey = 'COD_DOCTOR';
    public $timestamps = false;

    // Suponiendo esta FK (ajústala si tu tabla usa otro nombre)
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'FK_COD_PERSONA', 'COD_PERSONA');
    }
}
