@can('agenda.notificaciones.ver')
<li class="nav-item">
  <a href="{{ route('notificaciones.index') }}" class="nav-link" title="Notificaciones de citas">
    <i class="far fa-bell"></i>
    @if(($unread ?? 0) > 0)
      <span class="badge badge-danger navbar-badge">{{ $unread }}</span>
    @endif
  </a>
</li>
@endcan
