@extends('layouts.admin')

@section('title', 'Painel Administrativo')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Painel Administrativo</h1>
    <a href="{{ route('admin.dashboard.download') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="bi bi-download fa-sm text-white-50"></i> Gerar Relatório
    </a>
</div>

<div class="row">
    <!-- Total de Usuários Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Usuários</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsuarios }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Empresas Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total de Empresas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEmpresas }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MRR Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">MRR (Receita Mensal Recorrente)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ number_format($mrr, 2, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Churn Rate Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Churn Rate (Mensal)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($churnRate, 2) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Gráfico de Receita -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Receita Mensal</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Opções:</div>
                        <a class="dropdown-item" href="#">Exportar CSV</a>
                        <a class="dropdown-item" href="#">Exportar PDF</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Mais detalhes</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="receitaMensalChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Distribuição de Planos -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Distribuição de Planos</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Opções:</div>
                        <a class="dropdown-item" href="#">Exportar CSV</a>
                        <a class="dropdown-item" href="#">Exportar PDF</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Mais detalhes</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="planosDistribuicaoChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="bi bi-circle-fill text-primary"></i> Pessoa Física
                    </span>
                    <span class="mr-2">
                        <i class="bi bi-circle-fill text-success"></i> Empresa
                    </span>
                    <span class="mr-2">
                        <i class="bi bi-circle-fill text-info"></i> Grupo
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Últimos Usuários -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Últimos Usuários Registrados</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosUsuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    @if($usuario->tipo_usuario == 'pessoa_fisica')
                                        <span class="badge bg-primary">Pessoa Física</span>
                                    @elseif($usuario->tipo_usuario == 'usuario_master')
                                        <span class="badge bg-success">Usuário Master</span>
                                    @elseif($usuario->tipo_usuario == 'admin_grupo')
                                        <span class="badge bg-info">Admin Grupo</span>
                                    @else
                                        <span class="badge bg-danger">Admin</span>
                                    @endif
                                </td>
                                <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-primary btn-sm">Ver Todos os Usuários</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Licenças -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Últimas Licenças Ativadas</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Plano</th>
                                <th>Status</th>
                                <th>Expira em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimasLicencas as $licenca)
                            <tr>
                                <td>{{ $licenca->user->name }}</td>
                                <td>{{ $licenca->plano->nome }}</td>
                                <td>
                                    @if($licenca->status == 'ativa')
                                        <span class="badge bg-success">Ativa</span>
                                    @elseif($licenca->status == 'pendente')
                                        <span class="badge bg-warning">Pendente</span>
                                    @elseif($licenca->status == 'inativa')
                                        <span class="badge bg-secondary">Inativa</span>
                                    @else
                                        <span class="badge bg-danger">Cancelada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($licenca->data_expiracao)
                                        {{ $licenca->data_expiracao->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.licencas.index') }}" class="btn btn-primary btn-sm">Ver Todas as Licenças</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Receita
    var ctx = document.getElementById('receitaMensalChart').getContext('2d');
    var receitaMensalChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($receitaMensalLabels) !!},
            datasets: [{
                label: 'Receita Mensal (R$)',
                data: {!! json_encode($receitaMensalData) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                },
                y: {
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'R$ ' + context.parsed.y.toLocaleString('pt-BR');
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Distribuição de Planos
    var ctxPie = document.getElementById('planosDistribuicaoChart').getContext('2d');
    var planosDistribuicaoChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Pessoa Física', 'Empresa', 'Grupo'],
            datasets: [{
                data: {!! json_encode($distribuicaoPlanosData) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                hoverBorderColor: 'rgba(234, 236, 244, 1)',
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.parsed;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = Math.round((value * 100) / total);
                            return label + ': ' + percentage + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
