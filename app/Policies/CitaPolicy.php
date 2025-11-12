<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\Usuario;

class CitaPolicy
{
    /** Listar index */
    public function viewAny(Usuario $user): bool
    {
        // Si usas permisos por objetos
        if ($user->tienePermiso('CITAS.VER')) return true;

        // Roles que también pueden entrar a la lista (filtrada por controller)
        if ($user->esRol(['ADMIN','RECEPCIONISTA','DOCTOR','PACIENTE'])) return true;

        return false;
    }

    /** Ver detalle */
    public function view(Usuario $user, Cita $cita): bool
    {
        if ($user->esRol(['ADMIN','RECEPCIONISTA'])) return true;
        if ($user->esRol('DOCTOR')   && $user->doctorId()   === (int)$cita->FK_COD_DOCTOR)   return true;
        if ($user->esRol('PACIENTE') && $user->pacienteId() === (int)$cita->FK_COD_PACIENTE) return true;

        return false;
    }

    /** Crear */
    public function create(Usuario $user): bool
    {
        return $user->tienePermiso(['CITAS.CREAR','CITAS.CALENDARIO'])
            || $user->esRol(['ADMIN','RECEPCIONISTA','DOCTOR']);
    }

    /** Actualizar */
    public function update(Usuario $user, Cita $cita): bool
    {
        if ($user->esRol(['ADMIN','RECEPCIONISTA'])) return true;
        if ($user->esRol('DOCTOR') && $user->doctorId() === (int)$cita->FK_COD_DOCTOR) return true;

        return false;
    }

    /** Borrar */
    public function delete(Usuario $user, Cita $cita): bool
    {
        if ($user->esRol(['ADMIN','RECEPCIONISTA'])) return true;
        if ($user->esRol('DOCTOR') && $user->doctorId() === (int)$cita->FK_COD_DOCTOR) return true;

        return false;
    }

    /** Cambiar estado (útil para agenda) */
    public function changeStatus(Usuario $user, Cita $cita): bool
    {
        if ($user->esRol(['ADMIN','RECEPCIONISTA'])) return true;
        if ($user->esRol('DOCTOR')   && $user->doctorId()   === (int)$cita->FK_COD_DOCTOR)   return true;
        if ($user->esRol('PACIENTE') && $user->pacienteId() === (int)$cita->FK_COD_PACIENTE) return true;

        return false;
    }
}
