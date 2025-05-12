@extends('layouts.app')

@section('title', 'Renovar Licença')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Renovação de Licença</h4>
                </div>
                <div class="card-body">
                    @if($licenca->isAtiva())
                        <div class="alert alert-warning">
                            <h5><i class="bi bi-exclamation-triangle-fill"></i> Sua licença expira em breve</h5>
                            <p>Sua licença atual é válida até <strong>{{ $licenca->data_expiracao->format('d/m/Y') }}</strong> ({{ $licenca->data_expiracao->diffInDays(now()) }} dias restantes).</p>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h5><i class="bi bi-x-circle-fill"></i> Sua licença expirou</h5>
                            <p>Sua licença expirou em <strong>{{ $licenca->data_expiracao->format('d/m/Y') }}</strong>. Renove agora para continuar utilizando o sistema.</p>
                        </div>
                    @endif

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Detalhes da Licença Atual</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Plano:</strong> {{ $licenca->plano->nome }}</p>
                                    <p><strong>Ciclo de Cobrança:</strong> {{ $licenca->ciclo_cobranca == 'mensal' ? 'Mensal' : 'Anual' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong>
                                        @if($licenca->status == 'ativa')
                                            <span class="badge bg-success">Ativa</span>
                                        @elseif($licenca->status == 'inativa')
                                            <span class="badge bg-danger">Inativa</span>
                                        @elseif($licenca->status == 'pendente')
                                            <span class="badge bg-warning">Pendente</span>
                                        @else
                                            <span class="badge bg-secondary">Cancelada</span>
                                        @endif
                                    </p>
                                    <p><strong>Validade:</strong> {{ $licenca->data_expiracao ? $licenca->data_expiracao->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-4">Selecione o Plano para Renovação</h5>

                    <form method="POST" action="{{ route('licenca.processar') }}">
                        @csrf

                        <div class="row">
                            @foreach($planos as $plano)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 {{ $licenca->plano_id == $plano->id ? 'border-primary' : '' }}">
                                        <div class="card-header text-center">
                                            <h5 class="mb-0">{{ $plano->nome }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <h3 class="text-primary">R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}</h3>
                                                <p class="text-muted">por mês</p>
                                            </div>
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> {{ $plano->max_segmentos ?? 'Ilimitado' }} segmentos</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> {{ $plano->descricao }}</li>
                                            </ul>
                                        </div>
                                        <div class="card-footer bg-transparent border-0">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="plano_id" id="plano_{{ $plano->id }}" value="{{ $plano->id }}" {{ $licenca->plano_id == $plano->id ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="plano_{{ $plano->id }}">
                                                    {{ $licenca->plano_id == $plano->id ? 'Continuar com este plano' : 'Selecionar este plano' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Ciclo de Cobrança</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="ciclo_cobranca" id="mensal" value="mensal" {{ $licenca->ciclo_cobranca == 'mensal' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="mensal">
                                        <strong>Mensal</strong> - Pagamento mensal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ciclo_cobranca" id="anual" value="anual" {{ $licenca->ciclo_cobranca == 'anual' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="anual">
                                        <strong>Anual</strong> - Pagamento anual com 20% de desconto
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Continuar para Pagamento</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Voltar para o Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
