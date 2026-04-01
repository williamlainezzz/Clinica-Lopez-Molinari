@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<style>
    body.brand-theme-body {
        background:
            radial-gradient(circle at top left, rgba(29, 78, 216, 0.10), transparent 24%),
            radial-gradient(circle at bottom right, rgba(96, 165, 250, 0.12), transparent 30%),
            linear-gradient(180deg, #f7faff 0%, #eef4ff 45%, #f8fbff 100%);
        color: #1f2937;
    }

    .brand-theme-topnav {
        background: rgba(255, 255, 255, 0.88) !important;
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(191, 219, 254, 0.7);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .brand-theme-topnav .nav-link,
    .brand-theme-topnav .navbar-nav .nav-link {
        color: #1e3a8a !important;
    }

    .brand-theme-sidebar {
        background: linear-gradient(180deg, #0f172a 0%, #132144 45%, #1d4ed8 100%) !important;
    }

    .brand-theme-link {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.05);
    }

    .brand-theme-link .brand-image {
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.9);
        padding: 0.15rem;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.08);
    }

    .brand-theme-text {
        color: #ffffff !important;
        font-weight: 700 !important;
        letter-spacing: 0.01em;
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link {
        width: calc(100% - 0.65rem);
        border-radius: 0.95rem;
        margin: 0.18rem 0.3rem;
        padding-right: 0.7rem;
        color: rgba(255, 255, 255, 0.86);
    }

    .main-sidebar .nav-treeview > .nav-item > .nav-link {
        width: calc(100% - 0.9rem);
        border-radius: 0.85rem;
        margin: 0.12rem 0.4rem 0.12rem 0.5rem;
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link.active,
    .main-sidebar .nav-sidebar > .nav-item > .nav-link:hover,
    .main-sidebar .nav-treeview > .nav-item > .nav-link:hover {
        background: rgba(255, 255, 255, 0.14) !important;
        color: #ffffff !important;
    }

    .content-wrapper,
    .main-footer {
        background: transparent !important;
    }

    .content-header h1,
    .content-wrapper .card-title,
    .content-wrapper h1,
    .content-wrapper h2,
    .content-wrapper h3 {
        color: #0f172a;
    }

    .content-wrapper .card,
    .modal-content,
    .small-box,
    .info-box {
        border: 1px solid rgba(191, 219, 254, 0.65);
        border-radius: 1.1rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .content-wrapper .card-header {
        border-bottom-color: rgba(191, 219, 254, 0.7);
        background: linear-gradient(180deg, rgba(248, 250, 255, 0.95) 0%, rgba(239, 246, 255, 0.9) 100%);
    }

    .card.card-outline.card-primary {
        border-top: 3px solid #2563eb;
    }

    .btn-primary,
    .page-item.active .page-link {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
    }

    .btn-primary:hover,
    .page-link:hover {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
    }

    .btn-outline-primary {
        color: #2563eb !important;
        border-color: rgba(37, 99, 235, 0.35) !important;
    }

    .btn-outline-primary:hover {
        color: #ffffff !important;
        background-color: #2563eb !important;
        border-color: #2563eb !important;
    }

    .form-control:focus,
    .custom-select:focus {
        border-color: rgba(37, 99, 235, 0.5);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
    }

    .main-footer {
        border-top: 1px solid rgba(191, 219, 254, 0.7);
        color: #64748b;
    }
</style>

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    <ul class="navbar-nav">
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')
        @yield('content_top_nav_left')
    </ul>

    <ul class="navbar-nav ml-auto">
        @if(Auth::user())
            @include('layouts.partials.help-center-button')
        @endif

        @yield('content_top_nav_right')
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>

@if(Auth::user())
    @include('layouts.partials.help-center-modal')
@endif
