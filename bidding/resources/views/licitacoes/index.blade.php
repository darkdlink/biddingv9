@extends('layouts.app')

@section('title', 'Licitações')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Licitações</h1>

        <div class="d-flex">
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#filtrosModal">
                <i class="bi bi-funnel-fill me-1"></i> Filtros
            </button>

            <button type="button" class="btn btn-success" id="btnSincronizar" onclick="sincronizarLicitacoes()">
                <i class="bi bi-arrow-repeat me-1"></i> Sincronizar Licitações
            </button>
        </div>
    </div>

    <!-- Alerta de Sincronização -->
    <div id="alertaSincronizacao" class="alert alert-info d-none" role="alert">
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm me-2" role="status">
                <span class="visually-hidden">Sincronizando...</span>
            </div>
            <div id="mensagemSincronizacao">Sincronizando licitações. Por favor, aguarde...</div>
        </div>
    </div>

    <!-- Cards de filtros rápidos -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('licitacoes.index') }}" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Todas as Licitações</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLicitacoes ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-file-earmark-text fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('licitacoes.index', ['interesse' => 1]) }}" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    De Interesse</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalInteresse ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-star fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('licitacoes.index', ['data_min' => now()->format('Y-m-d')]) }}" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Abertas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAbertas ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <a href="{{ route('licitacoes.index', ['segmento' => 'relevantes']) }}" class="text-decoration-none">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Relevantes para meus Segmentos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRelevantes ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-tags fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Tabela de Licitações -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Licitações Disponíveis</h6>

            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="form-control" placeholder="Buscar no objeto..." id="searchInput">
                <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tabelaLicitacoes" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Objeto</th>
                            <th>Órgão</th>
                            <th>Modalidade</th>
                            <th>Valor Estimado</th>
                            <th>Encerramento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($licitacoes as $licitacao)
                            <tr>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 250px;">
                                        {{ $licitacao->objeto_compra }}
                                    </span>
                                </td>
                                <td>{{ $licitacao->orgao_entidade }}</td>
                                <td>{{ $licitacao->modalidade_nome }}</td>
                                <td>R$ {{ number_format($licitacao->valor_total_estimado, 2, ',', '.') }}</td>
                                <td>{{ $licitacao->data_encerramento_proposta ? $licitacao->data_encerramento_proposta->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @php
                                        $status = 'Aberta';
                                        $statusClass = 'success';

                                        if (!$licitacao->data_encerramento_proposta) {
                                            $status = 'Indefinida';
                                            $statusClass = 'secondary';
                                        } elseif ($licitacao->data_encerramento_proposta < now()) {
                                            $status = 'Encerrada';
                                            $statusClass = 'danger';
                                        } elseif ($licitacao->data_encerramento_proposta->diffInDays(now()) <= 3) {
                                            $status = 'Urgente';
                                            $statusClass = 'warning';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ $status }}

                                        @if ($status == 'Aberta' || $status == 'Urgente')
                                            ({{ $licitacao->data_encerramento_proposta->diffInDays(now()) }} dias)
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('licitacoes.show', $licitacao->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm {{ $licitacao->interesse ? 'btn-warning' : 'btn-outline-warning' }}"
                                                onclick="toggleInteresse({{ $licitacao->id }})">
                                            <i class="bi bi-star{{ $licitacao->interesse ? '-fill' : '' }}"></i>
                                        </button>

                                        <a href="{{ route('propostas.create', ['licitacao' => $licitacao->id]) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-file-earmark-plus"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-muted mb-0">Nenhuma licitação encontrada.</p>

                                    <button type="button" class="btn btn-primary mt-3" onclick="sincronizarLicitacoes()">
                                        <i class="bi bi-arrow-repeat me-1"></i> Sincronizar Licitações
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <p class="text-muted mb-0">
                        Exibindo {{ $licitacoes->firstItem() ?? 0 }} a {{ $licitacoes->lastItem() ?? 0 }} de {{ $licitacoes->total() ?? 0 }} licitações
                    </p>
                </div>

                <div>
                    {{ $licitacoes->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1" aria-labelledby="filtrosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtrosModalLabel">Filtros Avançados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('licitacoes.index') }}" method="GET" id="filtrosForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uf" class="form-label">Estado (UF)</label>
                            <select id="uf" name="uf" class="form-select">
                                <option value="">Todos os estados</option>
                                @foreach($ufs ?? [] as $uf)
                                    <option value="{{ $uf }}" {{ request('uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modalidade" class="form-label">Modalidade</label>
                            <select id="modalidade" name="modalidade" class="form-select">
                                <option value="">Todas as modalidades</option>
                                @foreach($modalidades ?? [] as $modalidade)
                                    <option value="{{ $modalidade }}" {{ request('modalidade') == $modalidade ? 'selected' : '' }}>{{ $modalidade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_min" class="form-label">Data de Encerramento (De)</label>
                            <input type="date" id="data_min" name="data_min" class="form-control" value="{{ request('data_min') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="data_max" class="form-label">Data de Encerramento (Até)</label>
                            <input type="date" id="data_max" name="data_max" class="form-control" value="{{ request('data_max') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="valor_min" class="form-label">Valor Mínimo</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" id="valor_min" name="valor_min" class="form-control" value="{{ request('valor_min') }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="valor_max" class="form-label">Valor Máximo</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" id="valor_max" name="valor_max" class="form-control" value="{{ request('valor_max') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="segmento_id" class="form-label">Segmento</label>
                            <select id="segmento_id" name="segmento_id" class="form-select">
                                <option value="">Todos os segmentos</option>
                                <option value="relevantes" {{ request('segmento_id') == 'relevantes' ? 'selected' : '' }}>Relevantes para meus segmentos</option>
                                @foreach($segmentos ?? [] as $segmento)
                                    <option value="{{ $segmento->id }}" {{ request('segmento_id') == $segmento->id ? 'selected' : '' }}>{{ $segmento->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="interesse" class="form-label">Interesse</label>
                            <select id="interesse" name="interesse" class="form-select">
                                <option value="">Todos</option>
                                <option value="1" {{ request('interesse') == '1' ? 'selected' : '' }}>Com interesse</option>
                                <option value="0" {{ request('interesse') == '0' ? 'selected' : '' }}>Sem interesse</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="termo" class="form-label">Busca por Texto</label>
                            <input type="text" id="termo" name="termo" class="form-control" placeholder="Buscar no objeto, órgão ou número..." value="{{ request('termo') }}">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnLimparFiltros">Limpar Filtros</button>
                <button type="button" class="btn btn-primary" id="btnAplicarFiltros">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Sincronizar licitações
    function sincronizarLicitacoes() {
        // Mostrar alerta de sincronização
        $('#alertaSincronizacao').removeClass('d-none');
        $('#btnSincronizar').prop('disabled', true);

        // Fazer requisição AJAX para sincronizar
        $.ajax({
            url: '{{ route("licitacoes.sincronizar") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Atualizar mensagem
                $('#alertaSincronizacao').removeClass('alert-info').addClass('alert-success');
                $('#mensagemSincronizacao').html('Sincronização concluída com sucesso! Recarregando página...');

                // Remover o spinner
                $('#alertaSincronizacao .spinner-border').remove();

                // Adicionar ícone de sucesso
                $('#mensagemSincronizacao').prepend('<i class="bi bi-check-circle-fill me-2"></i>');

                // Recarregar a página após 2 segundos
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            },
            error: function(xhr, status, error) {
                // Atualizar mensagem
                $('#alertaSincronizacao').removeClass('alert-info').addClass('alert-danger');
                $('#mensagemSincronizacao').html('Erro ao sincronizar: ' + (xhr.responseJSON?.message || error));

                // Remover o spinner
                $('#alertaSincronizacao .spinner-border').remove();

                // Adicionar ícone de erro
                $('#mensagemSincronizacao').prepend('<i class="bi bi-exclamation-triangle-fill me-2"></i>');

                // Reativar o botão após 3 segundos
                setTimeout(function() {
                    $('#btnSincronizar').prop('disabled', false);
                }, 3000);
            }
        });
    }

    // Marcar/desmarcar interesse
    function toggleInteresse(licitacaoId) {
        $.ajax({
            url: '{{ url("licitacoes") }}/' + licitacaoId + '/interesse',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                interesse: $('button[onclick="toggleInteresse(' + licitacaoId + ')"]').hasClass('btn-outline-warning') ? 1 : 0
            },
            success: function(response) {
                var btn = $('button[onclick="toggleInteresse(' + licitacaoId + ')"]');

                if (btn.hasClass('btn-outline-warning')) {
                    btn.removeClass('btn-outline-warning').addClass('btn-warning');
                    btn.find('i').removeClass('bi-star').addClass('bi-star-fill');
                    toastr.success('Licitação marcada como de interesse');
                } else {
                    btn.removeClass('btn-warning').addClass('btn-outline-warning');
                    btn.find('i').removeClass('bi-star-fill').addClass('bi-star');
                    toastr.success('Licitação removida dos interesses');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Erro ao atualizar interesse: ' + (xhr.responseJSON?.message || error));
            }
        });
    }

    // Aplicar filtros do modal
    $('#btnAplicarFiltros').click(function() {
        $('#filtrosForm').submit();
    });

    // Limpar filtros
    $('#btnLimparFiltros').click(function() {
        window.location.href = '{{ route("licitacoes.index") }}';
    });

    // Busca rápida
    $('#btnSearch').click(function() {
        const termo = $('#searchInput').val();
        window.location.href = '{{ route("licitacoes.index") }}?termo=' + encodeURIComponent(termo);
    });

    // Também permitir busca com Enter
    $('#searchInput').keypress(function(e) {
        if (e.which == 13) {
            $('#btnSearch').click();
        }
    });
</script>
@endsection
