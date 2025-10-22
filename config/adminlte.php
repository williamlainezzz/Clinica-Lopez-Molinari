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

    'logo' => '<span style="white-space:normal;line-height:1.1;font-size:0.95rem;display:inline-block">
  <b>Complejo Dental</b><br>López Molinari
</span>',

    'logo_img' => 'images/logo_clinica.avif',
    'logo_img_class' => 'brand-image img-circle elevation-3',
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
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
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
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
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
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

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
            'text' => 'Welcome',
            'route' => 'dashboard',
            'icon'  => 'fas fa-tachometer-alt',
        ],
        
        //Modulo de citas botones
        [
            'header' => '',
        ],
                ['text' => 'Citas',           'route' => 'Modulo_Citas.citas.index',          'icon' => 'far fa-circle'],
                ['text' => 'Disponibilidad',  'route' => 'Modulo_Citas.disponibilidad.index', 'icon' => 'far fa-circle'],
                // ['text' => 'Estados de cita', 'route' => 'estado-cita.index',    'icon' => 'far fa-circle'],
           
      

        // PERSONAS & USUARIOS
        [
            'header' => '',
        ],
        [
            'text' => 'Personas & Usuarios',
            'icon' => 'fas fa-users',
            'submenu' => [
                ['text' => 'Doctores',        'route' => 'doctores.index',        'icon' => 'far fa-circle'],
                ['text' => 'Pacientes',       'route' => 'pacientes.index',       'icon' => 'far fa-circle'],
                ['text' => 'Recepcionistas',  'route' => 'recepcionistas.index',  'icon' => 'far fa-circle'],
                ['text' => 'Administradores', 'route' => 'administradores.index', 'icon' => 'far fa-circle'],
            ],
        ],

        // SEGURIDAD
        ['header' => ''],
        [
            'text'    => 'Seguridad',
            'icon'    => 'fas fa-shield-alt',
            'can'     => 'seguridad.menu', // ← controla visibilidad del grupo completo
            'submenu' => [

                [
                    'text'  => 'Objetos',
                    'route' => 'seguridad.objetos.index',
                    'icon'  => 'far fa-circle',
                    'can'   => 'seguridad.objetos.ver',
                ],
                [
                    'text'  => 'Permisos',
                    'route' => 'seguridad.permisos.index',
                    'icon'  => 'far fa-circle',
                    'can'   => 'seguridad.permisos.ver',
                ],
                [
                    'text'  => 'Usuarios',
                    'route' => 'usuarios.index', // alias a seguridad.usuarios.index
                    'icon'  => 'far fa-circle',
                    'can'   => 'seguridad.usuarios.ver', // si decides controlar Usuarios también
                ],
                [
                    'text'  => 'Roles',
                    'route' => 'seguridad.roles.index',
                    'icon'  => 'far fa-circle',
                    'can'   => 'seguridad.roles.ver',
                ],
                [
                    'text'  => 'Bitácora',
                    'route' => 'seguridad.bitacora.index',
                    'icon'  => 'far fa-circle',
                    'can'   => 'seguridad.bitacora.ver',
                ],
                [
                    'text'  => 'Backups',
                    'route' => 'seguridad.backups.index',
                    'icon'  => 'far fa-circle',
                    'can'   => 'seguridad.backups.ver',
                ],
            ],
        ],

        // REPORTES
        ['header' => ''],
        [
            'text'  => 'Reportes',
            'icon'  => 'fas fa-chart-bar',
            'submenu' => [

                // Subbotón 1: contiene las 6 opciones
                [
                    'text'  => 'Tipos de reporte',
                    'icon'  => 'far fa-folder-open',
                    'submenu' => [
                        ['text' => 'Citas por rango de fechas',  'route' => 'reportes.citas_rango',       'icon' => 'far fa-dot-circle'],
                        ['text' => 'Citas por estado',           'route' => 'reportes.citas_estado',      'icon' => 'far fa-dot-circle'],
                        ['text' => 'Agenda por doctor',          'route' => 'reportes.agenda_doctor',     'icon' => 'far fa-dot-circle'],
                        ['text' => 'Pacientes activos/inactivos','route' => 'reportes.pacientes_estado',  'icon' => 'far fa-dot-circle'],
                        ['text' => 'Usuarios por rol',           'route' => 'reportes.usuarios_rol',      'icon' => 'far fa-dot-circle'],
                        ['text' => 'Citas no atendidas/ausencia','route' => 'reportes.citas_no_atendidas','icon' => 'far fa-dot-circle'],
                    ],
                ],

                // Subbotón 2: Procesos (aparte, sin submenu)
                [
                    'text'  => 'Procesos',
                    'route' => 'reportes.procesos',
                    'icon'  => 'far fa-circle',
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
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    */

    'livewire' => false,
];
