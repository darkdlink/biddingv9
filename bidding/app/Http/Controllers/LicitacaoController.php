<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Services\PncpApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LicitacaoController extends Controller
{
    protected $pncpApiService;

    public function __construct(PncpApiService $pncpApiService)
    {
        $this->pncpApiService = $pncpApiService;
    }

    public function index(Request $request)
    {
        return view('licitacoes.index');
    }

    public function sincronizar(Request $request)
    {
        try {
            Log::info('Iniciando sincronização de licitações');

            // Data padrão: próximos 3 meses
            $dataFinal = Carbon::now()->addMonths(3)->format('Ymd');

            $params = [
                'dataFinal' => $dataFinal,
                'pagina' => 1,
                'tamanhoPagina' => 20
            ];

            if ($request->has('uf') && !empty($request->uf)) {
                $params['uf'] = $request->uf;
            }

            if ($request->has('codigoModalidadeContratacao') && !empty($request->codigoModalidadeContratacao)) {
                $params['codigoModalidadeContratacao'] = $request->codigoModalidadeContratacao;
            }

            $resultado = $this->pncpApiService->consultarLicitacoesAbertas($params);

            // Analisar relevância das licitações
            $resultadoAnalise = $this->pncpApiService->analisarRelevanciaLicitacoes();

            // Enviar alertas
            $resultadoAlertas = $this->pncpApiService->enviarAlertasLicitacoes();

            $totalRegistros = $resultado['paginacao']['totalRegistros'] ?? 0;

            // Verificar se as licitações foram salvas
            $countAfter = Licitacao::count();
            Log::info('Total de licitações após sincronização: ' . $countAfter);

            Log::info('Sincronização concluída com sucesso. Total de registros na API: ' . $totalRegistros);

            return response()->json([
                'success' => true,
                'message' => 'Sincronização realizada com sucesso!',
                'total_registros' => $totalRegistros,
                'licitacoes_salvas' => $countAfter,
                'licitacoes_analisadas' => $resultadoAnalise['total_analisadas'] ?? 0,
                'alertas_enviados' => $resultadoAlertas['total_enviados'] ?? 0
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar licitações: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao sincronizar licitações: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $licitacao = Licitacao::findOrFail($id);

        return view('licitacoes.show', [
            'licitacao' => $licitacao
        ]);
    }

    public function marcarInteresse(Request $request, $id)
    {
        $licitacao = Licitacao::findOrFail($id);
        $licitacao->interesse = $request->interesse ? true : false;
        $licitacao->save();

        return response()->json([
            'success' => true,
            'message' => 'Status de interesse atualizado com sucesso!'
        ]);
    }

    public function marcarAnalisada(Request $request, $id)
    {
        $licitacao = Licitacao::findOrFail($id);
        $licitacao->analisada = $request->analisada ? true : false;
        $licitacao->save();

        return response()->json([
            'success' => true,
            'message' => 'Status de análise atualizado com sucesso!'
        ]);
    }
}
