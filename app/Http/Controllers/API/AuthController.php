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
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\URL;
use App\Notifications\NuevoUsuarioRegistrado;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:tendero,gestor_despacho',
            'direccion' => 'required_if:rol,tendero|string|max:255',
            'nombre_empresa' => 'required_if:rol,gestor_despacho|string|max:255',
        ]);
    
        // 🔒 Validación explícita adicional (opcional, pero útil)
        if ($request->rol === 'admin') {
        return response()->json([
            'mensaje' => 'No está permitido registrarse con rol de administrador.'
        ], 403);
        }

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
                     'estado_autorizacion' => 'pendiente', // 👈 Queda pendiente por aprobación
                ]);
            }
    
            DB::commit();
    // Notificar al administrador solo si es un gestor_despacho
if ($usuario->rol === 'gestor_despacho') {
    Notification::route('mail', 'abastopro07@gmail.com')
        ->notify(new NuevoUsuarioRegistrado($usuario));
}

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

    if ($usuario->estado !== 'activo') {
        return response()->json(['mensaje' => 'Verifica tu correo antes de iniciar sesión'], 403);
    }

    // 👇 Nueva validación para gestor_despacho
    if ($usuario->rol === 'gestor_despacho') {
        $distribuidor = Distribuidor::where('usuario_id', $usuario->id)->first();

        if (!$distribuidor || $distribuidor->estado_autorizacion !== 'aprobado') {
            return response()->json(['mensaje' => 'Tu cuenta aún no ha sido autorizada por un administrador.'], 403);
        }
    }

    // Revocar tokens anteriores en cada login
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
    



public function actualizarUsuario(Request $request)
{
    $usuario = $request->user();

    $reglas = [
        'nombre' => 'sometimes|required|string|max:255',
        'correo' => 'sometimes|required|email|unique:usuarios,correo,' . $usuario->id,
        'password' => 'sometimes|required|string|min:6|confirmed', // password + password_confirmation
    ];

    if ($usuario->rol === 'tendero' || $usuario->rol === 'gestor_despacho') {
        $reglas['direccion'] = 'sometimes|required|string|max:255';
    }

    if ($usuario->rol === 'gestor_despacho') {
        $reglas['nombre_empresa'] = 'sometimes|required|string|max:255';
    }

    $request->validate($reglas);

    $correoOriginal = $usuario->correo;

    if ($request->has('nombre')) {
        $usuario->nombre = $request->nombre;
    }

    if ($request->has('direccion')) {
        $usuario->direccion = $request->direccion;
    }

    if ($usuario->rol === 'gestor_despacho' && $request->has('nombre_empresa')) {
        $usuario->nombre_empresa = $request->nombre_empresa;
    }

    // Cambiar correo: se requiere verificación
    if ($request->has('correo') && $request->correo !== $correoOriginal) {
    $usuario->correo = $request->correo;
    $usuario->estado = 'inactivo';
    $usuario->verification_token = Str::random(60);
    $usuario->email_verified_at = null;

    $usuario->save();
    $request->user()->currentAccessToken()->delete();

    // Enviar correo personalizado para verificación
    Mail::to($usuario->correo)->send(new \App\Mail\VerifyUpdatedEmail($usuario));

    return response()->json([
        'mensaje' => 'Estás actualizando tu correo electrónico. Tu sesión quedará inhabilitada mientras confirmas la nueva dirección de correo.',
        'requiere_verificacion' => true,
    ]);
}


    if ($request->has('password')) {
        // Generar token manualmente
        $token = Str::random(60);
    
        // Guardar token en tabla password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $usuario->correo],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );
    
        // Construir URL de restablecimiento
        $url = url('/restablecer-contrasena/' . $token);
    
        // Enviar correo usando tu clase personalizada
        Mail::to($usuario->correo)->send(new \App\Mail\CustomResetPassword($url, $usuario->correo));
    
        return response()->json([
            'mensaje' => 'Se ha enviado un enlace para cambiar tu contraseña al correo',
            'requiere_verificacion' => $request->correo !== $correoOriginal,
        ]);
    }
    

    $usuario->save();

    return response()->json([
        'mensaje' => 'Información actualizada correctamente',
        'requiere_verificacion' => $request->has('correo') && $request->correo !== $correoOriginal,
        'usuario' => $usuario
    ]);
}

public function listarUsuarios(Request $request)
{
    $usuario = $request->user();

    // Solo el administrador con rol "admin" y correo específico puede acceder
    if ($usuario->rol !== 'admin' || $usuario->correo !== 'abastopro07@gmail.com') {
        return response()->json(['mensaje' => 'Acceso no autorizado'], 403);
    }

    $query = Usuario::query()
        ->where('id', '!=', $usuario->id); // Excluir al admin autenticado

    //  Búsqueda por nombre o correo
    if ($request->filled('buscar')) {
        $buscar = $request->input('buscar');
        $query->where(function ($q) use ($buscar) {
            $q->where('nombre', 'like', "%{$buscar}%")
              ->orWhere('correo', 'like', "%{$buscar}%");
        });
    }

    //  Filtro por rol
    if ($request->filled('rol')) {
        $rol = $request->input('rol');
        if (in_array($rol, ['tendero', 'gestor_despacho'])) {
            $query->where('rol', $rol);
        } else {
            return response()->json(['mensaje' => 'Rol inválido.'], 400);
        }
    }

    $usuarios = $query->get();

    if ($usuarios->isEmpty()) {
        return response()->json(['mensaje' => 'No se encontraron usuarios que coincidan con la búsqueda.'], 404);
    }

    return response()->json(['usuarios' => $usuarios], 200);
}



}
