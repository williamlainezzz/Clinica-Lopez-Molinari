@extends('layouts.app')

@section('content')
<h1>Objetos</h1>

<form method="POST" action="{{ route('seguridad.objetos.store') }}" class="mb-4">
 @csrf
 <input name="NOM_OBJETO" placeholder="Nombre" required>
 <input name="DESC_OBJETO" placeholder="Descripción">
 <input name="URL_OBJETO" placeholder="URL" required>
 <select name="ESTADO_OBJETO">
   <option value="1">ACTIVO</option>
   <option value="0">INACTIVO</option>
 </select>
 <button type="submit">Guardar</button>
</form>

<table border="1" cellpadding="6">
 <thead>
   <tr><th>Nombre</th><th>Descripción</th><th>URL</th><th>Estado</th><th>Acciones</th></tr>
 </thead>
 <tbody>
  @foreach($objetos as $o)
   <tr>
    <td>{{ $o->NOM_OBJETO }}</td>
    <td>{{ $o->DESC_OBJETO }}</td>
    <td>{{ $o->URL_OBJETO }}</td>
    <td>{{ $o->ESTADO_OBJETO ? 'ACTIVO':'INACTIVO' }}</td>
    <td>
      <form method="POST" action="{{ route('seguridad.objetos.destroy',$o->COD_OBJETO) }}" onsubmit="return confirm('¿Eliminar?')">
        @csrf @method('DELETE')
        <button>🗑</button>
      </form>
    </td>
   </tr>
  @endforeach
 </tbody>
</table>
@endsection
