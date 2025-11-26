<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar redirección si el usuario está autenticado
        if (!$request->user()) {
            return $next($request);
        }

        // Si está intentando acceder al dashboard
        if ($request->is('dashboard')) {
            // Si es cliente, redirigir al catálogo
            if ($request->user()->hasRole('cliente')) {
                return redirect()->route('home');
            }
        }

        // Si está intentando acceder al home/catálogo y es admin/gerente/ventas/almacen
        // y viene desde login, redirigir al dashboard
        if ($request->is('/') || $request->is('catalogo')) {
            if ($request->user()->hasAnyRole(['admin', 'gerente', 'ventas', 'almacen'])) {
                // Solo redirigir si viene de login (tiene referrer de login)
                $referrer = $request->headers->get('referer');
                if ($referrer && str_contains($referrer, 'login')) {
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}
