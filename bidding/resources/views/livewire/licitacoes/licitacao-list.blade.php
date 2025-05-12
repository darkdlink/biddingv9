<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Licitações Disponíveis</h3>

        <div>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-filter"></i> Filtrar por Segmento
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item {{ empty($segmentoId) ? 'active' : '' }}" href="{{ route('licitacoes.index') }}">Todos os Segmentos</a></li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($segmentos as $segmento)
                        <li><a class="dropdown-item {{ $segmentoId == $segmento->id ? 'active' : '' }}" href="{{ route('licitacoes.index', ['segmento' => $segmento->id]) }}">{{ $segmento->nome }}</a></li>
                    @endforeach
                </ul>
            </div>

            <button type="button" class="btn btn-primary" wire:click="sincronizarLicitacoes" {{ $sincronizando ? 'disabled' : '' }}>
                <i class="bi bi-arrow-repeat"></i> Sincronizar Licitações
            </button>
        </div>
    </div>

    @if($sincronizando || $sincronizacaoStatus)
        <div class="alert {{ $sincronizando ? 'alert-info' : 'alert-success' }} mb-4">
            <div class="d-flex align-items-center">
                @if($sincronizando)
                    <div class="spinner-border spinner-border-sm me-2" role="status">
                        <span class="visually-hidden">Sincronizando...</span>
                    </div>
                @else
                    <i class="bi bi-check-circle-fill me-2"></i>
                @endif
                <div>{{ $sincronizacaoStatus }}</div>
            </div>
        </div>
    @endif

    @if($licitacoes->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Nenhuma licitação encontrada com os filtros selecionados.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('objeto_compra')" class="text-decoration-none text-dark">
                                Objeto
                                @if($sortField === 'objeto_compra')
                                    <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('orgao_entidade')" class="text-decoration-none text-dark">
                                Órgão
                                @if($sortField === 'orgao_entidade')
                                    <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('valor_total_estimado')" class="text-decoration-none text-dark">
                                Valor Estimado
                                @if($sortField === 'valor_total_estimado')
                                    <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('data_encerramento_proposta')" class="text-decoration-none text-dark">
                                Encerramento
                                @if($sortField === 'data_encerramento_proposta')
                                    <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($licitacoes as $licitacao)
                        <tr>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                    {{ $licitacao->objeto_compra }}
                                </span>
                            </td>
                            <td>{{ $licitacao->orgao_entidade }}</td>
                            <td>R$ {{ number_format($licitacao->valor_total_estimado, 2, ',', '.') }}</td>
                            <td>{{ $licitacao->data_encerramento_proposta ? $licitacao->data_encerramento_proposta->format('d/m/Y') : 'N/A' }}</td>
                            <td>{!! $licitacao->status_formatado !!}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('licitacoes.show', $licitacao->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <button type="button" class="btn btn-sm {{ $licitacao->interesse ? 'btn-warning' : 'btn-outline-warning' }}"
                                            wire:click="toggleInteresse({{ $licitacao->id }})"
                                            title="{{ $licitacao->interesse ? 'Remover interesse' : 'Marcar interesse' }}">
                                        <i class="bi bi-star{{ $licitacao->interesse ? '-fill' : '' }}"></i>
                                    </button>

                                    <a href="{{ route('propostas.create', ['licitacao' => $licitacao->id]) }}" class="btn btn-sm btn-outline-success" title="Criar proposta">
                                        <i class="bi bi-file-earmark-plus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <p class="text-muted mb-0">
                    Exibindo {{ $licitacoes->firstItem() ?? 0 }} a {{ $licitacoes->lastItem() ?? 0 }} de {{ $licitacoes->total() }} licitações
                </p>
            </div>

            <div>
                {{ $licitacoes->links() }}
            </div>
        </div>
    @endif

    <!-- Notificações para sincronização -->
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
