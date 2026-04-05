<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    @php
        $sidebarUser = auth()->user();
        $persona = optional($sidebarUser)->persona;
        $firstName = trim((string) optional($persona)->PRIMER_NOMBRE);
        $firstLastName = trim((string) optional($persona)->PRIMER_APELLIDO);
        $displayName = trim($firstName . ' ' . $firstLastName);
        $displayName = $displayName !== '' ? $displayName : (optional($sidebarUser)->USR_USUARIO ?? 'Mi perfil');
        $isProfileActive = request()->routeIs('usuario.*');
    @endphp

    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    <div class="sidebar">
        @if($sidebarUser)
            <div class="px-3 pt-3 pb-2">
                <div class="brand-sidebar-divider"></div>

                <a href="{{ route('usuario.perfil') }}"
                   class="brand-user-shortcut {{ $isProfileActive ? 'is-active' : '' }}">
                    <div class="brand-user-shortcut__icon">
                        <i class="fas fa-user-circle"></i>
                    </div>

                    <div class="brand-user-shortcut__content">
                        <span class="brand-user-shortcut__label">Perfil</span>
                        <span class="brand-user-shortcut__name">{{ $displayName }}</span>
                    </div>
                </a>

                <div class="brand-sidebar-divider"></div>
            </div>
        @endif

        <nav class="pt-1">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
                @endif
                @if(!config('adminlte.sidebar_nav_accordion'))
                    data-accordion="false"
                @endif>
                @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
            </ul>
        </nav>
    </div>

</aside>
