<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $licenca = $user->licenca()->with('plano')->first();

        return view('perfil.show', [
            'user' => $user,
            'licenca' => $licenca
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    return $fail(__('A senha atual está incorreta.'));
                }
            }],
            'password' => ['nullable', 'required_with:current_password', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('perfil.show')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function markNotificationsAsRead()
    {
        $user = Auth::user();
        $user->alertasNaoLidos()->update(['lido' => true, 'data_leitura' => now()]);

        return redirect()->back()
            ->with('success', 'Todas as notificações foram marcadas como lidas.');
    }
}
