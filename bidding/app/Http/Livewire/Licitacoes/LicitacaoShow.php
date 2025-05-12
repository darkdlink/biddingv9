<?php

namespace App\Http\Livewire\Licitacoes;

use Livewire\Component;
use App\Models\Licitacao;
use App\Models\Acompanhamento;
use Illuminate\Support\Facades\Auth;

class LicitacaoShow extends Component
{
    public $licitacao;
    public $novoAcompanhamento = [
        'titulo' => '',
        'descricao' => '',
        'tipo' => 'anotacao',
        'is_public' => false,
    ];

    public function mount(Licitacao $licitacao)
    {
        $this->licitacao = $licitacao;
    }

    public function toggleInteresse()
    {
        $this->licitacao->interesse = !$this->licitacao->interesse;
        $this->licitacao->save();

        $this->emit('licitacaoInteresseAtualizado');

        $status = $this->licitacao->interesse ? 'marcada como de interesse' : 'removida dos interesses';
        $this->dispatchBrowserEvent('notify', [
            'message' => "Licitação {$status} com sucesso!"
        ]);
    }

    public function salvarAcompanhamento()
    {
        $this->validate([
            'novoAcompanhamento.titulo' => 'required|string|max:255',
            'novoAcompanhamento.descricao' => 'required|string',
            'novoAcompanhamento.tipo' => 'required|in:anotacao,lembrete,alteracao,documento,outro',
        ]);

        Acompanhamento::create([
            'licitacao_id' => $this->licitacao->id,
            'user_id' => Auth::id(),
            'titulo' => $this->novoAcompanhamento['titulo'],
            'descricao' => $this->novoAcompanhamento['descricao'],
            'tipo' => $this->novoAcompanhamento['tipo'],
            'is_public' => $this->novoAcompanhamento['is_public'],
            'data_evento' => now(),
        ]);

        $this->reset('novoAcompanhamento');
        $this->licitacao = $this->licitacao->fresh(['acompanhamentos', 'propostas', 'segmentos']);

        $this->dispatchBrowserEvent('notify', [
            'message' => 'Acompanhamento adicionado com sucesso!'
        ]);
    }

    public function excluirAcompanhamento($id)
    {
        $acompanhamento = Acompanhamento::find($id);

        if ($acompanhamento && $acompanhamento->user_id === Auth::id()) {
            $acompanhamento->delete();
            $this->licitacao = $this->licitacao->fresh(['acompanhamentos', 'propostas', 'segmentos']);

            $this->dispatchBrowserEvent('notify', [
                'message' => 'Acompanhamento excluído com sucesso!'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.licitacoes.licitacao-show');
    }
}
