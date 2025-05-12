<?php

namespace App\Http\Controllers;

use App\Models\Segmento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SegmentoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Se for usuário master, mostrar segmentos da empresa
        if ($user->isUsuarioMaster() && $user->empresa) {
            $segmentos = Segmento::where('empresa_id', $user->empresa_id)
                ->orderBy('nome')
                ->paginate(10);
        } else {
            // Usuário comum, mostrar segmentos pessoais
            $segmentos = Segmento::where('user_id', $user->id)
                ->orderBy('nome')
                ->paginate(10);
        }

        return view('segmentos.index', compact('segmentos'));
    }

    public function create()
    {
        return view('segmentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'palavras_chave' => 'required|string',
        ]);

        $user = Auth::user();

        // Processar palavras-chave
        $palavrasChave = array_map('trim', explode(',', $request->palavras_chave));
        $palavrasChave = array_filter($palavrasChave, function($palavra) {
            return !empty($palavra);
        });

        try {
            $segmento = new Segmento();
            $segmento->nome = $request->nome;
            $segmento->descricao = $request->descricao;
            $segmento->palavras_chave = $palavrasChave;

            // Se for usuário master, associar à empresa
            if ($user->isUsuarioMaster() && $user->empresa) {
                $segmento->empresa_id = $user->empresa_id;
            } else {
                $segmento->user_id = $user->id;
            }

            $segmento->save();

            // Associar segmento ao usuário
            $user->segmentos()->attach($segmento->id);

            return redirect()->route('segmentos.index')
                ->with('success', 'Segmento criado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao criar segmento: ' . $e->getMessage());
        }
    }

    public function show(Segmento $segmento)
    {
        $this->authorize('view', $segmento);

        // Carregar licitações relevantes para este segmento
        $licitacoes = $segmento->licitacoes()
            ->orderBy('data_encerramento_proposta')
            ->paginate(10);

        return view('segmentos.show', compact('segmento', 'licitacoes'));
    }

    public function edit(Segmento $segmento)
    {
        $this->authorize('update', $segmento);

        return view('segmentos.edit', compact('segmento'));
    }

    public function update(Request $request, Segmento $segmento)
    {
        $this->authorize('update', $segmento);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'palavras_chave' => 'required|string',
        ]);

        // Processar palavras-chave
        $palavrasChave = array_map('trim', explode(',', $request->palavras_chave));
        $palavrasChave = array_filter($palavrasChave, function($palavra) {
            return !empty($palavra);
        });

        try {
            $segmento->nome = $request->nome;
            $segmento->descricao = $request->descricao;
            $segmento->palavras_chave = $palavrasChave;
            $segmento->save();

            return redirect()->route('segmentos.show', $segmento->id)
                ->with('success', 'Segmento atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar segmento: ' . $e->getMessage());
        }
    }

    public function destroy(Segmento $segmento)
    {
        $this->authorize('delete', $segmento);

        try {
            // Remover associações com usuários
            $segmento->users()->detach();

            // Remover associações com licitações
            $segmento->licitacoes()->detach();

            // Excluir segmento
            $segmento->delete();

            return redirect()->route('segmentos.index')
                ->with('success', 'Segmento excluído com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao excluir segmento: ' . $e->getMessage());
        }
    }
}
