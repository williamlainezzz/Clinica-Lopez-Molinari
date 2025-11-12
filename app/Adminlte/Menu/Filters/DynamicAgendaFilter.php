<?php

namespace App\Adminlte\Menu\Filters;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use JeroenNoten\LaravelAdminLte\Menu\MenuBuilder;

class DynamicAgendaFilter implements FilterInterface
{
    private string $guard;

    public function __construct(private readonly AuthFactory $auth)
    {
        $this->guard = config('adminlte.guard') ?? config('auth.defaults.guard', 'web');
    }

    /**
     * AdminLTE v3+: firma con MenuBuilder
     * Debe devolver array (ítem transformado) o false para ocultarlo.
     */
    public function transform($item, MenuBuilder $builder)
    {
        if (!isset($item['key'])) {
            return $item;
        }

        // Respeta el guard configurado
        $user = $this->auth->guard($this->guard)->user();
        if (!$user) {
            return $this->applyFallback($item);
        }

        $role = strtoupper(optional($user->rol)->NOM_ROL ?? '');

        return match ($item['key']) {
            'agenda-menu'        => $this->transformMenuRoot($item, $role),
            'agenda.citas'       => $this->transformCitasItem($item, $role),
            'agenda.calendario'  => $this->transformCalendarioItem($item, $role),
            'agenda.reportes'    => $this->transformReportesItem($item, $role),
            default              => $item,
        };
    }

    private function transformMenuRoot(array $item, string $role): array
    {
        $item['text'] = ($role === 'DOCTOR') ? 'Mis pacientes' : 'Citas';
        return $item;
    }

    /** @return array|false */
    private function transformCitasItem(array $item, string $role)
    {
        $labels = [
            'ADMIN'         => 'Ver citas',
            'RECEPCIONISTA' => 'Ver citas',
            'DOCTOR'        => 'Mis pacientes',
            'PACIENTE'      => 'Mis citas',
        ];

        $item['text'] = $labels[$role] ?? 'Citas';
        return $item;
    }

    /** @return array|false */
    private function transformCalendarioItem(array $item, string $role)
    {
        if ($role === 'PACIENTE') {
            // Oculta el calendario a pacientes
            return false;
        }

        $item['text'] = 'Agenda';
        return $item;
    }

    /** @return array|false */
    private function transformReportesItem(array $item, string $role)
    {
        return match ($role) {
            'ADMIN'    => $item,                                            // visible
            'PACIENTE' => $this->applyText($item, 'Historial de citas'),    // renombrado
            default    => false,                                            // oculto para otros
        };
    }

    private function applyText(array $item, string $text): array
    {
        $item['text'] = $text;
        return $item;
    }

    private function applyFallback(array $item): array
    {
        if (($item['key'] ?? null) === 'agenda-menu') {
            $item['text'] = 'Citas';
        }

        if (($item['key'] ?? null) === 'agenda.calendario') {
            $item['text'] = 'Agenda';
        }

        return $item;
    }
}
