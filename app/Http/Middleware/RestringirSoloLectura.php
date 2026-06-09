<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestringirSoloLectura
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->esSoloLectura()) {
            // Block non-safe HTTP methods
            if (!in_array($request->method(), ['GET', 'HEAD'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'No tienes permisos para realizar esta acción.'], 403);
                }
                return back()->with('error', 'Tu usuario es de Solo Visualización y no tiene permisos para realizar modificaciones.');
            }

            // Also block GET requests targeting creation or edition forms
            $path = $request->path();
            $forbiddenPatterns = [
                '/crear$/i',
                '/editar/i',
                '/eliminar/i',
                '/importar/i',
                '/cambiar-estado/i'
            ];

            foreach ($forbiddenPatterns as $pattern) {
                if (preg_match($pattern, $path)) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['error' => 'Acceso denegado.'], 403);
                    }
                    return redirect('/')->with('error', 'Tu usuario es de Solo Visualización y no tiene acceso a esta sección.');
                }
            }
        }

        return $next($request);
    }
}
