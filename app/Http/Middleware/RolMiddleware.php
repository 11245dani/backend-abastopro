<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!in_array($request->user()->rol, $roles)) {
            return response()->json(['mensaje' => 'Acceso no autorizado'], 403);
        }

        return $next($request);
    }
}

