<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Empresa;
use App\Models\LicencaUsuario;
use App\Models\LicencaPlano;
use App\Models\Segmento;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsuarioTesteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar admin do sistema
        $adminSistema = User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistemabidding.com.br',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'admin_sistema',
            'is_active' => true,
        ]);

        // Criar grupo empresarial
        $grupo = Grupo::create([
            'nome' => 'Grupo Teste S/A',
            'cnpj' => '12.345.678/0001-90',
            'telefone' => '(11) 99999-8888',
            'email' => 'contato@grupoteste.com.br',
            'endereco' => 'Avenida Principal, 1000',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01000-000',
            'is_active' => true,
        ]);

        // Criar empresa do grupo
        $empresaGrupo = Empresa::create([
            'nome' => 'Empresa do Grupo Ltda',
            'cnpj' => '12.345.678/0002-71',
            'telefone' => '(11) 99999-7777',
            'email' => 'contato@empresagrupo.com.br',
            'endereco' => 'Avenida Secundária, 2000',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01000-000',
            'grupo_id' => $grupo->id,
            'is_active' => true,
        ]);

        // Criar admin do grupo
        $adminGrupo = User::create([
            'name' => 'Admin Grupo',
            'email' => 'admin.grupo@empresagrupo.com.br',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'admin_grupo',
            'empresa_id' => $empresaGrupo->id,
            'is_active' => true,
        ]);

        // Criar licença para admin do grupo
        $planoGrupo = LicencaPlano::where('tipo', 'grupo')->where('tier', 'basico')->first();
        $licencaAdminGrupo = LicencaUsuario::create([
            'user_id' => $adminGrupo->id,
            'plano_id' => $planoGrupo->id,
            'data_inicio' => Carbon::now(),
            'data_expiracao' => Carbon::now()->addYear(),
            'ciclo_cobranca' => 'anual',
            'status' => 'ativa',
            'ultimo_pagamento' => Carbon::now(),
            'proximo_pagamento' => Carbon::now()->addYear(),
        ]);

        // Criar empresa independente
        $empresaIndependente = Empresa::create([
            'nome' => 'Empresa Independente Ltda',
            'cnpj' => '98.765.432/0001-10',
            'telefone' => '(11) 99999-6666',
            'email' => 'contato@empresaindependente.com.br',
            'endereco' => 'Rua Principal, 500',
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
            'cep' => '20000-000',
            'is_active' => true,
        ]);

        // Criar usuário master da empresa
        $usuarioMaster = User::create([
            'name' => 'Usuário Master',
            'email' => 'master@empresaindependente.com.br',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'usuario_master',
            'empresa_id' => $empresaIndependente->id,
            'is_active' => true,
        ]);

        // Criar licença para usuário master
        $planoEmpresa = LicencaPlano::where('tipo', 'empresa')->where('tier', 'intermediario')->first();
        $licencaUsuarioMaster = LicencaUsuario::create([
            'user_id' => $usuarioMaster->id,
            'plano_id' => $planoEmpresa->id,
            'data_inicio' => Carbon::now(),
            'data_expiracao' => Carbon::now()->addYear(),
            'ciclo_cobranca' => 'anual',
            'status' => 'ativa',
            'ultimo_pagamento' => Carbon::now(),
            'proximo_pagamento' => Carbon::now()->addYear(),
        ]);

        // Criar usuários dependentes para a empresa
        for ($i = 1; $i <= 3; $i++) {
            $usuarioDependente = User::create([
                'name' => "Usuário Dependente {$i}",
                'email' => "dependente{$i}@empresaindependente.com.br",
                'password' => Hash::make('senha123'),
                'tipo_usuario' => 'pessoa_fisica',
                'empresa_id' => $empresaIndependente->id,
                'is_active' => true,
            ]);
        }

        // Criar uma pessoa física independente
        $pessoaFisica = User::create([
            'name' => 'Pessoa Física',
            'email' => 'pessoa.fisica@example.com',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'pessoa_fisica',
            'is_active' => true,
        ]);

        // Criar licença para pessoa física
        $planoPessoaFisica = LicencaPlano::where('tipo', 'pessoa_fisica')->where('tier', 'basico')->first();
        $licencaPessoaFisica = LicencaUsuario::create([
            'user_id' => $pessoaFisica->id,
            'plano_id' => $planoPessoaFisica->id,
            'data_inicio' => Carbon::now(),
            'data_expiracao' => Carbon::now()->addMonth(),
            'ciclo_cobranca' => 'mensal',
            'status' => 'ativa',
            'ultimo_pagamento' => Carbon::now(),
            'proximo_pagamento' => Carbon::now()->addMonth(),
        ]);

        // Criar segmentos
        $segmentoTI = Segmento::create([
            'nome' => 'Tecnologia da Informação',
            'descricao' => 'Segmento de TI incluindo hardware, software e serviços',
            'palavras_chave' => ['tecnologia', 'software', 'hardware', 'computador', 'sistema', 'informática', 'TI', 'rede', 'servidor', 'desenvolvimento'],
            'empresa_id' => $empresaIndependente->id,
        ]);

        $segmentoSaude = Segmento::create([
            'nome' => 'Saúde',
            'descricao' => 'Segmento de saúde incluindo equipamentos e serviços médicos',
            'palavras_chave' => ['saúde', 'médico', 'hospital', 'equipamento médico', 'medicamento', 'enfermagem', 'clínica', 'laboratorial', 'ambulatorial'],
            'empresa_id' => $empresaGrupo->id,
        ]);

        $segmentoConstrucao = Segmento::create([
            'nome' => 'Construção Civil',
            'descricao' => 'Segmento de construção civil e engenharia',
            'palavras_chave' => ['construção', 'obra', 'engenharia', 'reforma', 'edificação', 'infraestrutura', 'material de construção', 'pavimentação', 'estrutura'],
            'user_id' => $pessoaFisica->id,
        ]);

        // Associar usuários aos segmentos
        $usuarioMaster->segmentos()->attach($segmentoTI->id);
        $adminGrupo->segmentos()->attach($segmentoSaude->id);
        $pessoaFisica->segmentos()->attach($segmentoConstrucao->id);

        $this->command->info('Usuários de teste criados com sucesso!');
        $this->command->info('Credenciais:');
        $this->command->info('Admin do Sistema: admin@sistemabidding.com.br / senha123');
        $this->command->info('Admin do Grupo: admin.grupo@empresagrupo.com.br / senha123');
        $this->command->info('Usuário Master: master@empresaindependente.com.br / senha123');
        $this->command->info('Pessoa Física: pessoa.fisica@example.com / senha123');
    }
}
