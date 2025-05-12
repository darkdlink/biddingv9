<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLicencaPermission
{
    public function handle(Request $request, Closure $next, $recurso)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins do sistema têm acesso a tudo
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verificar se usuário tem permissão para o recurso
        if (!$user->hasRecurso($recurso)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Seu plano atual não permite acesso a este recurso. Considere fazer um upgrade do seu plano.'
                ], 403);
            }

            return redirect()->route('dashboard')->with('error',
                'Seu plano atual não permite acesso a este recurso. Considere fazer um upgrade do seu plano.');
        }

        return $next($request);
    }
}
