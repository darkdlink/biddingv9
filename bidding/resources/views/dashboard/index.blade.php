@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div>
            <a href="{{ route('licitacoes.index') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
                <i class="bi bi-search fa-sm text-white-50"></i> Buscar Licitações
            </a>
            <a href="{{ route('licitacoes.sincronizar') }}" class="d-none d-sm-inline-block btn btn-success shadow-sm ml-2"
               onclick="event.preventDefault(); document.getElementById('sincronizar-form').submit();">
                <i class="bi bi-arrow-repeat fa-sm text-white-50"></i> Sincronizar Licitações
            </a>
            <form id="sincronizar-form" action="{{ route('licitacoes.sincronizar') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row">
        <!-- Card - Licitações Ativas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Licitações Ativas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $licitacoesCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card - Licitações de Interesse -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Licitações de Interesse</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $licitacoesInteresseCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card - Minhas Propostas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Minhas Propostas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $propostasCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card - Segmentos Ativos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Segmentos Ativos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $segmentosCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Licitações de Interesse -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Licitações de Interesse</h6>
                    <a href="{{ route('licitacoes.index', ['interesse' => 1]) }}" class="btn btn-sm btn-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($licitacoesInteresse) && $licitacoesInteresse->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Objeto</th>
                                        <th>Órgão</th>
                                        <th>Encerramento</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licitacoesInteresse as $licitacao)
                                        <tr>
                                            <td>
                                                <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                                    {{ $licitacao->objeto_compra }}
                                                </span>
                                            </td>
                                            <td>{{ $licitacao->orgao_entidade }}</td>
                                            <td>{{ $licitacao->data_encerramento_proposta ? $licitacao->data_encerramento_proposta->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{!! $licitacao->status_formatado ?? '<span class="badge bg-primary">Aberta</span>' !!}</td>
                                            <td>
                                                <a href="{{ route('licitacoes.show', $licitacao->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted my-5">Nenhuma licitação de interesse encontrada.</p>
                        <div class="text-center">
                            <a href="{{ route('licitacoes.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Buscar Licitações
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Licitações Relevantes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Licitações Relevantes para seus Segmentos</h6>
                    <a href="{{ route('licitacoes.index') }}" class="btn btn-sm btn-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($licitacoesRelevantes) && $licitacoesRelevantes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Objeto</th>
                                        <th>Órgão</th>
                                        <th>Encerramento</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licitacoesRelevantes as $licitacao)
                                        <tr>
                                            <td>
                                                <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                                    {{ $licitacao->objeto_compra }}
                                                </span>
                                            </td>
                                            <td>{{ $licitacao->orgao_entidade }}</td>
                                            <td>{{ $licitacao->data_encerramento_proposta ? $licitacao->data_encerramento_proposta->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{!! $licitacao->status_formatado ?? '<span class="badge bg-primary">Aberta</span>' !!}</td>
                                            <td>
                                                <a href="{{ route('licitacoes.show', $licitacao->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted my-5">Nenhuma licitação relevante encontrada para seus segmentos.</p>
                        <div class="text-center">
                            <a href="{{ route('segmentos.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-tag"></i> Configurar Segmentos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Propostas Recentes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Propostas Recentes</h6>
                    <a href="{{ route('propostas.index') }}" class="btn btn-sm btn-primary">
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($propostasRecentes) && $propostasRecentes->count() > 0)
                        <div class="list-group">
                            @foreach($propostasRecentes as $proposta)
                                <a href="{{ route('propostas.show', $proposta->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $proposta->titulo }}</h6>
                                        <span class="badge {{ $proposta->status == 'rascunho' ? 'bg-secondary' : ($proposta->status == 'submetida' ? 'bg-primary' : ($proposta->status == 'vencedora' ? 'bg-success' : 'bg-danger')) }}">
                                            {{ ucfirst($proposta->status) }}
                                        </span>
                                    </div>
                                    <p class="mb-1 text-truncate">{{ $proposta->licitacao->objeto_compra ?? 'Licitação não encontrada' }}</p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $proposta->updated_at->diffForHumans() }}
                                        <span class="ms-3"><i class="bi bi-currency-dollar"></i> R$ {{ number_format($proposta->valor_proposta, 2, ',', '.') }}</span>
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted my-5">Nenhuma proposta recente encontrada.</p>
                        <div class="text-center">
                            <a href="{{ route('propostas.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-plus"></i> Criar Nova Proposta
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status da Licença e Alertas -->
        <div class="col-lg-6 mb-4">
            <!-- Status da Licença -->
            @if(isset($licenca) && $licenca)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Status da Licença</h6>
                    </div>
                    <div class="card-body">
                        <h5>Plano: {{ $licenca->plano->nome ?? 'N/A' }}</h5>
                        <p>Ciclo de Cobrança: <span class="badge bg-secondary">{{ $licenca->ciclo_cobranca == 'mensal' ? 'Mensal' : 'Anual' }}</span></p>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>Status:</div>
                            <div>
                                @if($licenca->status == 'ativa')
                                    <span class="badge bg-success">Ativa</span>
                                @elseif($licenca->status == 'inativa')
                                    <span class="badge bg-danger">Inativa</span>
                                @elseif($licenca->status == 'pendente')
                                    <span class="badge bg-warning">Pendente</span>
                                @else
                                    <span class="badge bg-secondary">Cancelada</span>
                                @endif
                            </div>
                        </div>

                        @if($licenca->data_expiracao)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>Válida até:</div>
                                <div>{{ $licenca->data_expiracao->format('d/m/Y') }}</div>
                            </div>

                            @if($licenca->isProximaExpirar())
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Sua licença expira em {{ $licenca->data_expiracao->diffInDays(now()) }} dias.
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('licenca.renovar') }}" class="btn btn-warning">
                                        <i class="bi bi-arrow-repeat"></i> Renovar Licença
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            <!-- Alertas Não Lidos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Alertas</h6>
                    @if(isset($alertasNaoLidos) && $alertasNaoLidos > 0)
                        <a href="{{ route('alertas.marcar-lidos') }}" class="btn btn-sm btn-primary">Marcar Como Lidos</a>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($alertasNaoLidos) && $alertasNaoLidos > 0)
                        <div class="alert alert-info">
                            <i class="bi bi-bell-fill"></i> Você tem {{ $alertasNaoLidos }} {{ $alertasNaoLidos == 1 ? 'alerta não lido' : 'alertas não lidos' }}.
                        </div>
                    @else
                        <p class="text-center text-muted my-5">Nenhum alerta pendente.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Você pode adicionar scripts específicos para o dashboard aqui
</script>
@endpush
