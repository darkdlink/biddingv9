<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     */
    public function show(Request $request)
    {
        // Verificar se o usuário está ativo
        if (!$request->user()->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Sua conta está desativada. Por favor, entre em contato com o suporte.');
        }

        return $request->user()->hasVerifiedEmail()
                        ? redirect($this->redirectPath())
                        : view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        // Verificar se o usuário está ativo
        if (!$request->user()->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Sua conta está desativada. Por favor, entre em contato com o suporte.');
        }

        if ($request->route('id') != $request->user()->getKey()) {
            return redirect()->route('verification.notice');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($request->user()));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        // Verificar se o usuário está ativo
        if (!$request->user()->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Sua conta está desativada. Por favor, entre em contato com o suporte.');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
