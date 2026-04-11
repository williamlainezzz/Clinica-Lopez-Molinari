<script>
    (function () {
        var storageKey = 'cdlm-theme';
        var mediaQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

        function getStoredTheme() {
            try {
                return window.localStorage.getItem(storageKey);
            } catch (error) {
                return null;
            }
        }

        function getPreferredTheme() {
            var storedTheme = getStoredTheme();

            if (storedTheme === 'dark' || storedTheme === 'light') {
                return storedTheme;
            }

            return mediaQuery && mediaQuery.matches ? 'dark' : 'light';
        }

        function updateToggleButtons(theme) {
            var isDark = theme === 'dark';

            document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
                var icon = button.querySelector('[data-theme-icon]');
                var label = button.querySelector('[data-theme-label]');

                button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                button.setAttribute('title', isDark ? 'Activar modo claro' : 'Activar modo oscuro');

                if (icon) {
                    icon.innerHTML = isDark
                        ? '<i class="fas fa-sun"></i>'
                        : '<i class="fas fa-moon"></i>';
                }

                if (label) {
                    label.textContent = isDark ? 'Modo claro' : 'Modo oscuro';
                }
            });
        }

        function applyTheme(theme) {
            var resolvedTheme = theme === 'dark' ? 'dark' : 'light';
            var root = document.documentElement;

            root.setAttribute('data-theme', resolvedTheme);
            root.style.colorScheme = resolvedTheme;

            if (document.body) {
                document.body.classList.toggle('theme-dark', resolvedTheme === 'dark');
                document.body.classList.toggle('theme-light', resolvedTheme !== 'dark');
            }

            updateToggleButtons(resolvedTheme);
        }

        window.CDLMTheme = {
            apply: function (theme, persist) {
                applyTheme(theme);

                if (persist !== false) {
                    try {
                        window.localStorage.setItem(storageKey, theme);
                    } catch (error) {
                    }
                }
            },
            current: function () {
                return document.documentElement.getAttribute('data-theme') || getPreferredTheme();
            },
            toggle: function () {
                var nextTheme = this.current() === 'dark' ? 'light' : 'dark';
                this.apply(nextTheme, true);
            },
        };

        applyTheme(getPreferredTheme());

        document.addEventListener('DOMContentLoaded', function () {
            applyTheme(getPreferredTheme());

            document.addEventListener('click', function (event) {
                var toggle = event.target.closest('[data-theme-toggle]');

                if (!toggle) {
                    return;
                }

                event.preventDefault();
                window.CDLMTheme.toggle();
            });
        });

        if (mediaQuery) {
            var syncSystemTheme = function (event) {
                if (getStoredTheme()) {
                    return;
                }

                applyTheme(event.matches ? 'dark' : 'light');
            };

            if (typeof mediaQuery.addEventListener === 'function') {
                mediaQuery.addEventListener('change', syncSystemTheme);
            } else if (typeof mediaQuery.addListener === 'function') {
                mediaQuery.addListener(syncSystemTheme);
            }
        }
    })();
</script>

<style>
    :root {
        color-scheme: light;
        --theme-body-bg:
            radial-gradient(circle at top left, rgba(29, 78, 216, 0.10), transparent 24%),
            radial-gradient(circle at bottom right, rgba(96, 165, 250, 0.12), transparent 30%),
            linear-gradient(180deg, #f7faff 0%, #eef4ff 45%, #f8fbff 100%);
        --theme-surface: rgba(255, 255, 255, 0.88);
        --theme-surface-strong: #ffffff;
        --theme-surface-soft: #f8fbff;
        --theme-border: rgba(191, 219, 254, 0.75);
        --theme-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        --theme-text: #0f172a;
        --theme-text-soft: #475569;
        --theme-link: #1e3a8a;
        --theme-link-hover: #1d4ed8;
        --theme-sidebar-bg: linear-gradient(180deg, #0f172a 0%, #132144 45%, #1d4ed8 100%);
        --theme-sidebar-surface: rgba(255, 255, 255, 0.08);
        --theme-sidebar-surface-strong: rgba(255, 255, 255, 0.14);
        --theme-sidebar-border: rgba(255, 255, 255, 0.10);
        --theme-sidebar-text: rgba(255, 255, 255, 0.86);
        --theme-sidebar-muted: rgba(191, 219, 254, 0.86);
        --theme-topnav-bg: rgba(255, 255, 255, 0.88);
        --theme-topnav-border: rgba(191, 219, 254, 0.7);
        --theme-input-bg: #ffffff;
        --theme-input-border: rgba(148, 163, 184, 0.35);
        --theme-input-text: #0f172a;
        --theme-table-stripe: rgba(59, 130, 246, 0.04);
        --theme-muted-panel: rgba(248, 250, 252, 0.92);
        --theme-toggle-bg: rgba(37, 99, 235, 0.08);
        --theme-toggle-border: rgba(37, 99, 235, 0.18);
        --theme-toggle-text: #1e3a8a;
        --theme-help-bg: rgba(37, 99, 235, 0.08);
        --theme-help-border: rgba(37, 99, 235, 0.22);
        --theme-help-text: #2563eb;
        --theme-danger-bg: rgba(220, 38, 38, 0.06);
        --theme-danger-border: rgba(239, 68, 68, 0.28);
        --theme-danger-text: #dc2626;
        --theme-table-head-bg: rgba(241, 245, 249, 0.92);
        --theme-table-head-text: #1e293b;
        --theme-table-warning-bg: rgba(254, 249, 195, 0.88);
        --theme-table-warning-text: #713f12;
    }

    html[data-theme='dark'] {
        color-scheme: dark;
        --theme-body-bg:
            radial-gradient(circle at top left, rgba(37, 99, 235, 0.18), transparent 24%),
            radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.12), transparent 30%),
            linear-gradient(180deg, #07111f 0%, #0b162b 48%, #091324 100%);
        --theme-surface: rgba(8, 15, 31, 0.86);
        --theme-surface-strong: #0f172a;
        --theme-surface-soft: #111c33;
        --theme-border: rgba(96, 165, 250, 0.18);
        --theme-shadow: 0 18px 40px rgba(2, 6, 23, 0.38);
        --theme-text: #e5eefc;
        --theme-text-soft: #9fb1cb;
        --theme-link: #c9ddff;
        --theme-link-hover: #ffffff;
        --theme-sidebar-bg: linear-gradient(180deg, #030712 0%, #081225 42%, #102348 100%);
        --theme-sidebar-surface: rgba(148, 163, 184, 0.08);
        --theme-sidebar-surface-strong: rgba(96, 165, 250, 0.16);
        --theme-sidebar-border: rgba(148, 163, 184, 0.12);
        --theme-sidebar-text: rgba(226, 232, 240, 0.92);
        --theme-sidebar-muted: rgba(191, 219, 254, 0.72);
        --theme-topnav-bg: rgba(4, 11, 24, 0.82);
        --theme-topnav-border: rgba(96, 165, 250, 0.14);
        --theme-input-bg: #0b162b;
        --theme-input-border: rgba(148, 163, 184, 0.2);
        --theme-input-text: #e5eefc;
        --theme-table-stripe: rgba(59, 130, 246, 0.08);
        --theme-muted-panel: rgba(15, 23, 42, 0.92);
        --theme-toggle-bg: rgba(96, 165, 250, 0.12);
        --theme-toggle-border: rgba(96, 165, 250, 0.22);
        --theme-toggle-text: #dbeafe;
        --theme-help-bg: rgba(37, 99, 235, 0.14);
        --theme-help-border: rgba(96, 165, 250, 0.28);
        --theme-help-text: #60a5fa;
        --theme-danger-bg: rgba(239, 68, 68, 0.10);
        --theme-danger-border: rgba(248, 113, 113, 0.24);
        --theme-danger-text: #fca5a5;
        --theme-table-head-bg: linear-gradient(180deg, rgba(19, 44, 86, 0.98) 0%, rgba(14, 33, 67, 0.98) 100%);
        --theme-table-head-text: #e8f1ff;
        --theme-table-warning-bg: rgba(133, 77, 14, 0.24);
        --theme-table-warning-text: #fde68a;
    }
    body,
    body.brand-theme-body {
        background: var(--theme-body-bg) !important;
        color: var(--theme-text);
        transition: background-color 0.25s ease, color 0.25s ease;
    }

    .brand-theme-topnav {
        background: var(--theme-topnav-bg) !important;
        backdrop-filter: blur(14px);
        border-bottom: 1px solid var(--theme-topnav-border);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .brand-theme-topnav .nav-link,
    .brand-theme-topnav .navbar-nav .nav-link,
    .brand-theme-topnav .navbar-nav .nav-link i,
    .brand-theme-topnav .navbar-nav .btn-link {
        color: var(--theme-link) !important;
    }

    .brand-theme-topnav .nav-link:hover,
    .brand-theme-topnav .navbar-nav .nav-link:hover {
        color: var(--theme-link-hover) !important;
    }

    .brand-theme-sidebar {
        background: var(--theme-sidebar-bg) !important;
    }

    .brand-theme-link {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: var(--theme-sidebar-surface);
    }

    .brand-theme-link .brand-image {
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.92);
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
        color: var(--theme-sidebar-text);
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .main-sidebar .nav-treeview > .nav-item > .nav-link {
        width: calc(100% - 0.9rem);
        border-radius: 0.85rem;
        margin: 0.08rem 0.4rem 0.08rem 0.5rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        color: var(--theme-sidebar-text);
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link.active,
    .main-sidebar .nav-sidebar > .nav-item > .nav-link:hover,
    .main-sidebar .nav-treeview > .nav-item > .nav-link:hover {
        background: var(--theme-sidebar-surface-strong) !important;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .main-sidebar .nav-sidebar > .nav-item.menu-open > .nav-link {
        background: rgba(96, 165, 250, 0.16) !important;
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
        background: var(--theme-sidebar-surface);
        border: 1px solid var(--theme-sidebar-border);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
        transition: background-color 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
    }

    .brand-user-shortcut:hover,
    .brand-user-shortcut.is-active {
        background: var(--theme-sidebar-surface-strong);
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
        background: linear-gradient(180deg, rgba(96, 165, 250, 0.68), rgba(37, 99, 235, 0.95));
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
        color: var(--theme-sidebar-muted);
        margin-bottom: 0.15rem;
    }

    .brand-user-shortcut__name {
        color: #ffffff;
        font-weight: 700;
        line-height: 1.2;
        white-space: normal;
        word-break: break-word;
    }

    .content-wrapper,
    .main-footer {
        background: transparent !important;
    }

    .content-header h1,
    .content-wrapper .card-title,
    .content-wrapper h1,
    .content-wrapper h2,
    .content-wrapper h3,
    .content-wrapper h4,
    .content-wrapper h5,
    .content-wrapper h6,
    .content-wrapper label,
    .content-wrapper .content,
    .content-wrapper .breadcrumb-item.active,
    .content-wrapper .text-dark,
    .content-wrapper .text-body {
        color: var(--theme-text) !important;
    }

    .content-wrapper p,
    .content-wrapper small,
    .content-wrapper .text-muted,
    .content-wrapper .small,
    .main-footer,
    .brand-logout-menu__copy {
        color: var(--theme-text-soft) !important;
    }

    .content-wrapper .card,
    .content-wrapper .modal-content,
    .content-wrapper .small-box,
    .content-wrapper .info-box,
    .content-wrapper .callout,
    .dropdown-menu,
    .swal2-popup {
        border: 1px solid var(--theme-border) !important;
        border-radius: 1.1rem;
        box-shadow: var(--theme-shadow);
        background: var(--theme-surface-strong);
        color: var(--theme-text);
    }

    .modal-content {
        overflow: hidden;
        border-radius: 1.1rem !important;
    }

    .content-wrapper .card-header,
    .modal-header,
    .dropdown-header {
        border-bottom-color: var(--theme-border) !important;
        background: var(--theme-muted-panel) !important;
        color: var(--theme-text) !important;
    }

    .modal-header {
        border-top-left-radius: calc(1.1rem - 1px);
        border-top-right-radius: calc(1.1rem - 1px);
    }

    .content-wrapper .card-footer,
    .modal-footer {
        border-top-color: var(--theme-border) !important;
        background: transparent;
    }

    .modal-body {
        background: var(--theme-surface-strong);
        color: var(--theme-text);
    }

    .modal-footer {
        border-bottom-left-radius: calc(1.1rem - 1px);
        border-bottom-right-radius: calc(1.1rem - 1px);
    }

    .modal-header.bg-white,
    .modal-footer.bg-light {
        background: var(--theme-muted-panel) !important;
    }

    .modal-header.bg-danger,
    .modal-header.bg-success,
    .modal-header.bg-primary,
    .modal-header.bg-info,
    .modal-header.bg-warning {
        color: #ffffff !important;
    }

    .modal-header .close,
    .modal-header .close span {
        color: inherit !important;
        opacity: 0.78;
        text-shadow: none;
    }

    .modal-header .close:hover,
    .modal-header .close:focus {
        opacity: 1;
    }

    .card.card-outline.card-primary {
        border-top: 3px solid #2563eb;
    }

    .content-wrapper .small-box .inner,
    .content-wrapper .small-box h3,
    .content-wrapper .small-box p,
    .content-wrapper .info-box,
    .content-wrapper .info-box-content {
        color: #ffffff !important;
    }

    html[data-theme='dark'] .content-wrapper .small-box.bg-warning .inner,
    html[data-theme='dark'] .content-wrapper .small-box.bg-warning h3,
    html[data-theme='dark'] .content-wrapper .small-box.bg-warning p,
    html[data-theme='dark'] .content-wrapper .small-box.bg-warning .small-box-footer {
        color: #111827 !important;
    }

    .content-wrapper .small-box .small-box-footer {
        backdrop-filter: blur(6px);
    }

    .content-wrapper table,
    .content-wrapper .table,
    .content-wrapper .table td,
    .content-wrapper .table th,
    .content-wrapper .table thead th {
        color: var(--theme-text);
        border-color: var(--theme-border) !important;
    }

    .content-wrapper .table-striped tbody tr:nth-of-type(odd),
    .content-wrapper .table-hover tbody tr:hover,
    .content-wrapper .list-group-item,
    .content-wrapper .timeline > div > .timeline-item {
        background-color: var(--theme-surface-strong);
        color: var(--theme-text);
    }

    .content-wrapper .table-striped tbody tr:nth-of-type(odd) {
        background-color: var(--theme-table-stripe);
    }

    .content-wrapper .table thead th,
    .content-wrapper .table thead td,
    .content-wrapper .table .thead-light th,
    .content-wrapper .table .thead-light td,
    .content-wrapper .table thead.thead-light th,
    .content-wrapper .table thead.thead-light td {
        background: var(--theme-table-head-bg) !important;
        color: var(--theme-table-head-text) !important;
        border-color: var(--theme-border) !important;
    }

    .content-wrapper .table thead tr {
        background: transparent !important;
    }

    .content-wrapper .table-responsive {
        border-radius: 1rem;
    }

    .content-wrapper .table-warning,
    .content-wrapper .table-warning > th,
    .content-wrapper .table-warning > td,
    .content-wrapper .table-warning th,
    .content-wrapper .table-warning td {
        background-color: var(--theme-table-warning-bg) !important;
        color: var(--theme-table-warning-text) !important;
    }

    .content-wrapper .table-warning .text-warning,
    .content-wrapper .table-warning i,
    .content-wrapper .table-warning span,
    .content-wrapper .table-warning small {
        color: inherit !important;
    }

    .form-control,
    .custom-select,
    .input-group-text,
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        background: var(--theme-input-bg) !important;
        color: var(--theme-input-text) !important;
        border-color: var(--theme-input-border) !important;
    }

    .form-control::placeholder,
    textarea::placeholder {
        color: var(--theme-text-soft) !important;
    }

    .form-control:focus,
    .custom-select:focus,
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-selection--single:focus {
        border-color: rgba(37, 99, 235, 0.58) !important;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.16) !important;
    }

    .page-item .page-link,
    .nav-pills .nav-link,
    .nav-tabs .nav-link,
    .list-group-item,
    .dropdown-item {
        background: transparent;
        color: var(--theme-text);
    }

    .dropdown-item:hover,
    .dropdown-item:focus,
    .nav-tabs .nav-link:hover,
    .nav-pills .nav-link:hover {
        background: rgba(59, 130, 246, 0.10);
        color: var(--theme-link-hover);
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

    .brand-theme-toggle {
        min-height: 2.35rem;
        padding: 0.42rem 0.9rem;
        border-radius: 999px;
        border: 1px solid var(--theme-toggle-border) !important;
        background: var(--theme-toggle-bg) !important;
        color: var(--theme-toggle-text) !important;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .brand-theme-toggle:hover,
    .brand-theme-toggle:focus {
        color: var(--theme-toggle-text) !important;
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(15, 23, 42, 0.12);
    }

    .brand-theme-toggle__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.15rem;
    }

    .brand-theme-toggle__label {
        font-size: 0.88rem;
    }

    .brand-navbar-pill {
        min-height: 2.35rem;
        padding: 0.42rem 0.9rem;
        border-radius: 999px;
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    }

    .brand-navbar-pill:hover,
    .brand-navbar-pill:focus {
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(15, 23, 42, 0.12);
    }

    .brand-navbar-pill--help {
        border: 1px solid var(--theme-help-border) !important;
        background: var(--theme-help-bg) !important;
        color: var(--theme-help-text) !important;
    }

    .brand-navbar-pill--help:hover,
    .brand-navbar-pill--help:focus {
        color: var(--theme-help-text) !important;
    }

    .brand-navbar-pill--danger {
        border: 1px solid var(--theme-danger-border) !important;
        background: var(--theme-danger-bg) !important;
        color: var(--theme-danger-text) !important;
    }

    .brand-navbar-pill--danger:hover,
    .brand-navbar-pill--danger:focus {
        color: var(--theme-danger-text) !important;
    }

    .brand-logout-toggle {
        font-weight: 600;
    }

    .brand-logout-menu {
        min-width: 15rem;
        border: 1px solid var(--theme-border);
        border-radius: 1rem;
        padding: 0.9rem;
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
        background: var(--theme-surface-strong);
    }

    .brand-logout-menu__title {
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--theme-text);
        margin-bottom: 0.35rem;
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

    .main-footer {
        border-top: 1px solid var(--theme-topnav-border);
    }

    .auth-shell,
    .register-shell {
        background: var(--theme-body-bg) !important;
        color: var(--theme-text);
    }

    .auth-card,
    .register-card,
    .register-card-soft,
    .modal-card {
        background: var(--theme-surface-strong) !important;
        color: var(--theme-text) !important;
        border-color: var(--theme-border) !important;
        box-shadow: var(--theme-shadow) !important;
    }

    .auth-link,
    .section-kicker,
    .register-kicker {
        color: #3b82f6 !important;
    }

    .auth-input,
    .auth-card input:not([type="checkbox"]):not([type="radio"]),
    .auth-card textarea,
    .auth-card select,
    .register-form input[type="text"],
    .register-form input[type="email"],
    .register-form input[type="password"],
    .register-form textarea,
    .register-form select,
    .modal-panel input[type="text"],
    .modal-panel input[type="email"],
    .modal-panel input[type="password"],
    .modal-panel input[type="number"],
    .modal-panel textarea,
    .modal-panel select {
        background: var(--theme-input-bg) !important;
        color: var(--theme-input-text) !important;
        border-color: var(--theme-input-border) !important;
        caret-color: var(--theme-input-text) !important;
    }

    .auth-card input:not([type="checkbox"]):not([type="radio"])::placeholder,
    .auth-card textarea::placeholder,
    .auth-card select::placeholder,
    .register-form label,
    .modal-panel label {
        color: var(--theme-text) !important;
    }

    .auth-card input:not([type="checkbox"]):not([type="radio"])::placeholder,
    .auth-card textarea::placeholder,
    .register-form input[type="text"]::placeholder,
    .register-form input[type="email"]::placeholder,
    .register-form input[type="password"]::placeholder,
    .register-form textarea::placeholder,
    .modal-panel input[type="text"]::placeholder,
    .modal-panel input[type="email"]::placeholder,
    .modal-panel input[type="password"]::placeholder,
    .modal-panel input[type="number"]::placeholder,
    .modal-panel textarea::placeholder {
        color: var(--theme-text-soft) !important;
        opacity: 1;
    }

    .auth-card input:not([type="checkbox"]):not([type="radio"]):focus,
    .auth-card textarea:focus,
    .auth-card select:focus {
        border-color: rgba(37, 99, 235, 0.58) !important;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.16) !important;
        outline: none !important;
    }

    .auth-card input:-webkit-autofill,
    .auth-card input:-webkit-autofill:hover,
    .auth-card input:-webkit-autofill:focus,
    .auth-card textarea:-webkit-autofill,
    .auth-card select:-webkit-autofill {
        -webkit-text-fill-color: var(--theme-input-text) !important;
        box-shadow: 0 0 0 1000px var(--theme-input-bg) inset !important;
        transition: background-color 9999s ease-in-out 0s;
    }

    .auth-card .text-gray-600,
    .auth-card .text-gray-500,
    .auth-card .text-gray-700,
    .auth-card .text-gray-900,
    .auth-card .font-medium.text-sm.text-green-600,
    .auth-card .underline.text-sm,
    .auth-card label,
    .auth-card h1,
    .auth-card h2,
    .auth-card h3,
    .auth-card p,
    .auth-card span,
    .auth-card a {
        color: var(--theme-text) !important;
    }

    .auth-card p.text-sm,
    .auth-card .text-sm.text-gray-600,
    .auth-card small,
    .auth-card .font-medium.text-sm.text-green-600,
    .auth-card .ms-2.text-sm {
        color: var(--theme-text-soft) !important;
    }

    .auth-card a:hover,
    .auth-card .underline.text-sm:hover {
        color: var(--theme-link-hover) !important;
    }

    @media (max-width: 575.98px) {
        .brand-navbar-btn {
            min-height: 2rem;
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }

        .brand-theme-toggle__label {
            display: none;
        }

        .brand-theme-toggle {
            min-width: 2.35rem;
            padding-right: 0.7rem;
            padding-left: 0.7rem;
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
</style>
