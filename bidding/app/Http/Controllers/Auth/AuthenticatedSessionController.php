<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Verificar se usuário está ativo
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Esta conta está desativada. Entre em contato com o suporte.']);
        }

        // Verificar status da licença (se não for admin)
        if (!$user->isAdmin() && $user->licenca) {
            if (!$user->licenca->isAtiva()) {
                // Se licença estiver expirada ou inativa, redirecionar para página de renovação
                return redirect()->route('licenca.renovar');
            }

            // Se licença estiver próxima de expirar, mostrar mensagem
            if ($user->licenca->isProximaExpirar()) {
                session()->flash('warning', 'Sua licença expira em ' . $user->licenca->data_expiracao->diffInDays(now()) . ' dias. Considere renovar sua assinatura.');
            }
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
