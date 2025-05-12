<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PncpApiService;
use Illuminate\Support\Facades\Log;

class SincronizarLicitacoes extends Command
{
    protected $signature = 'licitacoes:sincronizar {--uf=} {--paginas=5} {--tamanho=50}';
    protected $description = 'Sincroniza licitações do Portal Nacional de Contratações Públicas';

    protected $pncpApiService;

    public function __construct(PncpApiService $pncpApiService)
    {
        parent::__construct();
        $this->pncpApiService = $pncpApiService;
    }

    public function handle()
    {
        $this->info('Iniciando sincronização de licitações...');
        Log::info('Comando de sincronização de licitações iniciado');

        $uf = $this->option('uf');
        $paginas = (int) $this->option('paginas');
        $tamanho = (int) $this->option('tamanho');

        $bar = $this->output->createProgressBar($paginas);
        $bar->start();

        $totalLicitacoes = 0;

        try {
            for ($pagina = 1; $pagina <= $paginas; $pagina++) {
                $params = [
                    'pagina' => $pagina,
                    'tamanhoPagina' => $tamanho,
                    'dataFinal' => now()->addMonths(3)->format('Ymd')
                ];

                if ($uf) {
                    $params['uf'] = $uf;
                }

                $resultado = $this->pncpApiService->consultarLicitacoesAbertas($params);
                $totalLicitacoes += count($resultado['licitacoes'] ?? []);

                $bar->advance();
                sleep(1); // Pausa para evitar sobrecarga na API
            }

            $bar->finish();
            $this->newLine();

            // Após sincronizar, analisar relevância
            $this->info('Analisando relevância das licitações...');
            $analiseResultado = $this->pncpApiService->analisarRelevanciaLicitacoes();

            // Enviar alertas se necessário
            $this->info('Enviando alertas para usuários...');
            $alertasResultado = $this->pncpApiService->enviarAlertasLicitacoes();

            $this->info('Sincronização concluída!');
            $this->info("Total de licitações recuperadas: {$totalLicitacoes}");
            $this->info("Total de licitações analisadas: {$analiseResultado['total_analisadas']}");
            $this->info("Total de alertas enviados: {$alertasResultado['total_enviados']}");

            Log::info('Comando de sincronização concluído com sucesso');
            Log::info("Licitações recuperadas: {$totalLicitacoes}");
            Log::info("Licitações analisadas: {$analiseResultado['total_analisadas']}");
            Log::info("Alertas enviados: {$alertasResultado['total_enviados']}");

            return 0;
        } catch (\Exception $e) {
            $this->error('Erro durante a sincronização: ' . $e->getMessage());
            Log::error('Erro no comando de sincronização: ' . $e->getMessage());
            return 1;
        }
    }
}
