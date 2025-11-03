<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CitasAccionesController extends Controller
{
    // GET /agenda/citas/{id}
    public function show($id)
    {
        // Stub: aquí luego cargarás la cita real por $id
        return back()->with('status', "VER: Cita #{$id} (stub)");
    }

    // GET /agenda/citas/{id}/reprogramar
    public function edit($id)
    {
        // Stub: devuelve un 200 simple para probar
        return back()->with('status', "FORM REPROGRAMAR: Cita #{$id} (stub)");
    }

    // PUT /agenda/citas/{id}/reprogramar
    public function update(Request $request, $id)
    {
        // Stub: aquí luego validarás y actualizarás fecha/hora
        return back()->with('status', "REPROGRAMADA: Cita #{$id} (stub)");
    }

    // DELETE /agenda/citas/{id}
    public function cancel($id)
    {
        // Stub: aquí luego marcarás estado = Cancelada
        return back()->with('status', "CANCELADA: Cita #{$id} (stub)");
    }
}
