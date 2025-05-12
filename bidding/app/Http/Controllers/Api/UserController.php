<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        return response()->json([
            'user' => $user,
            'licenca' => $licenca,
            'plano' => $plano,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verificar senha atual se estiver alterando a senha
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'A senha atual está incorreta'
                ], 422);
            }
        }

        // Atualizar dados do usuário
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'user' => $user
        ]);
    }

    public function segmentos()
    {
        $user = Auth::user();
        $segmentos = $user->segmentos;

        return response()->json($segmentos);
    }
}
