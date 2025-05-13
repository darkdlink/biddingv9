<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\Segmento;
use App\Services\PncpApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LicitacaoController extends Controller
{
    protected $pncpApiService;

    public function __construct(PncpApiService $pncpApiService)
    {
        $this->pncpApiService = $pncpApiService;
    }

    /**
     * Exibe a lista de licitações com filtros opcionais
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            // Iniciar a query de licitações
            $query = Licitacao::query();

            // Aplicar filtros se fornecidos
            if ($request->has('uf') && !empty($request->uf)) {
                $query->where('uf', $request->uf);
            }

            if ($request->has('modalidade') && !empty($request->modalidade)) {
                $query->where('modalidade_nome', $request->modalidade);
            }

            if ($request->has('data_min') && !empty($request->data_min)) {
                $query->where('data_encerramento_proposta', '>=', $request->data_min);
            }

            if ($request->has('data_max') && !empty($request->data_max)) {
                $query->where('data_encerramento_proposta', '<=', $request->data_max);
            }

            if ($request->has('valor_min') && !empty($request->valor_min)) {
                $query->where('valor_total_estimado', '>=', $request->valor_min);
            }

            if ($request->has('valor_max') && !empty($request->valor_max)) {
                $query->where('valor_total_estimado', '<=', $request->valor_max);
            }

            if ($request->has('interesse') && $request->interesse !== '') {
                $query->where('interesse', $request->interesse == '1');
            }

            if ($request->has('termo') && !empty($request->termo)) {
                $termo = $request->termo;
                $query->where(function($q) use ($termo) {
                    $q->where('objeto_compra', 'like', '%' . $termo . '%')
                      ->orWhere('orgao_entidade', 'like', '%' . $termo . '%')
                      ->orWhere('numero_controle_pncp', 'like', '%' . $termo . '%');
                });
            }

            // Filtro por segmento
            if ($request->has('segmento_id') && !empty($request->segmento_id)) {
                if ($request->segmento_id === 'relevantes') {
                    // Buscar licitações relevantes para todos os segmentos do usuário
                    $segmentosIds = Auth::user()->segmentos()->pluck('segmentos.id')->toArray();
                    if (!empty($segmentosIds)) {
                        $query->whereHas('segmentos', function($q) use ($segmentosIds) {
                            $q->whereIn('segmento_id', $segmentosIds);
                        });
                    }
                } else {
                    // Buscar licitações para um segmento específico
                    $query->whereHas('segmentos', function($q) use ($request) {
                        $q->where('segmento_id', $request->segmento_id);
                    });
                }
            }

            // Ordenação
            $sortField = $request->get('sort', 'data_encerramento_proposta');
            $sortDirection = $request->get('direction', 'asc');

            $query->orderBy($sortField, $sortDirection);

            // Executar a consulta com paginação
            $licitacoes = $query->paginate(15);

            // Buscar estatísticas para os cards
            $totalLicitacoes = Licitacao::count();
            $totalInteresse = Licitacao::where('interesse', true)->count();
            $totalAbertas = Licitacao::where('data_encerramento_proposta', '>=', now())->count();

            // Licitações relevantes para segmentos do usuário
            $segmentosIds = Auth::user()->segmentos()->pluck('segmentos.id')->toArray();
            $totalRelevantes = 0;

            if (!empty($segmentosIds)) {
                $totalRelevantes = Licitacao::whereHas('segmentos', function($q) use ($segmentosIds) {
                    $q->whereIn('segmento_id', $segmentosIds);
                })->count();
            }

            // Buscar UFs e modalidades disponíveis para os filtros
            $ufs = Licitacao::select('uf')
                        ->whereNotNull('uf')
                        ->distinct()
                        ->orderBy('uf')
                        ->pluck('uf')
                        ->toArray();

            $modalidades = Licitacao::select('modalidade_nome')
                        ->whereNotNull('modalidade_nome')
                        ->distinct()
                        ->orderBy('modalidade_nome')
                        ->pluck('modalidade_nome')
                        ->toArray();

            // Buscar segmentos do usuário
            $segmentos = Auth::user()->segmentos;

            // Retornar a view com todos os dados
            return view('licitacoes.index', compact(
                'licitacoes',
                'totalLicitacoes',
                'totalInteresse',
                'totalAbertas',
                'totalRelevantes',
                'ufs',
                'modalidades',
                'segmentos'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao listar licitações: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Em caso de erro, retornar uma coleção vazia e registrar o erro
            return view('licitacoes.index', [
                'licitacoes' => collect()->paginate(15),
                'totalLicitacoes' => 0,
                'totalInteresse' => 0,
                'totalAbertas' => 0,
                'totalRelevantes' => 0,
                'ufs' => [],
                'modalidades' => [],
                'segmentos' => [],
                'error' => 'Erro ao buscar licitações. Por favor, tente novamente.'
            ])->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Exibe os detalhes de uma licitação específica
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $licitacao = Licitacao::with(['propostas', 'segmentos', 'acompanhamentos'])
                ->findOrFail($id);

            return view('licitacoes.show', [
                'licitacao' => $licitacao
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao exibir licitação: ' . $e->getMessage(), [
                'licitacao_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('licitacoes.index')
                ->withErrors(['error' => 'Licitação não encontrada ou erro ao carregar detalhes.']);
        }
    }

    /**
     * Sincroniza licitações com o Portal Nacional de Contratações Públicas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sincronizar(Request $request)
    {
        try {
            Log::info('Iniciando sincronização de licitações', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name
            ]);

            // Data padrão: próximos 3 meses
            $dataFinal = Carbon::now()->addMonths(3)->format('Ymd');

            $params = [
                'dataFinal' => $dataFinal,
                'pagina' => 1,
                'tamanhoPagina' => 50 // Aumentado para trazer mais licitações por vez
            ];

            // Adicionar UF se fornecida
            if ($request->has('uf') && !empty($request->uf)) {
                $params['uf'] = $request->uf;
            }

            // Executar sincronização
            $resultado = $this->pncpApiService->consultarLicitacoesAbertas($params);

            // Analisar relevância das licitações para segmentos
            $this->pncpApiService->analisarRelevanciaLicitacoes();

            // Enviar alertas para usuários com segmentos relevantes
            $this->pncpApiService->enviarAlertasLicitacoes();

            $totalRegistros = $resultado['paginacao']['totalRegistros'] ?? 0;
            $licitacoesSalvas = $resultado['licitacoes_processadas'] ?? count($resultado['licitacoes'] ?? []);

            Log::info('Sincronização concluída com sucesso', [
                'total_registros' => $totalRegistros,
                'licitacoes_salvas' => $licitacoesSalvas
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sincronização realizada com sucesso!',
                'total_registros' => $totalRegistros,
                'licitacoes_salvas' => $licitacoesSalvas
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar licitações: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao sincronizar licitações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marca uma licitação como de interesse ou remove o interesse
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarInteresse(Request $request, $id)
    {
        try {
            $licitacao = Licitacao::findOrFail($id);

            // Definir o valor do interesse baseado no que foi enviado ou alternar o valor atual
            $interesse = $request->has('interesse') ? (bool)$request->interesse : !$licitacao->interesse;

            $licitacao->interesse = $interesse;
            $licitacao->save();

            // Registrar no log
            Log::info('Interesse em licitação atualizado', [
                'user_id' => Auth::id(),
                'licitacao_id' => $id,
                'interesse' => $interesse
            ]);

            // Se marcou como interesse, cria um acompanhamento automaticamente
            if ($interesse && method_exists($licitacao, 'acompanhamentos')) {
                $licitacao->acompanhamentos()->create([
                    'user_id' => Auth::id(),
                    'titulo' => 'Marcada como de interesse',
                    'descricao' => 'Licitação marcada como de interesse pelo usuário.',
                    'tipo' => 'anotacao',
                    'data_evento' => now(),
                    'is_public' => true,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $interesse ? 'Licitação marcada como de interesse' : 'Licitação removida dos interesses',
                'interesse' => $interesse
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao marcar interesse: ' . $e->getMessage(), [
                'licitacao_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar interesse: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marca uma licitação como analisada ou não analisada
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarAnalisada(Request $request, $id)
    {
        try {
            $licitacao = Licitacao::findOrFail($id);

            // Definir o valor de analisada baseado no que foi enviado ou alternar o valor atual
            $analisada = $request->has('analisada') ? (bool)$request->analisada : !$licitacao->analisada;

            $licitacao->analisada = $analisada;
            $licitacao->save();

            return response()->json([
                'success' => true,
                'message' => $analisada ? 'Licitação marcada como analisada' : 'Licitação marcada como não analisada',
                'analisada' => $analisada
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao marcar como analisada: ' . $e->getMessage(), [
                'licitacao_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status de análise: ' . $e->getMessage()
            ], 500);
        }
    }
}
