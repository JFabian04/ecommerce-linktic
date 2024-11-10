<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateApi
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado con Sanctum
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['error' => 'No autorizado.'], 401);
        }

        return $next($request);
    }
}
