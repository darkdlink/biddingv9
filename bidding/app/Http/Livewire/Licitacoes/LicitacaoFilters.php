<?php

namespace App\Http\Livewire\Licitacoes;

use Livewire\Component;
use App\Models\Licitacao;
use Illuminate\Support\Facades\DB;

class LicitacaoFilters extends Component
{
    public $modalidades = [];
    public $ufs = [];

    // Filtros
    public $uf;
    public $modalidade;
    public $dataMin;
    public $dataMax;
    public $valorMin;
    public $valorMax;
    public $interesse;
    public $termoBusca;

    protected $queryString = [
        'uf',
        'modalidade',
        'dataMin',
        'dataMax',
        'valorMin',
        'valorMax',
        'interesse',
        'termoBusca'
    ];

    public function mount()
    {
        // Carregar modalidades disponíveis
        $this->modalidades = Licitacao::select('modalidade_nome')
            ->distinct()
            ->whereNotNull('modalidade_nome')
            ->orderBy('modalidade_nome')
            ->pluck('modalidade_nome')
            ->toArray();

        // Carregar UFs disponíveis
        $this->ufs = Licitacao::select('uf')
            ->distinct()
            ->whereNotNull('uf')
            ->orderBy('uf')
            ->pluck('uf')
            ->toArray();

        // Inicializar filtros padrão
        $this->dataMin = now()->format('Y-m-d');
        $this->dataMax = now()->addMonths(3)->format('Y-m-d');
    }

    public function updatedUf()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function updatedModalidade()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function updatedDataMin()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function updatedDataMax()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function updatedValorMin()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function updatedValorMax()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function updatedInteresse()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function updatedTermoBusca()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function resetarFiltros()
    {
        $this->reset(['uf', 'modalidade', 'valorMin', 'valorMax', 'interesse', 'termoBusca']);
        $this->dataMin = now()->format('Y-m-d');
        $this->dataMax = now()->addMonths(3)->format('Y-m-d');
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function aplicarFiltros()
    {
        $this->emitTo('licitacoes.licitacao-list', 'filtrosAtualizados');
    }

    public function render()
    {
        return view('livewire.licitacoes.licitacao-filters');
    }
}
