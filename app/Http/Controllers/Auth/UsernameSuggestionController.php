<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UsernameGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsernameSuggestionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $nombre   = trim((string) $request->query('nombre', ''));
        $apellido = trim((string) $request->query('apellido', ''));

        if ($nombre === '' || $apellido === '') {
            return response()->json([
                'message' => 'Nombre y apellido son obligatorios.',
            ], 422);
        }

        return response()->json([
            'username' => UsernameGenerator::generate($nombre, $apellido),
        ]);
    }
}
