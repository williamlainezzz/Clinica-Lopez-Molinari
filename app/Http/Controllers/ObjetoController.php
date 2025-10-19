<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use Illuminate\Http\Request;

class ObjetoController extends Controller
{
    public function index()
    {
        $objetos = Objeto::orderBy('NOM_OBJETO')->get();
        return view('seguridad.objetos.index', compact('objetos'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'NOM_OBJETO'   => 'required|string|max:150',
            'DESC_OBJETO'  => 'nullable|string|max:255',
            'URL_OBJETO'   => 'required|string|max:255',
            'ESTADO_OBJETO'=> 'required|in:0,1',
        ]);

        // upsert por nombre o url
        $existente = Objeto::where('NOM_OBJETO',$data['NOM_OBJETO'])
            ->orWhere('URL_OBJETO',$data['URL_OBJETO'])->first();

        if ($existente) {
            $existente->update($data);
        } else {
            Objeto::create($data);
        }
        return back()->with('ok','Objeto guardado');
    }

    public function destroy($id)
    {
        Objeto::where('COD_OBJETO',$id)->delete();
        return back()->with('ok','Objeto eliminado');
    }
}
