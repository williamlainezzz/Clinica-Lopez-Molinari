@php( $logoutUrl = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )

@if (config('adminlte.use_route_url', false))
    @php( $logoutUrl = $logoutUrl ? route($logoutUrl) : '' )
@else
    @php( $logoutUrl = $logoutUrl ? url($logoutUrl) : '' )
@endif

<li class="nav-item dropdown d-flex align-items-center">
    <a
        href="#"
        class="btn btn-outline-danger btn-sm brand-logout-toggle brand-navbar-btn text-danger"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        <i class="fas fa-sign-out-alt mr-1"></i>
        Salir
    </a>

    <div class="dropdown-menu dropdown-menu-right brand-logout-menu">
        <div class="brand-logout-menu__title">Deseas salir del sistema?</div>
        <div class="brand-logout-menu__copy">Puedes cancelar si todavia necesitas seguir trabajando.</div>

        <div class="brand-logout-actions">
            <button type="button" class="btn btn-outline-secondary" onclick="event.preventDefault(); $(this).closest('.dropdown').find('[data-toggle=&quot;dropdown&quot;]').dropdown('toggle');">No</button>
            <button type="button" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Si</button>
        </div>
    </div>
</li>

<form id="logout-form" action="{{ $logoutUrl }}" method="POST" style="display: none;">
    @if(config('adminlte.logout_method'))
        {{ method_field(config('adminlte.logout_method')) }}
    @endif
    {{ csrf_field() }}
</form>
