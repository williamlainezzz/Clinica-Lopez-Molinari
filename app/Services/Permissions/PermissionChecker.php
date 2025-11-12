<?php

namespace App\Services\Permissions;

use Illuminate\Support\Facades\DB;

class PermissionChecker
{
    private int $adminRoleId;

    public function __construct(int $adminRoleId = 1)
    {
        $this->adminRoleId = $adminRoleId;
    }

    public function isAdmin($user): bool
    {
        $roleId = (int)($user->FK_COD_ROL ?? 0);

        if ($roleId === $this->adminRoleId) {
            return true;
        }

        if ($roleId <= 0) {
            return false;
        }

        $roleName = $this->resolveRoleName($roleId);

        return $roleName && strtoupper(trim($roleName)) === 'ADMIN';
    }

    public function roleIs($user, array $roles): bool
    {
        $roleId = (int)($user->FK_COD_ROL ?? 0);

        if ($roleId <= 0 || $roles === []) {
            return false;
        }

        $roleName = $this->resolveRoleName($roleId);

        if (!$roleName) {
            return false;
        }

        $targets = array_map(static fn ($role) => strtoupper(trim((string) $role)), $roles);

        return in_array(strtoupper(trim($roleName)), $targets, true);
    }

    public function hasPermission($user, string $object, string $action = 'VER'): bool
    {
        $action = strtoupper($action);

        if (function_exists('puede')) {
            return puede($object, $action);
        }

        $roleId = (int)($user->FK_COD_ROL ?? 0);

        if ($roleId <= 0) {
            return false;
        }

        $row = DB::selectOne(
            'SELECT fn_tiene_permiso(?, ?, ?) AS ok',
            [$roleId, $object, $action]
        );

        return $row && (int)($row->ok ?? 0) === 1;
    }

    private function resolveRoleName(int $roleId): ?string
    {
        return DB::table('tbl_rol')
            ->where('COD_ROL', $roleId)
            ->value('NOM_ROL');
    }
}
