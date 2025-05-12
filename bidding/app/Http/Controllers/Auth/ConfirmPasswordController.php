<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the password confirmation view.
     */
    public function showConfirmForm()
    {
        // Verificar se o usuário está ativo
        if (!auth()->user()->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Sua conta está desativada. Por favor, entre em contato com o suporte.');
        }

        return view('auth.passwords.confirm');
    }

    /**
     * Confirm the given user's password.
     */
    public function confirm(Request $request)
    {
        // Verificar se o usuário está ativo
        if (!$request->user()->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Sua conta está desativada. Por favor, entre em contato com o suporte.');
        }

        $this->validatePassword($request);

        $this->resetPasswordConfirmationTimeout($request);

        return redirect()->intended($this->redirectPath());
    }
}
