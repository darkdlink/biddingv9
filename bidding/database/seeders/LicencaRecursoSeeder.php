<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LicencaRecurso;

class LicencaRecursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $recursos = [
            [
                'nome' => 'alertas_basicos',
                'categoria' => 'notificacao',
                'descricao' => 'Alertas básicos (limite de 10 por mês)',
            ],
            [
                'nome' => 'alertas_ilimitados',
                'categoria' => 'notificacao',
                'descricao' => 'Alertas ilimitados',
            ],
            [
                'nome' => 'propostas_basico',
                'categoria' => 'proposta',
                'descricao' => 'Armazenamento limitado de propostas (até 20)',
            ],
            [
                'nome' => 'propostas_ilimitado',
                'categoria' => 'proposta',
                'descricao' => 'Armazenamento ilimitado de propostas',
            ],
            [
                'nome' => 'classificacao_relevancia',
                'categoria' => 'analise',
                'descricao' => 'Classificação de relevância de licitações',
            ],
            [
                'nome' => 'analise_concorrencia',
                'categoria' => 'analise',
                'descricao' => 'Análise de concorrência',
            ],
            [
                'nome' => 'relatorios_avancados',
                'categoria' => 'relatorio',
                'descricao' => 'Relatórios avançados',
            ],
            [
                'nome' => 'exportacao_dados',
                'categoria' => 'exportacao',
                'descricao' => 'Exportação de dados',
            ],
            [
                'nome' => 'dashboard_empresa',
                'categoria' => 'dashboard',
                'descricao' => 'Dashboard unificado para empresa',
            ],
            [
                'nome' => 'gerenciamento_tarefas',
                'categoria' => 'colaboracao',
                'descricao' => 'Atribuição de tarefas entre usuários',
            ],
            [
                'nome' => 'relatorios_empresa',
                'categoria' => 'relatorio',
                'descricao' => 'Relatórios consolidados da empresa',
            ],
            [
                'nome' => 'integracao_sistemas',
                'categoria' => 'integracao',
                'descricao' => 'Integração com outros sistemas',
            ],
            [
                'nome' => 'dashboard_grupo',
                'categoria' => 'dashboard',
                'descricao' => 'Dashboard consolidado do grupo',
            ],
            [
                'nome' => 'relatorios_grupo',
                'categoria' => 'relatorio',
                'descricao' => 'Relatórios comparativos entre empresas',
            ],
            [
                'nome' => 'api_dedicada',
                'categoria' => 'integracao',
                'descricao' => 'API dedicada',
            ],
        ];

        foreach ($recursos as $recurso) {
            LicencaRecurso::create($recurso);
        }
    }
}
