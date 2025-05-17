<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolGestorDespacho
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->rol === 'gestor_despacho') {
            return $next($request);
        }

        return response()->json(['message' => 'No autorizado'], 403);
    }
}

