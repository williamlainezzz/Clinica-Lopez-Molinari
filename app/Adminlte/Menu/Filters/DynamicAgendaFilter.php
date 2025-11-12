<?php

namespace App\Adminlte\Menu\Filters;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class DynamicAgendaFilter implements FilterInterface
{
    public function __construct(private readonly AuthFactory $auth)
    {
    }

    public function transform($item)
    {
        if (!isset($item['key'])) {
            return $item;
        }

        $user = $this->auth->user();
        if (!$user) {
            return $this->applyFallback($item);
        }

        $role = strtoupper(optional($user->rol)->NOM_ROL ?? '');

        return match ($item['key']) {
            'agenda-menu'      => $this->transformMenuRoot($item, $role),
            'agenda.citas'     => $this->transformCitasItem($item, $role),
            'agenda.calendario'=> $this->transformCalendarioItem($item, $role),
            'agenda.reportes'  => $this->transformReportesItem($item, $role),
            default            => $item,
        };
    }

    private function transformMenuRoot(array $item, string $role): array
    {
        $item['text'] = $role === 'DOCTOR' ? 'Mis pacientes' : 'Citas';

        return $item;
    }

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

    private function transformCalendarioItem(array $item, string $role)
    {
        if ($role === 'PACIENTE') {
            return false;
        }

        $item['text'] = 'Agenda';

        return $item;
    }

    private function transformReportesItem(array $item, string $role)
    {
        return match ($role) {
            'ADMIN'    => $item,
            'PACIENTE' => $this->applyText($item, 'Historial de citas'),
            default    => false,
        };
    }

    private function applyText(array $item, string $text): array
    {
        $item['text'] = $text;

        return $item;
    }

    private function applyFallback($item)
    {
        if ($item['key'] === 'agenda-menu') {
            $item['text'] = 'Citas';
        }

        if ($item['key'] === 'agenda.calendario') {
            $item['text'] = 'Agenda';
        }

        return $item;
    }
}
