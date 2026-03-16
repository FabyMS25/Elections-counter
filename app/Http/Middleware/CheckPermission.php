<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                abort(401, 'No autenticado.');
            }
            return redirect()->route('login');
        }
        if (!Auth::user()->hasPermission($permission)) {
            abort(403, "Acceso denegado: se requiere el permiso '{$permission}'.");
        }

        return $next($request);
    }
}
