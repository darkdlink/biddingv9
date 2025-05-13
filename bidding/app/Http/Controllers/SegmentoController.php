<?php

namespace App\Http\Controllers;

use App\Models\Segmento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SegmentoController extends Controller
{
    /**
     * Exibe a lista de segmentos do usuário
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // Se for usuário master, mostrar segmentos da empresa
            if (method_exists($user, 'isUsuarioMaster') && $user->isUsuarioMaster() && $user->empresa) {
                $segmentos = Segmento::where('empresa_id', $user->empresa_id)
                    ->orderBy('nome')
                    ->get();

                $titulo = 'Segmentos da Empresa';
                $escopo = 'empresa';
            } else {
                // Usuário comum, mostrar segmentos pessoais
                $segmentos = Segmento::where('user_id', $user->id)
                    ->orderBy('nome')
                    ->get();

                $titulo = 'Meus Segmentos';
                $escopo = 'pessoal';
            }

            return view('segmentos.index', [
                'segmentos' => $segmentos,
                'titulo' => $titulo,
                'escopo' => $escopo
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao listar segmentos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return view('segmentos.index', [
                'segmentos' => collect(),
                'titulo' => 'Meus Segmentos',
                'escopo' => 'pessoal',
                'error' => 'Erro ao carregar segmentos. Por favor, tente novamente.'
            ])->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Exibe o formulário para criar um novo segmento
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('segmentos.create');
    }

    /**
     * Armazena um novo segmento no banco de dados
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'palavras_chave' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            // Processar palavras-chave
            $palavrasChave = explode(',', $request->palavras_chave);
            $palavrasChave = array_map('trim', $palavrasChave);
            $palavrasChave = array_filter($palavrasChave, function($palavra) {
                return !empty($palavra);
            });

            // Criar segmento
            $segmento = new Segmento();
            $segmento->nome = $request->nome;
            $segmento->descricao = $request->descricao;
            $segmento->palavras_chave = $palavrasChave;

            // Se for usuário master, associar à empresa
            if (method_exists($user, 'isUsuarioMaster') && $user->isUsuarioMaster() && $user->empresa) {
                $segmento->empresa_id = $user->empresa_id;
            } else {
                $segmento->user_id = $user->id;
            }

            $segmento->save();

            // Associar segmento ao usuário
            if (method_exists($user, 'segmentos')) {
                $user->segmentos()->attach($segmento->id);
            }

            return redirect()->route('segmentos.index')
                ->with('success', 'Segmento criado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao criar segmento: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Erro ao criar segmento: ' . $e->getMessage());
        }
    }

    /**
     * Exibe os detalhes de um segmento específico
     *
     * @param  \App\Models\Segmento  $segmento
     * @return \Illuminate\View\View
     */
    public function show(Segmento $segmento)
    {
        // Verificar permissão
        $this->authorize('view', $segmento);

        // Buscar licitações relevantes para este segmento
        $licitacoes = $segmento->licitacoes()
            ->orderBy('data_encerramento_proposta')
            ->paginate(10);

        return view('segmentos.show', [
            'segmento' => $segmento,
            'licitacoes' => $licitacoes
        ]);
    }

    /**
     * Exibe o formulário para editar um segmento
     *
     * @param  \App\Models\Segmento  $segmento
     * @return \Illuminate\View\View
     */
    public function edit(Segmento $segmento)
    {
        // Verificar permissão
        $this->authorize('update', $segmento);

        return view('segmentos.edit', [
            'segmento' => $segmento
        ]);
    }

    /**
     * Atualiza um segmento específico no banco de dados
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Segmento  $segmento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Segmento $segmento)
    {
        // Verificar permissão
        $this->authorize('update', $segmento);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'palavras_chave' => 'required|string',
        ]);

        try {
            // Processar palavras-chave
            $palavrasChave = explode(',', $request->palavras_chave);
            $palavrasChave = array_map('trim', $palavrasChave);
            $palavrasChave = array_filter($palavrasChave, function($palavra) {
                return !empty($palavra);
            });

            // Atualizar segmento
            $segmento->nome = $request->nome;
            $segmento->descricao = $request->descricao;
            $segmento->palavras_chave = $palavrasChave;
            $segmento->save();

            // Analisar licitações para verificar relevância com as novas palavras-chave
            if (app()->bound('App\Services\PncpApiService')) {
                $service = app('App\Services\PncpApiService');
                $service->analisarRelevanciaLicitacoes();
            }

            return redirect()->route('segmentos.show', $segmento->id)
                ->with('success', 'Segmento atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar segmento: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Erro ao atualizar segmento: ' . $e->getMessage());
        }
    }

    /**
     * Remove um segmento do banco de dados
     *
     * @param  \App\Models\Segmento  $segmento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Segmento $segmento)
    {
        // Verificar permissão
        $this->authorize('delete', $segmento);

        try {
            // Remover associações com usuários
            if (method_exists($segmento, 'users')) {
                $segmento->users()->detach();
            }

            // Remover associações com licitações
            if (method_exists($segmento, 'licitacoes')) {
                $segmento->licitacoes()->detach();
            }

            // Excluir segmento
            $segmento->delete();

            return redirect()->route('segmentos.index')
                ->with('success', 'Segmento excluído com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao excluir segmento: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Erro ao excluir segmento: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza as palavras-chave de um segmento específico
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Segmento  $segmento
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePalavrasChave(Request $request, Segmento $segmento)
    {
        // Verificar permissão
        $this->authorize('update', $segmento);

        $request->validate([
            'palavras_chave' => 'required|array',
            'palavras_chave.*' => 'string|max:50',
        ]);

        try {
            // Atualizar palavras-chave
            $segmento->palavras_chave = $request->palavras_chave;
            $segmento->save();

            // Analisar licitações para verificar relevância com as novas palavras-chave
            if (app()->bound('App\Services\PncpApiService')) {
                $service = app('App\Services\PncpApiService');
                $service->analisarRelevanciaLicitacoes();
            }

            return response()->json([
                'success' => true,
                'message' => 'Palavras-chave atualizadas com sucesso!',
                'segmento' => $segmento
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar palavras-chave: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar palavras-chave: ' . $e->getMessage()
            ], 500);
        }
    }
}
