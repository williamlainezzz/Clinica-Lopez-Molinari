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
        margin: 0.04rem 0.3rem;
        padding-top: 0.52rem;
        padding-bottom: 0.52rem;
        padding-right: 0.7rem;
        color: rgba(255, 255, 255, 0.86);
    }

    .main-sidebar .nav-treeview > .nav-item > .nav-link {
        width: calc(100% - 0.9rem);
        border-radius: 0.85rem;
        margin: 0.08rem 0.4rem 0.08rem 0.5rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link.active,
    .main-sidebar .nav-sidebar > .nav-item > .nav-link:hover,
    .main-sidebar .nav-treeview > .nav-item > .nav-link:hover {
        background: rgba(255, 255, 255, 0.14) !important;
        color: #ffffff !important;
    }

    .brand-sidebar-divider {
        height: 1px;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.14));
        margin: 0.35rem 0 0.85rem;
    }

    .brand-user-shortcut {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.9rem 1rem;
        border-radius: 1rem;
        text-decoration: none !important;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
        transition: background-color 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
    }

    .brand-user-shortcut:hover,
    .brand-user-shortcut.is-active {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(191, 219, 254, 0.45);
        transform: translateY(-1px);
    }

    .brand-user-shortcut__icon {
        width: 2.65rem;
        height: 2.65rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.55rem;
        color: #ffffff;
        background: linear-gradient(180deg, rgba(96, 165, 250, 0.65), rgba(37, 99, 235, 0.9));
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.22);
        flex-shrink: 0;
    }

    .brand-user-shortcut__content {
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .brand-user-shortcut__label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: rgba(191, 219, 254, 0.86);
        margin-bottom: 0.15rem;
    }

    .brand-user-shortcut__name {
        color: #ffffff;
        font-weight: 700;
        line-height: 1.2;
        white-space: normal;
        word-break: break-word;
    }

    .sidebar-mini.sidebar-collapse .brand-sidebar-divider {
        display: none;
    }

    .sidebar-mini.sidebar-collapse .main-sidebar .sidebar > .px-3.pt-3.pb-2 {
        padding: 0.85rem 0.35rem 0.55rem !important;
    }

    .sidebar-mini.sidebar-collapse .brand-user-shortcut {
        width: 2.9rem;
        height: 2.9rem;
        margin: 0 auto;
        padding: 0;
        gap: 0;
        border-radius: 999px;
        justify-content: center;
        background: transparent;
        border-color: transparent;
        box-shadow: none;
        transform: none;
    }

    .sidebar-mini.sidebar-collapse .brand-user-shortcut:hover,
    .sidebar-mini.sidebar-collapse .brand-user-shortcut.is-active {
        background: rgba(59, 130, 246, 0.22);
        border-color: rgba(147, 197, 253, 0.24);
        box-shadow: none;
        transform: none;
    }

    .sidebar-mini.sidebar-collapse .brand-user-shortcut__content {
        display: none;
    }

    .sidebar-mini.sidebar-collapse .brand-user-shortcut__icon {
        width: 2.3rem;
        height: 2.3rem;
        margin: 0;
        font-size: 1.35rem;
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

    .brand-navbar-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        white-space: nowrap;
        font-weight: 600;
    }

    .brand-logout-toggle {
        font-weight: 600;
    }

    .brand-logout-menu {
        min-width: 15rem;
        border: 1px solid rgba(191, 219, 254, 0.85);
        border-radius: 1rem;
        padding: 0.9rem;
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
    }

    .brand-logout-menu__title {
        font-size: 0.98rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.35rem;
    }

    .brand-logout-menu__copy {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 0.9rem;
    }

    .brand-logout-actions {
        display: flex;
        gap: 0.55rem;
    }

    .brand-logout-actions .btn {
        flex: 1 1 0;
        border-radius: 0.75rem;
        font-weight: 600;
    }

    @media (max-width: 575.98px) {
        .brand-navbar-btn {
            min-height: 2rem;
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }

        .brand-logout-menu {
            min-width: 13.75rem;
            padding: 0.8rem;
        }

        .brand-logout-menu__title {
            font-size: 0.92rem;
        }

        .brand-logout-menu__copy {
            font-size: 0.84rem;
        }
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
        @yield('content_top_nav_right')
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        @if(Auth::user())
            @include('layouts.partials.help-center-button')
            @include('layouts.partials.logout-button')
        @endif

        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>

@if(Auth::user())
    @include('layouts.partials.help-center-modal')
@endif
