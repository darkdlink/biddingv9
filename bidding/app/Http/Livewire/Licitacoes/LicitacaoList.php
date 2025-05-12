<?php

namespace App\Http\Livewire\Licitacoes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Licitacao;
use App\Models\Segmento;
use Illuminate\Support\Facades\Auth;
use App\Services\PncpApiService;

class LicitacaoList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtros recebidos
    public $uf;
    public $modalidade;
    public $dataMin;
    public $dataMax;
    public $valorMin;
    public $valorMax;
    public $interesse;
    public $termoBusca;
    public $segmentoId;

    // Ordenação
    public $sortField = 'data_encerramento_proposta';
    public $sortDirection = 'asc';

    // Status da sincronização
    public $sincronizando = false;
    public $sincronizacaoStatus = null;

    protected $listeners = [
        'filtrosAtualizados' => 'atualizarFiltros',
        'licitacaoInteresseAtualizado' => '$refresh',
    ];

    public function mount()
    {
        // Obter segmento da query string se existir
        $this->segmentoId = request()->query('segmento');
    }

    public function atualizarFiltros()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleInteresse($licitacaoId)
    {
        $licitacao = Licitacao::find($licitacaoId);
        if ($licitacao) {
            $licitacao->interesse = !$licitacao->interesse;
            $licitacao->save();
            $this->emit('licitacaoInteresseAtualizado');
        }
    }

    public function sincronizarLicitacoes()
    {
        $this->sincronizando = true;
        $this->sincronizacaoStatus = 'Iniciando sincronização...';

        try {
            $pncpService = app(PncpApiService::class);

            $this->sincronizacaoStatus = 'Consultando API do PNCP...';
            $params = [
                'dataFinal' => now()->addMonths(3)->format('Ymd'),
                'pagina' => 1,
                'tamanhoPagina' => 50
            ];

            if ($this->uf) {
                $params['uf'] = $this->uf;
            }

            $resultado = $pncpService->consultarLicitacoesAbertas($params);

            $this->sincronizacaoStatus = 'Analisando relevância das licitações...';
            $pncpService->analisarRelevanciaLicitacoes();

            $this->sincronizacaoStatus = 'Sincronização finalizada com sucesso!';

            // Resetar após 3 segundos
            $this->dispatchBrowserEvent('notify', ['message' => 'Sincronização concluída com sucesso!']);

        } catch (\Exception $e) {
            $this->sincronizacaoStatus = 'Erro na sincronização: ' . $e->getMessage();
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Erro na sincronização: ' . $e->getMessage()]);
        }

        $this->sincronizando = false;
    }

    public function render()
    {
        $user = Auth::user();

        // Iniciar consulta
        $query = Licitacao::query();

        // Filtro por segmento
        if ($this->segmentoId) {
            $query->whereHas('segmentos', function($q) {
                $q->where('segmento_id', $this->segmentoId);
            });
        } elseif ($user && !$user->isAdmin()) {
            // Filtrar por segmentos do usuário
            $segmentosIds = $user->segmentos->pluck('id')->toArray();

            if (!empty($segmentosIds)) {
                $query->whereHas('segmentos', function($q) use ($segmentosIds) {
                    $q->whereIn('segmento_id', $segmentosIds);
                });
            }
        }

        // Aplicar outros filtros
        if ($this->uf) {
            $query->where('uf', $this->uf);
        }

        if ($this->modalidade) {
            $query->where('modalidade_nome', $this->modalidade);
        }

        if ($this->dataMin) {
            $query->where('data_encerramento_proposta', '>=', $this->dataMin);
        }

        if ($this->dataMax) {
            $query->where('data_encerramento_proposta', '<=', $this->dataMax);
        }

        if ($this->valorMin) {
            $query->where('valor_total_estimado', '>=', $this->valorMin);
        }

        if ($this->valorMax) {
            $query->where('valor_total_estimado', '<=', $this->valorMax);
        }

        if ($this->interesse !== null && $this->interesse !== '') {
            $query->where('interesse', $this->interesse);
        }

        if ($this->termoBusca) {
            $query->where(function($q) {
                $q->where('objeto_compra', 'like', '%' . $this->termoBusca . '%')
                  ->orWhere('orgao_entidade', 'like', '%' . $this->termoBusca . '%')
                  ->orWhere('numero_controle_pncp', 'like', '%' . $this->termoBusca . '%');
            });
        }

        // Ordenação
        $query->orderBy($this->sortField, $this->sortDirection);

        // Paginação
        $licitacoes = $query->paginate(15);

        // Obter segmentos do usuário para o filtro
        $segmentos = [];
        if ($user) {
            if ($user->isAdmin()) {
                $segmentos = Segmento::all();
            } else {
                $segmentos = $user->segmentos;
            }
        }

        return view('livewire.licitacoes.licitacao-list', [
            'licitacoes' => $licitacoes,
            'segmentos' => $segmentos
        ]);
    }
}
