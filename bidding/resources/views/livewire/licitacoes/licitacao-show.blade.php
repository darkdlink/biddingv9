<div>
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Detalhes da Licitação</h2>
            <p class="text-muted">{{ $licitacao->numero_controle_pncp }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button type="button" class="btn {{ $licitacao->interesse ? 'btn-warning' : 'btn-outline-warning' }}"
                        wire:click="toggleInteresse">
                    <i class="bi bi-star{{ $licitacao->interesse ? '-fill' : '' }}"></i>
                    {{ $licitacao->interesse ? 'Remover Interesse' : 'Marcar Interesse' }}
                </button>

                <a href="{{ route('propostas.create', ['licitacao' => $licitacao->id]) }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-plus"></i> Nova Proposta
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informações Gerais</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Objeto</h6>
                    <p>{{ $licitacao->objeto_compra }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-1">Órgão</h6>
                    <p>{{ $licitacao->orgao_entidade }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Valor Estimado</h6>
                    <p class="fw-bold">R$ {{ number_format($licitacao->valor_total_estimado, 2, ',', '.') }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Data de Abertura</h6>
                    <p>{{ $licitacao->data_abertura_proposta ? $licitacao->data_abertura_proposta->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Data de Encerramento</h6>
                    <p class="fw-bold">{{ $licitacao->data_encerramento_proposta ? $licitacao->data_encerramento_proposta->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Status</h6>
                    <p>{!! $licitacao->status_formatado !!}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Modalidade</h6>
                    <p>{{ $licitacao->modalidade_nome }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Situação</h6>
                    <p>{{ $licitacao->situacao_compra_nome }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">UF</h6>
                    <p>{{ $licitacao->uf }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">Município</h6>
                    <p>{{ $licitacao->municipio }}</p>
                </div>
            </div>

            @if($licitacao->link_sistema_origem)
                <div class="mt-3">
                    <a href="{{ $licitacao->link_sistema_origem }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="bi bi-link-45deg"></i> Acessar Licitação no Sistema Origem
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Segmentos Relevantes</h5>
                </div>
                <div class="card-body">
                    @if($licitacao->segmentos->isEmpty())
                        <p class="text-muted">Nenhum segmento relevante identificado para esta licitação.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Segmento</th>
                                        <th>Relevância</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licitacao->segmentos as $segmento)
                                        <tr>
                                            <td>{{ $segmento->nome }}</td>
                                            <td>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                         style="width: {{ min($segmento->pivot->relevancia * 10, 100) }}%;"
                                                         aria-valuenow="{{ $segmento->pivot->relevancia }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="10">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Propostas</h5>
                    <a href="{{ route('propostas.create', ['licitacao' => $licitacao->id]) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-lg"></i> Nova Proposta
                    </a>
                </div>
                <div class="card-body">
                    @if($licitacao->propostas->isEmpty())
                        <p class="text-muted">Nenhuma proposta criada para esta licitação.</p>
                    @else
                        <div class="list-group">
                            @foreach($licitacao->propostas as $proposta)
                                <a href="{{ route('propostas.show', $proposta->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $proposta->titulo }}</h6>
                                        <small class="text-muted">R$ {{ number_format($proposta->valor_proposta, 2, ',', '.') }}</small>
                                    </div>
                                    <div class="d-flex w-100 justify-content-between">
                                        <small>
                                            <span class="badge bg-{{ $proposta->status === 'rascunho' ? 'secondary' : ($proposta->status === 'submetida' ? 'primary' : ($proposta->status === 'vencedora' ? 'success' : 'danger')) }}">
                                                {{ ucfirst($proposta->status) }}
                                            </span>
                                        </small>
                                        <small class="text-muted">{{ $proposta->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Acompanhamentos</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="salvarAcompanhamento" class="mb-4">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" wire:model.defer="novoAcompanhamento.titulo" required>
                            @error('novoAcompanhamento.titulo') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" rows="3" wire:model.defer="novoAcompanhamento.descricao" required></textarea>
                            @error('novoAcompanhamento.descricao') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo" wire:model.defer="novoAcompanhamento.tipo">
                                    <option value="anotacao">Anotação</option>
                                    <option value="lembrete">Lembrete</option>
                                    <option value="alteracao">Alteração</option>
                                    <option value="documento">Documento</option>
                                    <option value="outro">Outro</option>
                                </select>
                                @error('novoAcompanhamento.tipo') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" value="1" id="isPublic" wire:model.defer="novoAcompanhamento.is_public">
                                    <label class="form-check-label" for="isPublic">
                                        Visível para toda a empresa/grupo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Adicionar Acompanhamento
                        </button>
                    </form>

                    <hr>

                    @if($licitacao->acompanhamentos->isEmpty())
                        <p class="text-muted">Nenhum acompanhamento registrado para esta licitação.</p>
                    @else
                        <div class="timeline">
                            @foreach($licitacao->acompanhamentos->sortByDesc('created_at') as $acompanhamento)
                                <div class="timeline-item mb-4">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge bg-{{ $acompanhamento->tipo === 'anotacao' ? 'info' : ($acompanhamento->tipo === 'lembrete' ? 'warning' : ($acompanhamento->tipo === 'alteracao' ? 'primary' : ($acompanhamento->tipo === 'documento' ? 'success' : 'secondary'))) }} mb-2">
                                                {{ ucfirst($acompanhamento->tipo) }}
                                            </span>

                                            @if($acompanhamento->is_public)
                                                <span class="badge bg-light text-dark ms-2">
                                                    <i class="bi bi-people"></i> Público
                                                </span>
                                            @endif
                                        </div>

                                        <small class="text-muted">{{ $acompanhamento->created_at->format('d/m/Y H:i') }}</small>
                                    </div>

                                    <h6>{{ $acompanhamento->titulo }}</h6>
                                    <p class="mb-2">{{ $acompanhamento->descricao }}</p>

                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Por: {{ $acompanhamento->user->name }}</small>

                                        @if($acompanhamento->user_id === auth()->id())
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    wire:click="excluirAcompanhamento({{ $acompanhamento->id }})"
                                                    onclick="confirm('Tem certeza que deseja excluir este acompanhamento?') || event.stopImmediatePropagation()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notificações -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('notify', event => {
                const type = event.detail.type || 'success';
                const message = event.detail.message;

                // Aqui você pode usar sua biblioteca de notificações preferida
                // Exemplo com Toastr
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                } else {
                    alert(message);
                }
            });
        });
    </script>
</div>
