<?php

namespace App\Support;

use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PasswordSecurityService
{
    private function hasUsuarioColumn(string $column): bool
    {
        return Schema::hasTable('tbl_usuario')
            && Schema::hasColumn('tbl_usuario', $column);
    }

    private function passwordChangedAtColumn(): ?string
    {
        // Priorizar columna nueva
        if ($this->hasUsuarioColumn('PWD_CAMBIADA_EN')) {
            return 'PWD_CAMBIADA_EN';
        }

        // Compatibilidad legacy
        if ($this->hasUsuarioColumn('PWD_ACTUALIZADA_EN')) {
            return 'PWD_ACTUALIZADA_EN';
        }

        return null;
    }

    private function temporaryPasswordFlagColumn(): ?string
    {
        // Priorizar columna nueva
        if ($this->hasUsuarioColumn('PWD_TEMPORAL')) {
            return 'PWD_TEMPORAL';
        }

        // Compatibilidad legacy
        if ($this->hasUsuarioColumn('FORZAR_CAMBIO_PWD')) {
            return 'FORZAR_CAMBIO_PWD';
        }

        return null;
    }

    public function passwordMetadataColumnsExist(): bool
    {
        return $this->passwordChangedAtColumn() !== null
            && $this->hasUsuarioColumn('PWD_RECORDATORIO_ENVIADO_EN');
    }

    public function temporaryPasswordColumnExists(): bool
    {
        return $this->temporaryPasswordFlagColumn() !== null;
    }

    public function shouldForcePasswordChange(Usuario $usuario): bool
    {
        $flagColumn = $this->temporaryPasswordFlagColumn();

        if (!$flagColumn) {
            return false;
        }

        return (bool) ($usuario->{$flagColumn} ?? false);
    }

    public function shouldEnforceExpiry(Usuario $usuario): bool
    {
        $changedAtColumn = $this->passwordChangedAtColumn();

        if (!$changedAtColumn) {
            return false;
        }

        $lastUpdated = $usuario->{$changedAtColumn} ?? null;

        if (empty($lastUpdated)) {
            return false;
        }

        return now()->greaterThanOrEqualTo(
            Carbon::parse($lastUpdated)->addDays((int) config('security.password_expiry_days', 60))
        );
    }

    public function shouldSendReminder(Usuario $usuario): bool
    {
        if (!$this->hasUsuarioColumn('PWD_RECORDATORIO_ENVIADO_EN')) {
            return false;
        }

        $changedAtColumn = $this->passwordChangedAtColumn();

        if (!$changedAtColumn) {
            return false;
        }

        $lastUpdated = $usuario->{$changedAtColumn} ?? null;

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
        $data = [];

        // Columna nueva
        if ($this->hasUsuarioColumn('PWD_CAMBIADA_EN')) {
            $data['PWD_CAMBIADA_EN'] = now();
        }

        // Compatibilidad legacy
        if ($this->hasUsuarioColumn('PWD_ACTUALIZADA_EN')) {
            $data['PWD_ACTUALIZADA_EN'] = now();
        }

        // Columna compartida
        if ($this->hasUsuarioColumn('PWD_RECORDATORIO_ENVIADO_EN')) {
            $data['PWD_RECORDATORIO_ENVIADO_EN'] = null;
        }

        // Flag nueva
        if ($this->hasUsuarioColumn('PWD_TEMPORAL')) {
            $data['PWD_TEMPORAL'] = 0;
        }

        // Flag legacy
        if ($this->hasUsuarioColumn('FORZAR_CAMBIO_PWD')) {
            $data['FORZAR_CAMBIO_PWD'] = 0;
        }

        if (empty($data)) {
            return;
        }

        DB::table('tbl_usuario')
            ->where('COD_USUARIO', $userId)
            ->update($data);
    }

    public function markTemporaryPassword(int $userId): void
    {
        $data = [];

        if ($this->hasUsuarioColumn('PWD_TEMPORAL')) {
            $data['PWD_TEMPORAL'] = 1;
        }

        if ($this->hasUsuarioColumn('FORZAR_CAMBIO_PWD')) {
            $data['FORZAR_CAMBIO_PWD'] = 1;
        }

        if (empty($data)) {
            return;
        }

        DB::table('tbl_usuario')
            ->where('COD_USUARIO', $userId)
            ->update($data);
    }

    public function markReminderSent(int $userId): void
    {
        if (!$this->hasUsuarioColumn('PWD_RECORDATORIO_ENVIADO_EN')) {
            return;
        }

        DB::table('tbl_usuario')
            ->where('COD_USUARIO', $userId)
            ->update([
                'PWD_RECORDATORIO_ENVIADO_EN' => now(),
            ]);
    }
}