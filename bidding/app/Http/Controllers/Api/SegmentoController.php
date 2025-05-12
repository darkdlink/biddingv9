<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Segmento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SegmentoController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */ // <--- ADICIONE ISSO
        $user = Auth::user();

        // Se for admin, pode ver todos os segmentos
        if ($user->isAdmin()) {
            $query = Segmento::with(['user', 'empresa']);
        }
        // Se for usuário master, vê segmentos da empresa
        elseif ($user->isUsuarioMaster() && $user->empresa_id) {
            $query = Segmento::where('empresa_id', $user->empresa_id);
        }
        // Caso contrário, vê apenas seus segmentos
        else {
            $query = $user->segmentos();
        }

        // Aplicar filtros
        if ($request->has('termo') && !empty($request->termo)) {
            $query->where(function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->termo . '%')
                  ->orWhere('descricao', 'like', '%' . $request->termo . '%');
            });
        }

        // Ordenação
        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginação
        $perPage = $request->get('per_page', 10);
        $segmentos = $query->paginate($perPage);

        return response()->json($segmentos);
    }

    public function store(Request $request)
    {

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'palavras_chave' => 'required|array|min:1',
            'palavras_chave.*' => 'required|string|max:50',
        ]);
        /** @var \App\Models\User $user */ // <--- ADICIONE ISSO
        $user = Auth::user();

        // Verificar limite de segmentos pelo plano
        if (!$user->isAdmin()) {
            $plano = $user->licenca->plano ?? null;

            if ($plano && $plano->max_segmentos) {
                $segmentosAtuais = $user->segmentos()->count();

                if ($segmentosAtuais >= $plano->max_segmentos) {
                    return response()->json([
                        'message' => 'Você atingiu o limite de segmentos do seu plano. Considere fazer um upgrade para adicionar mais segmentos.'
                    ], 422);
                }
            }
        }

        // Criar segmento
        $segmento = new Segmento();
        $segmento->nome = $request->nome;
        $segmento->descricao = $request->descricao;
        $segmento->palavras_chave = $request->palavras_chave;

        // Se for usuário master, associar à empresa
        if ($user->isUsuarioMaster() && $user->empresa_id) {
            $segmento->empresa_id = $user->empresa_id;
        } else {
            $segmento->user_id = $user->id;
        }

        $segmento->save();

        // Associar segmento ao usuário
        if (!$user->segmentos()->where('segmento_id', $segmento->id)->exists()) {
            $user->segmentos()->attach($segmento->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Segmento criado com sucesso!',
            'segmento' => $segmento
        ]);
    }

    public function show($id)
    {
        $segmento = Segmento::findOrFail($id);
        /** @var \App\Models\User $user */ // <--- ADICIONE ISSO
        $user = Auth::user();

        // Verificar se o usuário tem acesso a este segmento
        if (!$user->isAdmin() &&
            $segmento->user_id !== $user->id &&
            $segmento->empresa_id !== $user->empresa_id &&
            !$user->segmentos()->where('segmento_id', $segmento->id)->exists()) {

            return response()->json([
                'message' => 'Você não tem permissão para acessar este segmento'
            ], 403);
        }

        return response()->json($segmento);
    }

    public function update(Request $request, $id)
    {
        $segmento = Segmento::findOrFail($id);
        /** @var \App\Models\User $user */ // <--- ADICIONE ISSO
        $user = Auth::user();

        // Verificar se o usuário tem permissão
        if (!$user->isAdmin() &&
            $segmento->user_id !== $user->id &&
            ($user->isUsuarioMaster() && $segmento->empresa_id !== $user->empresa_id)) {

            return response()->json([
                'message' => 'Você não tem permissão para editar este segmento'
            ], 403);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'palavras_chave' => 'required|array|min:1',
            'palavras_chave.*' => 'required|string|max:50',
        ]);

        // Atualizar segmento
        $segmento->nome = $request->nome;
        $segmento->descricao = $request->descricao;
        $segmento->palavras_chave = $request->palavras_chave;
        $segmento->save();

        return response()->json([
            'success' => true,
            'message' => 'Segmento atualizado com sucesso!',
            'segmento' => $segmento
        ]);
    }

    public function destroy($id)
    {
        $segmento = Segmento::findOrFail($id);
        /** @var \App\Models\User $user */ // <--- ADICIONE ISSO
        $user = Auth::user();

        // Verificar se o usuário tem permissão
        if (!$user->isAdmin() &&
            $segmento->user_id !== $user->id &&
            ($user->isUsuarioMaster() && $segmento->empresa_id !== $user->empresa_id)) {

            return response()->json([
                'message' => 'Você não tem permissão para excluir este segmento'
            ], 403);
        }

        // Iniciar transação
        \DB::beginTransaction();

        try {
            // Desassociar o segmento de todas as licitações
            $segmento->licitacoes()->detach();

            // Desassociar o segmento de todos os usuários
            $segmento->users()->detach();

            // Excluir segmento
            $segmento->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Segmento excluído com sucesso!'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir segmento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function licitacoes($id, Request $request)
    {
        $segmento = Segmento::findOrFail($id);
        /** @var \App\Models\User $user */ // <--- ADICIONE ISSO
        $user = Auth::user();

        // Verificar se o usuário tem acesso a este segmento
        if (!$user->isAdmin() &&
            $segmento->user_id !== $user->id &&
            $segmento->empresa_id !== $user->empresa_id &&
            !$user->segmentos()->where('segmento_id', $segmento->id)->exists()) {

            return response()->json([
                'message' => 'Você não tem permissão para acessar este segmento'
            ], 403);
        }

        // Consultar licitações relevantes para o segmento
        $query = $segmento->licitacoes();

// Aplicar filtros
        if ($request->has('data_min') && !empty($request->data_min)) {
            $query->where('data_encerramento_proposta', '>=', $request->data_min);
        } else {
            // Por padrão, mostrar apenas licitações não encerradas
            $query->where('data_encerramento_proposta', '>=', now());
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

        if ($request->has('relevancia_min') && !empty($request->relevancia_min)) {
            $query->wherePivot('relevancia', '>=', $request->relevancia_min);
        }

        // Ordenação
        $sortField = $request->get('sort', 'data_encerramento_proposta');
        $sortDirection = $request->get('direction', 'asc');

        if ($sortField === 'relevancia') {
            $query->orderByPivot('relevancia', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Paginação
        $perPage = $request->get('per_page', 10);
        $licitacoes = $query->paginate($perPage);

        return response()->json($licitacoes);
    }
}
