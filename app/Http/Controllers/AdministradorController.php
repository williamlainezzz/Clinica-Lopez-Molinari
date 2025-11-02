<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AdministradorController extends Controller
{
    public function store(Request $request)
    {
        // Validar campos según el partial de registro (ajusta si tus nombres de input difieren)
        $rules = [
            'PRIMER_NOMBRE' => 'required|string|max:255',
            'PRIMER_APELLIDO' => 'required|string|max:255',
            'CORREO' => 'required|email|max:255|unique:users,email',
            'NUM_TELEFONO' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed', // espera password_confirmation
        ];

        $validated = $request->validate($rules);

        // Construir nombre completo
        $first = trim($validated['PRIMER_NOMBRE']);
        $last  = trim($validated['PRIMER_APELLIDO']);
        $name  = trim($first . ' ' . $last);

        // Generar username base: inicial + primer apellido (sanitizado)
        $initial = mb_substr($first, 0, 1);
        $cleanLast = preg_replace('/\s+/', '', $last);
        $baseUsername = strtolower($initial . $cleanLast);
        $baseUsername = Str::ascii($baseUsername);
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);
        if (!$baseUsername) {
            $baseUsername = 'user' . Str::random(4);
        }

        // Determinar username único SOLO si la columna existe en la tabla users
        if (Schema::hasColumn('users', 'username')) {
            $username = $baseUsername;
            $i = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $i++;
                if ($i > 1000) {
                    $username = $baseUsername . Str::random(4);
                    break;
                }
            }
        } else {
            // Si no existe la columna username en la BD, generar un valor local y NO usar consultas que referencien la columna
            $username = $baseUsername . Str::random(4);
        }

        // Preparar los datos a insertar; sólo incluir campos que existen en la tabla
        $insertData = [
            'name' => $name,
            'email' => $validated['CORREO'],
            'password' => Hash::make($validated['password']),
        ];

        if (Schema::hasColumn('users', 'username')) {
            $insertData['username'] = $username;
        }
        if (Schema::hasColumn('users', 'phone')) {
            $insertData['phone'] = $validated['NUM_TELEFONO'] ?? null;
        }
        if (Schema::hasColumn('users', 'role')) {
            $insertData['role'] = 'admin';
        }

        // Crear usuario usando solo las columnas existentes
        $user = User::create($insertData);

        return response()->json([
            'success' => true,
            'message' => 'Administrador creado exitosamente',
            'data' => $user
        ], 201);
    }
}
