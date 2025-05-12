<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\LicencaUsuario;
use App\Models\LicencaPlano;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        // Verificar credenciais
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        // Verificar se usuário está ativo
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Conta desativada. Entre em contato com o suporte.'
            ], 403);
        }

        // Verificar se licença está ativa
        if ($user->licenca && !$user->licenca->isAtiva()) {
            return response()->json([
                'message' => 'Sua licença está inativa ou expirada. Por favor, renove sua assinatura.'
            ], 403);
        }

        // Criar token
        $deviceName = $request->device_name ?? $request->userAgent() ?? 'Unknown Device';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tipo_usuario' => 'required|in:pessoa_fisica,usuario_master',
            'empresa_nome' => 'required_if:tipo_usuario,usuario_master|nullable|string',
            'empresa_cnpj' => 'required_if:tipo_usuario,usuario_master|nullable|string',
            'plano_id' => 'required|exists:licenca_planos,id',
            'ciclo_cobranca' => 'required|in:mensal,anual',
        ]);

        // Iniciar transação
        \DB::beginTransaction();

        try {
            $empresaId = null;

            // Criar empresa se for usuário master
            if ($request->tipo_usuario === 'usuario_master') {
                $empresa = \App\Models\Empresa::create([
                    'nome' => $request->empresa_nome,
                    'cnpj' => $request->empresa_cnpj,
                    'email' => $request->email,
                ]);
                $empresaId = $empresa->id;
            }

            // Criar usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo_usuario' => $request->tipo_usuario,
                'empresa_id' => $empresaId,
                'is_active' => true,
            ]);

            // Criar licença
            $plano = LicencaPlano::findOrFail($request->plano_id);

            $dataInicio = Carbon::now();
            $dataExpiracao = $request->ciclo_cobranca === 'anual'
                             ? Carbon::now()->addYear()
                             : Carbon::now()->addMonth();

            LicencaUsuario::create([
                'user_id' => $user->id,
                'plano_id' => $request->plano_id,
                'data_inicio' => $dataInicio,
                'data_expiracao' => $dataExpiracao,
                'ciclo_cobranca' => $request->ciclo_cobranca,
                'status' => 'ativa',
                'ultimo_pagamento' => Carbon::now(),
                'proximo_pagamento' => $dataExpiracao,
            ]);

            // Commit transação
            \DB::commit();

            // Criar token
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();

return response()->json([
                'message' => 'Erro ao registrar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // Revogar o token atual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('licenca.plano');

        return response()->json([
            'user' => $user
        ]);
    }
}
