<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $usuario = $request->user();

        Log::info('ROLE MIDDLEWARE DEBUG', [
        'user' => $usuario,
        'roles_required' => $roles,
        'user_role' => $usuario?->role_user,
    ]);

        if (! $usuario || ! $usuario->hasAnyRole(...$roles)) {
            Log::warning('ACCESS DENIED', [
            'user_id' => $usuario?->id,
            'user_role' => $usuario?->role_user,
            'required_roles' => $roles,
        ]);

            abort(403);
        }

        return $next($request);
    }
}
