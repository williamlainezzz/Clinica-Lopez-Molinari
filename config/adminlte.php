<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */

    'title' => 'Complejo Dental Lopez Molinari',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    */

    'logo' => '<span class="brand-text"><b>Complejo Dental</b><br>López Molinari</span>',

    'logo_img' => 'images/logo_clinica.avif',
    'logo_img_class' => 'brand-image elevation-0',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Clínica Dental',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'images/logo_clinica.avif',
            'alt' => 'Complejo Dental López Molinari',
            'class' => 'object-contain',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'images/logo_clinica.avif',
            'alt' => 'Cargando Complejo Dental López Molinari',
            'effect' => 'animation__shake',
            'width' => 64,
            'height' => 64,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    */

    'classes_body' => '',
    'classes_brand' => 'text-wrap',
    'classes_brand_text' => 'brand-text font-weight-bold',
    'classes_content_wrapper' => 'clinica-content-wrapper',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4 clinica-sidebar',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-dark',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container-fluid',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    */

    'menu' => [

        // Panel
        [
            'text' => 'Panel principal',
            'route' => 'dashboard',
            'icon'  => 'fas fa-home',
        ],

        // Módulo de citas y agenda
        [
            'header' => 'Agenda y citas',
        ],
        [
            'text'  => 'Citas y agenda',
            'icon'  => 'fas fa-calendar-check',
            'can'   => 'agenda.menu',
            'submenu' => [
                [
                    'text'   => 'Citas',
                    'route'  => 'agenda.citas',
                    'icon'   => 'fas fa-dot-circle',
                    'active' => ['agenda/citas*'],
                    'can'    => 'agenda.citas.ver',
                ],
                [
                    'text'   => 'Agenda',
                    'route'  => 'agenda.calendario',
                    'icon'   => 'fas fa-dot-circle',
                    'active' => ['agenda/calendario*'],
                    'can'    => 'agenda.calendario.ver',
                ],
                [
                    'text'   => 'Historial',
                    'route'  => 'agenda.reportes',
                    'icon'   => 'fas fa-dot-circle',
                    'active' => ['agenda/reportes*'],
                    'can'    => 'agenda.reportes.ver',
                ],
            ],
        ],

        // PERSONAS & USUARIOS
        [
            'header' => 'Gestión de personas',
        ],
        [
            'text' => 'Personas y usuarios',
            'icon' => 'fas fa-users',
            'can'  => 'personas.menu',
            'submenu' => [
                [
                    'text'   => 'Doctores',
                    'route'  => 'doctores.index',
                    'icon'   => 'fas fa-user-md',
                    'can'    => 'personas.doctores.ver',
                    'active' => ['personas/doctores*'],
                ],
                [
                    'text'   => 'Pacientes',
                    'route'  => 'pacientes.index',
                    'icon'   => 'fas fa-user-injured',
                    'can'    => 'personas.pacientes.ver',
                    'active' => ['personas/pacientes*'],
                ],
                [
                    'text'   => 'Recepcionistas',
                    'route'  => 'recepcionistas.index',
                    'icon'   => 'fas fa-clipboard-user',
                    'can'    => 'personas.recepcionistas.ver',
                    'active' => ['personas/recepcionistas*'],
                ],
                [
                    'text'   => 'Administradores',
                    'route'  => 'administradores.index',
                    'icon'   => 'fas fa-user-shield',
                    'can'    => 'personas.administradores.ver',
                    'active' => ['personas/administradores*'],
                ],
            ],
        ],

        // SEGURIDAD
        [
            'header' => 'Seguridad',
        ],
        [
            'text'    => 'Controles de seguridad',
            'icon'    => 'fas fa-shield-alt',
            'can'     => 'seguridad.menu',
            'submenu' => [
                [
                    'text'  => 'Objetos',
                    'route' => 'seguridad.objetos.index',
                    'icon'  => 'fas fa-dot-circle',
                    'can'   => 'seguridad.objetos.ver',
                ],
                [
                    'text'  => 'Permisos',
                    'route' => 'seguridad.permisos.index',
                    'icon'  => 'fas fa-dot-circle',
                    'can'   => 'seguridad.permisos.ver',
                ],
                [
                    'text'  => 'Usuarios',
                    'route' => 'usuarios.index',
                    'icon'  => 'fas fa-dot-circle',
                    'can'   => 'seguridad.usuarios.ver',
                ],
                [
                    'text'  => 'Roles',
                    'route' => 'seguridad.roles.index',
                    'icon'  => 'fas fa-dot-circle',
                    'can'   => 'seguridad.roles.ver',
                ],
                [
                    'text'  => 'Bitácora',
                    'route' => 'seguridad.bitacora.index',
                    'icon'  => 'fas fa-dot-circle',
                    'can'   => 'seguridad.bitacora.ver',
                ],
                [
                    'text'  => 'Backups',
                    'route' => 'seguridad.backups.index',
                    'icon'  => 'fas fa-dot-circle',
                    'can'   => 'seguridad.backups.ver',
                ],
            ],
        ],

        // REPORTES
        [
            'header' => 'Reportes y analíticas',
        ],
        [
            'text'  => 'Reportes',
            'icon'  => 'fas fa-chart-bar',
            'submenu' => [
                [
                    'text'  => 'Tipos de reporte',
                    'icon'  => 'fas fa-folder-open',
                    'submenu' => [
                        ['text' => 'Citas por rango de fechas',  'route' => 'reportes.citas_rango',       'icon' => 'fas fa-dot-circle'],
                        ['text' => 'Citas por estado',           'route' => 'reportes.citas_estado',      'icon' => 'fas fa-dot-circle'],
                        ['text' => 'Agenda por doctor',          'route' => 'reportes.agenda_doctor',     'icon' => 'fas fa-dot-circle'],
                        ['text' => 'Pacientes activos/inactivos','route' => 'reportes.pacientes_estado',  'icon' => 'fas fa-dot-circle'],
                        ['text' => 'Usuarios por rol',           'route' => 'reportes.usuarios_rol',      'icon' => 'fas fa-dot-circle'],
                        ['text' => 'Citas no atendidas/ausencia','route' => 'reportes.citas_no_atendidas','icon' => 'fas fa-dot-circle'],
                    ],
                ],
                [
                    'text'  => 'Procesos',
                    'route' => 'reportes.procesos',
                    'icon'  => 'fas fa-dot-circle',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    */

    'plugins' => [
        'ClinicaTheme' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'css/clinica-theme.css',
                ],
            ],
        ],
    ],

    'iframe' => [
        // ... (igual que lo tenías)
    ],

    'livewire' => false,
];
