<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyLicencaStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar se usuário está ativo
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua conta está desativada. Entre em contato com o suporte.');
        }

        // Usuários admin do sistema não precisam de licença
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verificar se usuário tem licença
        $licenca = $user->licenca;
        if (!$licenca) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Você não possui uma licença válida. Por favor, adquira um plano.');
        }

        // Verificar se licença está ativa
        if (!$licenca->isAtiva()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua licença está inativa ou expirada. Por favor, renove sua assinatura.');
        }

        return $next($request);
    }
}
