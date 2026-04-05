<?php

namespace App\Http\Controllers;

use App\Models\PreguntaSeguridad;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(): View
    {
        $preguntasSeg = Cache::remember('welcome.active_security_questions', now()->addMinutes(30), function () {
            return PreguntaSeguridad::query()
                ->where('ESTADO', 1)
                ->orderBy('TEXTO_PREGUNTA')
                ->get();
        });

        return view('welcome', [
            'preguntasSeg' => $preguntasSeg,
        ]);
    }
}
