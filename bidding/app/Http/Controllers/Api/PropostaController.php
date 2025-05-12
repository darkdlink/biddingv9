<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposta;
use App\Models\PropostaArquivo;
use App\Models\PropostaVersao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropostaController extends Controller
{
    public function index(Request $request)
    {
        $query = Proposta::where('user_id', Auth::id())
            ->with('licitacao');

        // Filtros
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('licitacao_id') && !empty($request->licitacao_id)) {
            $query->where('licitacao_id', $request->licitacao_id);
        }

        // Ordenação
        $sortField = $request->get('sort', 'updated_at');
        $sortDirection = $request->get('direction', 'desc');

        $query->orderBy($sortField, $sortDirection);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $propostas = $query->paginate($perPage);

        return response()->json($propostas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'licitacao_id' => 'required|exists:licitacoes,id',
            'titulo' => 'required|string|max:255',
            'valor_proposta' => 'required|numeric|min:0',
            'conteudo' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ]);

        // Iniciar transação
        \DB::beginTransaction();

        try {
            // Criar a proposta
            $proposta = Proposta::create([
                'licitacao_id' => $request->licitacao_id,
                'user_id' => Auth::id(),
                'titulo' => $request->titulo,
                'valor_proposta' => $request->valor_proposta,
                'status' => 'rascunho',
                'conteudo' => $request->conteudo,
                'observacoes' => $request->observacoes,
            ]);

            // Criar primeira versão
            PropostaVersao::create([
                'proposta_id' => $proposta->id,
                'versao' => 1,
                'conteudo' => $request->conteudo,
                'valor_proposta' => $request->valor_proposta,
                'user_id' => Auth::id(),
            ]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proposta criada com sucesso!',
                'data' => $proposta->load('licitacao')
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar proposta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $proposta = Proposta::with(['licitacao', 'versoes', 'arquivos'])
            ->findOrFail($id);

        // Verificar permissão
        if ($proposta->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }

        return response()->json($proposta);
    }

    public function update(Request $request, $id)
    {
        $proposta = Proposta::findOrFail($id);

        // Verificar permissão
        if ($proposta->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }

        // Verificar se proposta está em rascunho
        if (!$proposta->isRascunho()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível editar uma proposta que já foi submetida.'
            ], 400);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'valor_proposta' => 'required|numeric|min:0',
            'conteudo' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ]);

        // Iniciar transação
        \DB::beginTransaction();

        try {
            // Verificar se houve alteração no conteúdo ou valor para criar versão
            $criarVersao = $proposta->conteudo !== $request->conteudo ||
                           $proposta->valor_proposta != $request->valor_proposta;

            // Atualizar proposta
            $proposta->update([
                'titulo' => $request->titulo,
                'valor_proposta' => $request->valor_proposta,
                'conteudo' => $request->conteudo,
                'observacoes' => $request->observacoes,
            ]);

            // Criar nova versão se necessário
            if ($criarVersao) {
                $ultimaVersao = $proposta->versoes()->orderBy('versao', 'desc')->first();
                $versao = $ultimaVersao ? $ultimaVersao->versao + 1 : 1;

                PropostaVersao::create([
                    'proposta_id' => $proposta->id,
                    'versao' => $versao,
                    'conteudo' => $request->conteudo,
                    'valor_proposta' => $request->valor_proposta,
                    'user_id' => Auth::id(),
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proposta atualizada com sucesso!',
                'data' => $proposta->fresh(['licitacao', 'versoes', 'arquivos'])
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar proposta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $proposta = Proposta::findOrFail($id);

        // Verificar permissão
        if ($proposta->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }

        // Verificar se proposta está em rascunho
        if (!$proposta->isRascunho()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir uma proposta que já foi submetida.'
            ], 400);
        }

        try {
            // Excluir arquivos do storage
            foreach ($proposta->arquivos as $arquivo) {
                Storage::disk('public')->delete($arquivo->caminho);
            }

            // Excluir proposta
            $proposta->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proposta excluída com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir proposta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submeter($id)
    {
        $proposta = Proposta::findOrFail($id);

        // Verificar permissão
        if ($proposta->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }

        // Verificar se proposta está em rascunho
        if (!$proposta->isRascunho()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta proposta já foi submetida.'
            ], 400);
        }

        try {
            $proposta->update([
                'status' => 'submetida',
                'data_submissao' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposta submetida com sucesso!',
                'data' => $proposta->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao submeter proposta: ' . $e->getMessage()
            ], 500);
        }
    }
}
