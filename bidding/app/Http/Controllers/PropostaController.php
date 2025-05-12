<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\PropostaArquivo;
use App\Models\PropostaVersao;
use App\Models\Licitacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropostaController extends Controller
{
    public function index()
    {
        $propostas = Proposta::where('user_id', Auth::id())
            ->with('licitacao')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('propostas.index', compact('propostas'));
    }

    public function create(Request $request)
    {
        $licitacao = null;

        if ($request->has('licitacao')) {
            $licitacao = Licitacao::findOrFail($request->licitacao);
        }

        return view('propostas.create', compact('licitacao'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'licitacao_id' => 'required|exists:licitacoes,id',
            'titulo' => 'required|string|max:255',
            'valor_proposta' => 'required|numeric|min:0',
            'conteudo' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'arquivos.*' => 'nullable|file|max:10240', // 10MB max por arquivo
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

            // Processar arquivos, se houver
            if ($request->hasFile('arquivos')) {
                foreach ($request->file('arquivos') as $arquivo) {
                    $path = $arquivo->store('propostas/' . $proposta->id, 'public');

                    PropostaArquivo::create([
                        'proposta_id' => $proposta->id,
                        'nome' => $arquivo->getClientOriginalName(),
                        'caminho' => $path,
                        'tipo_mime' => $arquivo->getMimeType(),
                        'tamanho' => $arquivo->getSize(),
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('propostas.show', $proposta->id)
                ->with('success', 'Proposta criada com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao criar proposta: ' . $e->getMessage());
        }
    }

    public function show(Proposta $proposta)
    {
        $this->authorize('view', $proposta);

        $proposta->load(['licitacao', 'versoes', 'arquivos']);

        return view('propostas.show', compact('proposta'));
    }

    public function edit(Proposta $proposta)
    {
        $this->authorize('update', $proposta);

        // Verificar se proposta está em rascunho
        if (!$proposta->isRascunho()) {
            return redirect()->route('propostas.show', $proposta->id)
                ->with('error', 'Não é possível editar uma proposta que já foi submetida.');
        }

        $proposta->load('licitacao');

        return view('propostas.edit', compact('proposta'));
    }

    public function update(Request $request, Proposta $proposta)
    {
        $this->authorize('update', $proposta);

        // Verificar se proposta está em rascunho
        if (!$proposta->isRascunho()) {
            return redirect()->route('propostas.show', $proposta->id)
                ->with('error', 'Não é possível editar uma proposta que já foi submetida.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'valor_proposta' => 'required|numeric|min:0',
            'conteudo' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'arquivos.*' => 'nullable|file|max:10240', // 10MB max por arquivo
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

            // Processar arquivos, se houver
            if ($request->hasFile('arquivos')) {
                foreach ($request->file('arquivos') as $arquivo) {
                    $path = $arquivo->store('propostas/' . $proposta->id, 'public');

                    PropostaArquivo::create([
                        'proposta_id' => $proposta->id,
                        'nome' => $arquivo->getClientOriginalName(),
                        'caminho' => $path,
                        'tipo_mime' => $arquivo->getMimeType(),
                        'tamanho' => $arquivo->getSize(),
                    ]);
                }
            }

            \DB::commit();

            return redirect()->route('propostas.show', $proposta->id)
                ->with('success', 'Proposta atualizada com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();

            return back()->withInput()
                ->with('error', 'Erro ao atualizar proposta: ' . $e->getMessage());
        }
    }

    public function destroy(Proposta $proposta)
    {
        $this->authorize('delete', $proposta);

        // Verificar se proposta está em rascunho
        if (!$proposta->isRascunho()) {
            return redirect()->route('propostas.show', $proposta->id)
                ->with('error', 'Não é possível excluir uma proposta que já foi submetida.');
        }

        try {
            // Excluir arquivos do storage
            foreach ($proposta->arquivos as $arquivo) {
                Storage::disk('public')->delete($arquivo->caminho);
            }

            // Excluir proposta (as relações serão excluídas em cascata devido às migrações)
            $proposta->delete();

            return redirect()->route('propostas.index')
                ->with('success', 'Proposta excluída com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao excluir proposta: ' . $e->getMessage());
        }
    }

    public function submeter(Proposta $proposta)
    {
        $this->authorize('update', $proposta);

        // Verificar se proposta está em rascunho
        if (!$proposta->isRascunho()) {
            return redirect()->route('propostas.show', $proposta->id)
                ->with('error', 'Esta proposta já foi submetida.');
        }

        try {
            $proposta->update([
                'status' => 'submetida',
                'data_submissao' => now(),
            ]);

            return redirect()->route('propostas.show', $proposta->id)
                ->with('success', 'Proposta submetida com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao submeter proposta: ' . $e->getMessage());
        }
    }

    public function cancelar(Proposta $proposta)
    {
        $this->authorize('update', $proposta);

        // Verificar se proposta já foi submetida
        if ($proposta->isRascunho() || $proposta->status === 'cancelada') {
            return redirect()->route('propostas.show', $proposta->id)
                ->with('error', 'Não é possível cancelar esta proposta.');
        }

        try {
            $proposta->update([
                'status' => 'cancelada',
            ]);

            return redirect()->route('propostas.show', $proposta->id)
                ->with('success', 'Proposta cancelada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao cancelar proposta: ' . $e->getMessage());
        }
    }

    public function excluirArquivo(PropostaArquivo $arquivo)
    {
        $this->authorize('update', $arquivo->proposta);

        // Verificar se proposta está em rascunho
        if (!$arquivo->proposta->isRascunho()) {
            return redirect()->route('propostas.show', $arquivo->proposta->id)
                ->with('error', 'Não é possível excluir arquivos de uma proposta que já foi submetida.');
        }

        try {
            // Excluir arquivo do storage
            Storage::disk('public')->delete($arquivo->caminho);

            // Excluir registro
            $arquivo->delete();

            return back()
                ->with('success', 'Arquivo excluído com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao excluir arquivo: ' . $e->getMessage());
        }
    }
}
