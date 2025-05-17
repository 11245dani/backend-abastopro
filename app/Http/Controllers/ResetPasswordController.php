<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ]);

        // Buscar el registro solo por email
        $registro = DB::table('password_resets')->where('email', $request->email)->first();

        // Validar existencia, token y expiración
        if (
            !$registro ||
            !Hash::check($request->token, $registro->token) || // Comparar usando Hash::check
            Carbon::parse($registro->created_at)->addMinutes(60)->isPast()
        ) {
            return redirect()->back()->withErrors(['token' => 'El token es inválido o ha expirado.']);
        }

        // Buscar al usuario por el campo 'correo'
        $usuario = Usuario::where('correo', $request->email)->first();

        if (!$usuario) {
            return redirect()->back()->withErrors(['email' => 'No se encontró un usuario con este correo.']);
        }

        // Actualizar contraseña
        $usuario->password = Hash::make($request->password);
        $usuario->save();

        // Eliminar token usado
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('reset.password.success');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }
}