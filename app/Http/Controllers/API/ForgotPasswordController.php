<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        // Validar el campo de correo
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email|exists:usuarios,correo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'El correo es requerido y debe estar registrado.',
                'errores' => $validator->errors()
            ], 422);
        }

        // Enviar enlace de restablecimiento (usar clave 'email' en el arreglo)
        $status = Password::broker('usuarios')->sendResetLink([
            'correo' => $request->correo, // 🔧 Clave correcta para el broker de Laravel
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['mensaje' => 'Enlace de restablecimiento enviado al correo.']);
        }

        return response()->json([
            'mensaje' => 'No se pudo enviar el enlace. Intenta de nuevo más tarde.'
        ], 500);
    }
}
