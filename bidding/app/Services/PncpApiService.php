<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Licitacao;
use App\Models\Segmento;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PncpApiService
{
    public function consultarLicitacoesAbertas($params = [])
    {
        try {
            // Limpar e configurar o log para debugging
            Log::info('==== INÍCIO DA SINCRONIZAÇÃO DE LICITAÇÕES ====');
            Log::info('Parâmetros recebidos: ' . json_encode($params));

            $client = new Client([
                'timeout' => 90, // Aumentar timeout para 90 segundos
                'http_errors' => false
            ]);

            // Data padrão: próximos 3 meses no formato YYYYMMDD
            $dataFormatada = Carbon::now()->addMonths(3)->format('Ymd');

            // Construir os parâmetros
            $queryParams = [
                'dataFinal' => $params['dataFinal'] ?? $dataFormatada,
                'pagina' => $params['pagina'] ?? 1,
                'tamanhoPagina' => $params['tamanhoPagina'] ?? 10
            ];

            // Construir a URL completa
            $url = 'https://pncp.gov.br/api/consulta/v1/contratacoes/proposta?';
            $url .= 'dataFinal=' . $queryParams['dataFinal'];
            $url .= '&pagina=' . $queryParams['pagina'];
            $url .= '&tamanhoPagina=' . $queryParams['tamanhoPagina'];

            Log::info('Consultando API com URL: ' . $url);

            // Realizar a requisição com a URL completa
            $response = $client->request('GET', $url);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            Log::info('Resposta da API recebida. Status: ' . $statusCode);

            if ($statusCode >= 400) {
                Log::error('Erro na API do PNCP. Status: ' . $statusCode);
                Log::error('Resposta: ' . substr($body, 0, 1000));
                throw new Exception('Erro na API do PNCP. Status: ' . $statusCode);
            }

            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Erro ao decodificar JSON: ' . json_last_error_msg());
                Log::error('Corpo da resposta: ' . substr($body, 0, 1000));
                throw new Exception('Resposta inválida da API (JSON inválido)');
            }

            Log::info('JSON decodificado com sucesso. Verificando dados...');

            if (!isset($data['data']) || empty($data['data'])) {
                Log::warning('Nenhum dado encontrado na resposta da API');
                return [
                    'licitacoes' => [],
                    'paginacao' => [
                        'totalRegistros' => 0,
                        'totalPaginas' => 0,
                        'paginaAtual' => $queryParams['pagina']
                    ]
                ];
            }

            Log::info('Dados encontrados na resposta. Total de registros: ' . count($data['data']));

            // Testar processamento de uma licitação como exemplo
            if (count($data['data']) > 0) {
                $exemplo = $data['data'][0];
                Log::info('Exemplo de licitação: ' . json_encode($exemplo));
            }

            // Tenta processar as licitações dentro de uma transação
            $processados = $this->processarLicitacoes($data['data']);

            Log::info('Licitações processadas: ' . $processados);
            Log::info('==== FIM DA SINCRONIZAÇÃO DE LICITAÇÕES ====');

            return [
                'licitacoes' => $data['data'],
                'paginacao' => [
                    'totalRegistros' => $data['totalRegistros'] ?? 0,
                    'totalPaginas' => $data['totalPaginas'] ?? 0,
                    'paginaAtual' => $queryParams['pagina']
                ]
            ];
        } catch (Exception $e) {
            Log::error('ERRO CRÍTICO ao consultar licitações: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
    // ...

    // Adicionar método para analisar relevância automática de licitações
    public function analisarRelevanciaLicitacoes($licitacaoIds = null)
    {
        try {
            Log::info('Iniciando análise de relevância para segmentos');

            // Se não especificar IDs, analisa licitações não analisadas
            $query = Licitacao::query();
            if ($licitacaoIds) {
                $query->whereIn('id', $licitacaoIds);
            } else {
                $query->where('analisada', false);
            }

            $licitacoes = $query->get();
            $segmentos = Segmento::all();

            Log::info('Analisando ' . $licitacoes->count() . ' licitações para ' . $segmentos->count() . ' segmentos');

            // Para cada licitação, analisar relevância para todos os segmentos
            foreach ($licitacoes as $licitacao) {
                DB::beginTransaction();

                try {
                    // Limpar associações anteriores se existirem
                    $licitacao->segmentos()->detach();

                    // Analisar relevância para cada segmento
                    $licitacao->analisarRelevancia($segmentos);

                    // Marcar licitação como analisada
                    $licitacao->analisada = true;
                    $licitacao->save();

                    DB::commit();
                    Log::info('Licitação ID ' . $licitacao->id . ' analisada com sucesso');
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Erro ao analisar licitação ID ' . $licitacao->id . ': ' . $e->getMessage());
                }
            }

            return [
                'success' => true,
                'message' => 'Análise de relevância concluída para ' . $licitacoes->count() . ' licitações',
                'total_analisadas' => $licitacoes->count()
            ];

        } catch (Exception $e) {
            Log::error('ERRO CRÍTICO na análise de relevância: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return [
                'success' => false,
                'message' => 'Erro ao analisar relevância: ' . $e->getMessage(),
            ];
        }
    }

    // Método para enviar alertas de novas licitações relevantes
    public function enviarAlertasLicitacoes()
    {
        try {
            Log::info('Iniciando envio de alertas de licitações');

            // Buscar licitações recentes relevantes
            $licitacoesRecentes = Licitacao::where('data_inclusao', '>=', Carbon::now()->subDays(1))
                ->where('analisada', true)
                ->whereHas('segmentos', function($query) {
                    $query->where('relevancia', '>=', 3); // Relevância mínima para alerta
                })
                ->get();

            $totalEnviados = 0;

            // Para cada licitação, buscar usuários interessados
            foreach ($licitacoesRecentes as $licitacao) {
                $segmentosIds = $licitacao->segmentos->pluck('id')->toArray();

                // Buscar usuários que têm acesso a esses segmentos
                $usuarios = \App\Models\User::whereHas('segmentos', function($query) use ($segmentosIds) {
                    $query->whereIn('segmento_id', $segmentosIds);
                })->get();

                // Enviar alerta para cada usuário
                foreach ($usuarios as $usuario) {
                    // Verificar limite de alertas do plano
                    $plano = $usuario->licenca->plano;

                    // Se plano básico pessoa física, verificar limite
                    if ($plano->isPessoaFisica() && $plano->tier === 'basico') {
                        $alertasEnviados = \App\Models\Alerta::where('user_id', $usuario->id)
                            ->whereMonth('created_at', now()->month)
                            ->count();

                        if ($alertasEnviados >= 10) {
                            continue; // Limite atingido
                        }
                    }

                    // Criar alerta
                    \App\Models\Alerta::create([
                        'user_id' => $usuario->id,
                        'licitacao_id' => $licitacao->id,
                        'titulo' => 'Nova licitação relevante: ' . $licitacao->objeto_compra,
                        'conteudo' => 'Encontramos uma nova licitação relevante para seus segmentos de interesse.',
                        'lido' => false,
                    ]);

                    $totalEnviados++;
                }
            }

            Log::info('Alertas enviados: ' . $totalEnviados);

            return [
                'success' => true,
                'message' => 'Alertas enviados com sucesso',
                'total_enviados' => $totalEnviados
            ];

        } catch (Exception $e) {
            Log::error('ERRO ao enviar alertas: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Erro ao enviar alertas: ' . $e->getMessage(),
            ];
        }
    }
}
