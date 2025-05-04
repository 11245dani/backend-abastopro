<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Tienda;
use App\Models\Distribuidor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => [
                'required',
                'string',
                'min:6',
            ],
            'rol' => 'required|in:tendero,gestor_despacho,admin',
            'direccion' => 'required_if:rol,tendero|string|max:255', // Agregada validación
            'nombre_empresa' => 'required_if:rol,gestor_despacho|string|max:255', // para distribuidores
        ]);
        

        // Transacción para asegurar consistencia
        DB::beginTransaction();

        try {
            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'password' => Hash::make($request->password), // Mejor que bcrypt directo
                'rol' => $request->rol,
            ]);

            if ($usuario->rol === 'tendero') {
    Tienda::create([
        'usuario_id' => $usuario->id,
        'nombre' => $usuario->nombre,
        'direccion' => $request->direccion,
    ]);

            } elseif ($usuario->rol === 'gestor_despacho') {
                Distribuidor::create([
                    'usuario_id' => $usuario->id,
                    'nombre_empresa' => $usuario->nombre,
                    'direccion' => $request->direccion, // ¡ahora sí mandas la dirección!
                ]);
            }

            DB::commit();

            return response()->json([
                'token' => $usuario->createToken('auth_token')->plainTextToken,
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'mensaje' => 'Error al registrar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json(['mensaje' => 'Credenciales inválidas'], 401);
        }

        // Opcional: Revocar tokens anteriores en cada login
        $usuario->tokens()->delete();

        return response()->json([
            'token' => $usuario->createToken('auth_token')->plainTextToken,
            'usuario' => $usuario
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['mensaje' => 'Sesión cerrada con éxito']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
