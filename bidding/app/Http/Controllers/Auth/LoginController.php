<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request. Alias for store method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Limitar tentativas de login
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        // Tentar autenticar o usuário
        if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Limpar limite de tentativas
        RateLimiter::clear($throttleKey);

        // Regenerar sessão
        $request->session()->regenerate();

        // Verificar se usuário está ativo
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Esta conta está desativada. Entre em contato com o suporte.',
            ]);
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

        // Registrar último login
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        // Redirecionar para a página pretendida ou dashboard
        return redirect()->intended(route('dashboard'));
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

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        return $this->destroy($request);
    }
}
