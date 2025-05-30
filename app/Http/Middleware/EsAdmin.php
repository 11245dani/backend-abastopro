<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
 public function handle($request, Closure $next)
{
    if ($request->user()->rol !== 'admin') {
        return response()->json(['mensaje' => 'Acceso denegado. Solo administradores.'], 403);
    }

    return $next($request);
}

}
