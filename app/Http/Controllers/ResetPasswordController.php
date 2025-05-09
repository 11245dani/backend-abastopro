<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function mostrarFormulario($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function cambiarContrasena(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ]);

        $registro = DB::table('password_resets')->where([
            ['email', $request->email],
            ['token', $request->token],
        ])->first();

        if (!$registro || Carbon::parse($registro->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'El token es inválido o ha expirado.']);
        }

        $usuario = Usuario::where('correo', $request->email)->first();

        if (!$usuario) {
            return back()->withErrors(['email' => 'No se encontró un usuario con este correo.']);
        }

        $usuario->password = Hash::make($request->password);
        $usuario->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return view('auth.reset-password-exito');
    }
}
