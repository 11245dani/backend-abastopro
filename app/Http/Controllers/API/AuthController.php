<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Tienda;
use App\Models\Distribuidor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:tendero,gestor_despacho,admin',
            'direccion' => 'required_if:rol,tendero|string|max:255',
            'nombre_empresa' => 'required_if:rol,gestor_despacho|string|max:255',
        ]);
    
        DB::beginTransaction();
    
        try {
            // 1. Crear usuario vacío
            $usuario = new Usuario();
            $usuario->nombre = $request->nombre;
            $usuario->correo = $request->correo;
            $usuario->password = Hash::make($request->password);
            $usuario->rol = $request->rol;
            $usuario->estado = 'inactivo';
            $usuario->verification_token = Str::random(60);
            $usuario->save(); // 👈 Importante: ahora el token queda guardado correctamente
    
            // 2. Enviar correo con token correcto
            Mail::to($usuario->correo)->send(new \App\Mail\VerifyEmail($usuario));
    
            // 3. Crear tienda o distribuidor si aplica
            if ($usuario->rol === 'tendero') {
                Tienda::create([
                    'usuario_id' => $usuario->id,
                    'nombre' => $usuario->nombre,
                    'direccion' => $request->direccion,
                ]);
            } elseif ($usuario->rol === 'gestor_despacho') {
                Distribuidor::create([
                    'usuario_id' => $usuario->id,
                    'nombre_empresa' => $request->nombre_empresa,
                    'direccion' => $request->direccion,
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'mensaje' => 'Registro exitoso. Revisa tu correo para verificar tu cuenta.',
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
        
        if ($usuario->estado !== 'activo') {
            return response()->json(['mensaje' => 'Verifica tu correo antes de iniciar sesión'], 403);
        }
        
        

        // Opcional: Revocar tokens anteriores en cada login
        $usuario->tokens()->delete();

        return response()->json([
            'token' => $usuario->createToken('auth_token')->plainTextToken,
            'usuario' => $usuario,
            'rol' => $usuario->rol // <-- agregado

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


    public function verificarCorreoWeb($token)
    {
        $usuario = Usuario::where('verification_token', $token)->first();
    
        if (!$usuario) {
            return view('verificacion.resultado', ['mensaje' => 'Token inválido']);
        }
    
        $usuario->estado = 'activo';
        $usuario->verification_token = null;
        $usuario->save();
    
        return view('verificacion.resultado', ['mensaje' => 'Cuenta verificada exitosamente']);
    }
    

}
