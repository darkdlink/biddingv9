<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Empresa;
use App\Models\LicencaPlano;
use App\Models\LicencaUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Carbon\Carbon;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'tipo_usuario' => ['required', 'in:pessoa_fisica,usuario_master'],
            'plano_id' => ['required', 'exists:licenca_planos,id'],
            'ciclo_cobranca' => ['required', 'in:mensal,anual'],
        ];

        // Regras adicionais para empresas
        if (isset($input['tipo_usuario']) && $input['tipo_usuario'] === 'usuario_master') {
            $rules['empresa_nome'] = ['required', 'string', 'max:255'];
            $rules['empresa_cnpj'] = ['required', 'string', 'max:18', 'unique:empresas,cnpj'];
        }

        Validator::make($input, $rules)->validate();

        // Iniciar transação
        return DB::transaction(function () use ($input) {
            $empresaId = null;

            // Criar empresa se for usuário master
            if ($input['tipo_usuario'] === 'usuario_master') {
                $empresa = Empresa::create([
                    'nome' => $input['empresa_nome'],
                    'cnpj' => $input['empresa_cnpj'],
                    'email' => $input['email'],
                    'is_active' => true,
                ]);

                $empresaId = $empresa->id;
            }

            // Criar usuário
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'tipo_usuario' => $input['tipo_usuario'],
                'empresa_id' => $empresaId,
                'is_active' => true,
            ]);

            // Verificar compatibilidade do plano
            $plano = LicencaPlano::findOrFail($input['plano_id']);

            if (($plano->tipo === 'pessoa_fisica' && $input['tipo_usuario'] !== 'pessoa_fisica') ||
                ($plano->tipo === 'empresa' && $input['tipo_usuario'] !== 'usuario_master') ||
                ($plano->tipo === 'grupo' && $input['tipo_usuario'] !== 'admin_grupo')) {

                throw new \Exception('O plano selecionado não é compatível com o tipo de usuário.');
            }

            // Criar licença
            $dataInicio = Carbon::now();
            $dataExpiracao = $input['ciclo_cobranca'] === 'anual' ?
                            Carbon::now()->addYear() :
                            Carbon::now()->addMonth();

            LicencaUsuario::create([
                'user_id' => $user->id,
                'plano_id' => $input['plano_id'],
                'data_inicio' => $dataInicio,
                'data_expiracao' => $dataExpiracao,
                'ciclo_cobranca' => $input['ciclo_cobranca'],
                'status' => 'ativa',
                'ultimo_pagamento' => Carbon::now(),
                'proximo_pagamento' => $dataExpiracao,
            ]);

            return $user;
        });
    }
}
