<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use App\Models\LicencaUsuario;
use App\Models\LicencaPlano;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // Obter planos disponíveis
        $planos = LicencaPlano::where('tipo', 'pessoa_fisica')->get();
        $planosEmpresa = LicencaPlano::where('tipo', 'empresa')->get();

        // Verificar se há um plano pré-selecionado na query string
        $planoPreSelecionado = null;
        if ($request->has('plan')) {
            $planoPreSelecionado = $request->plan;
        }

        return view('auth.register', [
            'planos' => $planos,
            'planosEmpresa' => $planosEmpresa,
            'planoPreSelecionado' => $planoPreSelecionado,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tipo_usuario' => ['required', 'in:pessoa_fisica,usuario_master'],
            'empresa_nome' => ['required_if:tipo_usuario,usuario_master', 'nullable', 'string', 'max:255'],
            'empresa_cnpj' => ['required_if:tipo_usuario,usuario_master', 'nullable', 'string', 'max:18'],
            'plano_id' => ['required', 'exists:licenca_planos,id'],
            'ciclo_cobranca' => ['required', 'in:mensal,anual'],
        ]);

        // Iniciar transação para garantir consistência
        \DB::beginTransaction();

        try {
            // Criar empresa se for usuário master
            $empresaId = null;
            if ($request->tipo_usuario === 'usuario_master' && $request->empresa_nome) {
                $empresa = Empresa::create([
                    'nome' => $request->empresa_nome,
                    'cnpj' => $request->empresa_cnpj,
                    'email' => $request->email,
                    'is_active' => true,
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

            // Verificar compatibilidade do plano com tipo de usuário
            if (($plano->tipo === 'pessoa_fisica' && $request->tipo_usuario !== 'pessoa_fisica') ||
                ($plano->tipo === 'empresa' && $request->tipo_usuario !== 'usuario_master') ||
                ($plano->tipo === 'grupo' && $request->tipo_usuario !== 'admin_grupo')) {
                \DB::rollBack();
                return redirect()->back()->withErrors([
                    'plano_id' => 'O plano selecionado não é compatível com o tipo de usuário'
                ])->withInput();
            }

            // Definir datas para licença
            $dataInicio = Carbon::now();
            $dataExpiracao = $request->ciclo_cobranca === 'anual' ?
                             Carbon::now()->addYear() :
                             Carbon::now()->addMonth();

            // Criar licença com status pendente (até processamento do pagamento)
            LicencaUsuario::create([
                'user_id' => $user->id,
                'plano_id' => $request->plano_id,
                'data_inicio' => $dataInicio,
                'data_expiracao' => $dataExpiracao,
                'ciclo_cobranca' => $request->ciclo_cobranca,
                'status' => 'pendente', // Será atualizado após confirmação de pagamento
                'ultimo_pagamento' => null,
                'proximo_pagamento' => $dataExpiracao,
            ]);

            // Commit transação
            \DB::commit();

            // Disparar evento de registro
            event(new Registered($user));

            // Autenticar usuário
            Auth::login($user);

            // Redirecionar para página de pagamento
            return redirect()->route('checkout.plano', ['plano_id' => $plano->id, 'ciclo' => $request->ciclo_cobranca]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return redirect()->back()->withErrors([
                'error' => 'Erro ao registrar: ' . $e->getMessage()
            ])->withInput();
        }
    }
}
