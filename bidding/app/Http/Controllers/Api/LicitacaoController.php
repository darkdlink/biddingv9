<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Licitacao;
use App\Services\PncpApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicitacaoController extends Controller
{
    protected $pncpApiService;

    public function __construct(PncpApiService $pncpApiService)
    {
        $this->pncpApiService = $pncpApiService;
    }

    public function index(Request $request)
    {
        $query = Licitacao::query();

        // Filtros
        if ($request->has('uf') && !empty($request->uf)) {
            $query->where('uf', $request->uf);
        }

        if ($request->has('modalidade') && !empty($request->modalidade)) {
            $query->where('modalidade_nome', 'like', '%' . $request->modalidade . '%');
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

        if ($request->has('interesse')) {
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
            $query->whereHas('segmentos', function($q) use ($request) {
                $q->where('segmento_id', $request->segmento_id);
            });
        }

        // Ordenação
        $sortField = $request->get('sort', 'data_encerramento_proposta');
        $sortDirection = $request->get('direction', 'asc');

        $query->orderBy($sortField, $sortDirection);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $licitacoes = $query->paginate($perPage);

        return response()->json($licitacoes);
    }

    public function show($id)
    {
        $licitacao = Licitacao::with(['propostas', 'segmentos', 'acompanhamentos'])
            ->findOrFail($id);

        return response()->json($licitacao);
    }

    public function sincronizar(Request $request)
    {
        try {
            // Data padrão: próximos 3 meses
            $dataFinal = now()->addMonths(3)->format('Ymd');

            $params = [
                'dataFinal' => $dataFinal,
                'pagina' => $request->get('pagina', 1),
                'tamanhoPagina' => $request->get('tamanho_pagina', 20)
            ];

            if ($request->has('uf') && !empty($request->uf)) {
                $params['uf'] = $request->uf;
            }

            $resultado = $this->pncpApiService->consultarLicitacoesAbertas($params);

            // Analisar relevância
            $this->pncpApiService->analisarRelevanciaLicitacoes();

            // Enviar alertas
            $this->pncpApiService->enviarAlertasLicitacoes();

            return response()->json([
                'success' => true,
                'message' => 'Sincronização realizada com sucesso!',
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao sincronizar licitações: ' . $e->getMessage()
            ], 500);
        }
    }

    public function marcarInteresse(Request $request, $id)
    {
        $licitacao = Licitacao::findOrFail($id);
        $licitacao->interesse = $request->interesse ? true : false;
        $licitacao->save();

        return response()->json([
            'success' => true,
            'message' => 'Status de interesse atualizado com sucesso!',
            'data' => $licitacao
        ]);
    }
}
