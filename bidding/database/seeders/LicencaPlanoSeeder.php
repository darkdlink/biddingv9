<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LicencaPlano;
use App\Models\LicencaRecurso;

class LicencaPlanoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Planos para Pessoa Física
        $pessoaFisicaBasico = LicencaPlano::create([
            'nome' => 'Pessoa Física - Básico',
            'tipo' => 'pessoa_fisica',
            'tier' => 'basico',
            'preco_mensal' => 97.00,
            'preco_anual' => 932.00, // 20% desconto anual
            'max_usuarios' => 1,
            'max_segmentos' => 1,
            'max_empresas' => null,
            'descricao' => 'Plano básico para pessoa física com acesso limitado a recursos.',
        ]);

        $pessoaFisicaIntermediario = LicencaPlano::create([
            'nome' => 'Pessoa Física - Intermediário',
            'tipo' => 'pessoa_fisica',
            'tier' => 'intermediario',
            'preco_mensal' => 197.00,
            'preco_anual' => 1892.00, // 20% desconto anual
            'max_usuarios' => 1,
            'max_segmentos' => 2,
            'max_empresas' => null,
            'descricao' => 'Plano intermediário para pessoa física com recursos avançados.',
        ]);

        $pessoaFisicaAvancado = LicencaPlano::create([
            'nome' => 'Pessoa Física - Avançado',
            'tipo' => 'pessoa_fisica',
            'tier' => 'avancado',
            'preco_mensal' => 347.00,
            'preco_anual' => 3332.00, // 20% desconto anual
            'max_usuarios' => 1,
            'max_segmentos' => 4,
            'max_empresas' => null,
            'descricao' => 'Plano avançado para pessoa física com todos os recursos.',
        ]);

        // Planos para Empresa
        $empresaBasico = LicencaPlano::create([
            'nome' => 'Empresa - Básico',
            'tipo' => 'empresa',
            'tier' => 'basico',
            'preco_mensal' => 297.00,
            'preco_anual' => 2852.00, // 20% desconto anual
            'max_usuarios' => 3, // 1 master + 2 dependentes
            'max_segmentos' => 1,
            'max_empresas' => null,
            'descricao' => 'Plano básico para empresas com acesso limitado a recursos.',
        ]);

        $empresaIntermediario = LicencaPlano::create([
            'nome' => 'Empresa - Intermediário',
            'tipo' => 'empresa',
            'tier' => 'intermediario',
            'preco_mensal' => 597.00,
            'preco_anual' => 5732.00, // 20% desconto anual
            'max_usuarios' => 6, // 1 master + 5 dependentes
            'max_segmentos' => 3,
            'max_empresas' => null,
            'descricao' => 'Plano intermediário para empresas com recursos avançados.',
        ]);

        $empresaAvancado = LicencaPlano::create([
            'nome' => 'Empresa - Avançado',
            'tipo' => 'empresa',
            'tier' => 'avancado',
            'preco_mensal' => 997.00,
            'preco_anual' => 9572.00, // 20% desconto anual
            'max_usuarios' => 11, // 1 master + 10 dependentes
            'max_segmentos' => null, // ilimitado
            'max_empresas' => null,
            'descricao' => 'Plano avançado para empresas com todos os recursos.',
        ]);

        // Planos para Grupo Empresarial
        $grupoBasico = LicencaPlano::create([
            'nome' => 'Grupo Empresarial - Básico',
            'tipo' => 'grupo',
            'tier' => 'basico',
            'preco_mensal' => 1997.00,
            'preco_anual' => 19172.00, // 20% desconto anual
            'max_usuarios' => 16, // Até 5 por empresa (3 empresas)
            'max_segmentos' => null, // ilimitado
            'max_empresas' => 3,
            'descricao' => 'Plano básico para grupos empresariais.',
        ]);

        $grupoIntermediario = LicencaPlano::create([
            'nome' => 'Grupo Empresarial - Intermediário',
            'tipo' => 'grupo',
            'tier' => 'intermediario',
            'preco_mensal' => 3997.00,
            'preco_anual' => 38372.00, // 20% desconto anual
            'max_usuarios' => 51, // Até 10 por empresa (5 empresas) + 1 admin auxiliar
            'max_segmentos' => null, // ilimitado
            'max_empresas' => 5,
            'descricao' => 'Plano intermediário para grupos empresariais.',
        ]);

        $grupoAvancado = LicencaPlano::create([
            'nome' => 'Grupo Empresarial - Avançado',
            'tipo' => 'grupo',
            'tier' => 'avancado',
            'preco_mensal' => 6997.00,
            'preco_anual' => 67172.00, // 20% desconto anual
            'max_usuarios' => null, // ilimitado
            'max_segmentos' => null, // ilimitado
            'max_empresas' => null, // ilimitado
            'descricao' => 'Plano avançado para grupos empresariais com todos os recursos.',
        ]);

        // Associar recursos aos planos
        $this->associarRecursosPlanos([
            $pessoaFisicaBasico->id => ['alertas_basicos', 'propostas_basico'],
            $pessoaFisicaIntermediario->id => ['alertas_ilimitados', 'propostas_ilimitado', 'classificacao_relevancia'],
            $pessoaFisicaAvancado->id => ['alertas_ilimitados', 'propostas_ilimitado', 'classificacao_relevancia', 'analise_concorrencia', 'relatorios_avancados', 'exportacao_dados'],
            $empresaBasico->id => ['alertas_basicos', 'propostas_basico', 'dashboard_empresa'],
            $empresaIntermediario->id => ['alertas_ilimitados', 'propostas_ilimitado', 'dashboard_empresa', 'gerenciamento_tarefas', 'relatorios_empresa', 'classificacao_relevancia'],
            $empresaAvancado->id => ['alertas_ilimitados', 'propostas_ilimitado', 'dashboard_empresa', 'gerenciamento_tarefas', 'relatorios_empresa', 'classificacao_relevancia', 'analise_concorrencia', 'relatorios_avancados', 'exportacao_dados', 'integracao_sistemas'],
            $grupoBasico->id => ['alertas_ilimitados', 'propostas_ilimitado', 'dashboard_empresa', 'gerenciamento_tarefas', 'relatorios_empresa', 'dashboard_grupo'],
            $grupoIntermediario->id => ['alertas_ilimitados', 'propostas_ilimitado', 'dashboard_empresa', 'gerenciamento_tarefas', 'relatorios_empresa', 'classificacao_relevancia', 'analise_concorrencia', 'relatorios_avancados', 'dashboard_grupo', 'relatorios_grupo'],
            $grupoAvancado->id => ['alertas_ilimitados', 'propostas_ilimitado', 'dashboard_empresa', 'gerenciamento_tarefas', 'relatorios_empresa', 'classificacao_relevancia', 'analise_concorrencia', 'relatorios_avancados', 'exportacao_dados', 'integracao_sistemas', 'dashboard_grupo', 'relatorios_grupo', 'api_dedicada'],
        ]);
    }

    /**
     * Associar recursos aos planos.
     *
     * @param array $planoRecursos
     * @return void
     */
    private function associarRecursosPlanos(array $planoRecursos)
    {
        foreach ($planoRecursos as $planoId => $recursosNomes) {
            $plano = LicencaPlano::find($planoId);
            $recursosIds = LicencaRecurso::whereIn('nome', $recursosNomes)->pluck('id');
            $plano->recursos()->attach($recursosIds);
        }
    }
}
