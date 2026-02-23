<?php

namespace App\Support;

use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PasswordSecurityService
{
    public function passwordMetadataColumnsExist(): bool
    {
        return Schema::hasTable('tbl_usuario')
            && Schema::hasColumn('tbl_usuario', 'PWD_ACTUALIZADA_EN')
            && Schema::hasColumn('tbl_usuario', 'PWD_RECORDATORIO_ENVIADO_EN');
    }

    public function temporaryPasswordColumnExists(): bool
    {
        return Schema::hasTable('tbl_usuario')
            && Schema::hasColumn('tbl_usuario', 'FORZAR_CAMBIO_PWD');
    }

    public function shouldForcePasswordChange(Usuario $usuario): bool
    {
        if (!$this->temporaryPasswordColumnExists()) {
            return false;
        }

        return (bool) ($usuario->FORZAR_CAMBIO_PWD ?? false);
    }

    public function shouldEnforceExpiry(Usuario $usuario): bool
    {
        if (!$this->passwordMetadataColumnsExist()) {
            return false;
        }

        $lastUpdated = $usuario->PWD_ACTUALIZADA_EN;

        if (empty($lastUpdated)) {
            return false;
        }

        return now()->greaterThanOrEqualTo(
            Carbon::parse($lastUpdated)->addDays((int) config('security.password_expiry_days', 60))
        );
    }

    public function shouldSendReminder(Usuario $usuario): bool
    {
        if (!$this->passwordMetadataColumnsExist()) {
            return false;
        }

        $lastUpdated = $usuario->PWD_ACTUALIZADA_EN;

        if (empty($lastUpdated)) {
            return false;
        }

        $expiresAt = Carbon::parse($lastUpdated)->addDays((int) config('security.password_expiry_days', 60));
        $reminderAt = $expiresAt->copy()->subDays((int) config('security.password_reminder_days', 15));

        if (now()->lt($reminderAt)) {
            return false;
        }

        return empty($usuario->PWD_RECORDATORIO_ENVIADO_EN);
    }

    public function markPasswordChanged(int $userId): void
    {
        if (!$this->passwordMetadataColumnsExist() && !$this->temporaryPasswordColumnExists()) {
            return;
        }

        $data = [];

        if ($this->passwordMetadataColumnsExist()) {
            $data['PWD_ACTUALIZADA_EN'] = now();
            $data['PWD_RECORDATORIO_ENVIADO_EN'] = null;
        }

        if ($this->temporaryPasswordColumnExists()) {
            $data['FORZAR_CAMBIO_PWD'] = 0;
        }

        DB::table('tbl_usuario')
            ->where('COD_USUARIO', $userId)
            ->update($data);
    }

    public function markTemporaryPassword(int $userId): void
    {
        if (!$this->temporaryPasswordColumnExists()) {
            return;
        }

        DB::table('tbl_usuario')
            ->where('COD_USUARIO', $userId)
            ->update([
                'FORZAR_CAMBIO_PWD' => 1,
            ]);
    }

    public function markReminderSent(int $userId): void
    {
        if (!$this->passwordMetadataColumnsExist()) {
            return;
        }

        DB::table('tbl_usuario')
            ->where('COD_USUARIO', $userId)
            ->update([
                'PWD_RECORDATORIO_ENVIADO_EN' => now(),
            ]);
    }
}
