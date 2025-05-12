@extends('layouts.admin')

@section('title', 'Editar Licença')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Editar Licença #{{ $licenca->id }}</h1>
    <div>
        <a href="{{ route('admin.licencas.show', $licenca->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm me-2">
            <i class="bi bi-eye fa-sm text-white-50"></i> Visualizar
        </a>
        <a href="{{ route('admin.licencas.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="bi bi-arrow-left fa-sm text-white-50"></i> Voltar para Lista
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informações do Usuário</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($licenca->user->name) }}&background=4e73df&color=ffffff&size=128"
                             alt="{{ $licenca->user->name }}" class="img-fluid rounded-circle mb-3">
                    </div>
                    <div class="col-md-9">
                        <h5>{{ $licenca->user->name }}</h5>
                        <p><i class="bi bi-envelope"></i> {{ $licenca->user->email }}</p>
                        <p><i class="bi bi-person-badge"></i>
                            @if($licenca->user->tipo_usuario == 'pessoa_fisica')
                                <span class="badge bg-primary">Pessoa Física</span>
                            @elseif($licenca->user->tipo_usuario == 'usuario_master')
                                <span class="badge bg-success">Usuário Master</span>
                            @elseif($licenca->user->tipo_usuario == 'admin_grupo')
                                <span class="badge bg-info">Admin Grupo</span>
                            @else
                                <span class="badge bg-danger">Admin</span>
                            @endif
                        </p>
                        <p><i class="bi bi-calendar"></i> Cadastrado em: {{ $licenca->user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detalhes da Licença</h6>
            </div>
            <div class="card-body">
                <p><strong>Plano Atual:</strong>
                    <span class="badge bg-{{ $licenca->plano->tipo == 'pessoa_fisica' ? 'primary' : ($licenca->plano->tipo == 'empresa' ? 'success' : 'info') }}">
                        {{ ucfirst(str_replace('_', ' ', $licenca->plano->tipo)) }}
                    </span>
                    {{ $licenca->plano->nome }} ({{ ucfirst($licenca->plano->tier) }})
                </p>
                <p><strong>Status:</strong>
                    @if($licenca->status == 'ativa')
                        <span class="badge bg-success">Ativa</span>
                    @elseif($licenca->status == 'pendente')
                        <span class="badge bg-warning">Pendente</span>
                    @elseif($licenca->status == 'inativa')
                        <span class="badge bg-secondary">Inativa</span>
                    @else
                        <span class="badge bg-danger">Cancelada</span>
                    @endif
                </p>
                <p><strong>Ciclo de Cobrança:</strong> {{ ucfirst($licenca->ciclo_cobranca) }}</p>
                <p><strong>Criada em:</strong> {{ $licenca->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Última Atualização:</strong> {{ $licenca->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Atualizar Licença</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.licencas.update', $licenca->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="plano_id" class="form-label">Plano <span class="text-danger">*</span></label>
                    <select class="form-select @error('plano_id') is-invalid @enderror" id="plano_id" name="plano_id" required>
                        <optgroup label="Planos Pessoa Física">
                            @foreach($planos->where('tipo', 'pessoa_fisica') as $plano)
                                <option value="{{ $plano->id }}" {{ (old('plano_id', $licenca->plano_id) == $plano->id) ? 'selected' : '' }}>
                                    {{ $plano->nome }} - {{ ucfirst($plano->tier) }} - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Planos Empresa">
                            @foreach($planos->where('tipo', 'empresa') as $plano)
                                <option value="{{ $plano->id }}" {{ (old('plano_id', $licenca->plano_id) == $plano->id) ? 'selected' : '' }}>
                                    {{ $plano->nome }} - {{ ucfirst($plano->tier) }} - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Planos Grupo">
                            @foreach($planos->where('tipo', 'grupo') as $plano)
                                <option value="{{ $plano->id }}" {{ (old('plano_id', $licenca->plano_id) == $plano->id) ? 'selected' : '' }}>
                                    {{ $plano->nome }} - {{ ucfirst($plano->tier) }} - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('plano_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="ativa" {{ old('status', $licenca->status) == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="pendente" {{ old('status', $licenca->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="inativa" {{ old('status', $licenca->status) == 'inativa' ? 'selected' : '' }}>Inativa</option>
                        <option value="cancelada" {{ old('status', $licenca->status) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ciclo_cobranca" class="form-label">Ciclo de Cobrança <span class="text-danger">*</span></label>
                    <select class="form-select @error('ciclo_cobranca') is-invalid @enderror" id="ciclo_cobranca" name="ciclo_cobranca" required>
                        <option value="mensal" {{ old('ciclo_cobranca', $licenca->ciclo_cobranca) == 'mensal' ? 'selected' : '' }}>Mensal</option>
                        <option value="anual" {{ old('ciclo_cobranca', $licenca->ciclo_cobranca) == 'anual' ? 'selected' : '' }}>Anual (desconto de 20%)</option>
                    </select>
                    @error('ciclo_cobranca')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="data_expiracao" class="form-label">Data de Expiração <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('data_expiracao') is-invalid @enderror" id="data_expiracao" name="data_expiracao"
                           value="{{ old('data_expiracao', $licenca->data_expiracao ? $licenca->data_expiracao->format('Y-m-d') : '') }}" required>
                    @error('data_expiracao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ultimo_pagamento" class="form-label">Data do Último Pagamento</label>
                    <input type="date" class="form-control @error('ultimo_pagamento') is-invalid @enderror" id="ultimo_pagamento" name="ultimo_pagamento"
                           value="{{ old('ultimo_pagamento', $licenca->ultimo_pagamento ? $licenca->ultimo_pagamento->format('Y-m-d') : '') }}">
                    @error('ultimo_pagamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="proximo_pagamento" class="form-label">Data do Próximo Pagamento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('proximo_pagamento') is-invalid @enderror" id="proximo_pagamento" name="proximo_pagamento"
                           value="{{ old('proximo_pagamento', $licenca->proximo_pagamento ? $licenca->proximo_pagamento->format('Y-m-d') : '') }}" required>
                    @error('proximo_pagamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $licenca->observacoes) }}</textarea>
                    @error('observacoes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-secondary me-md-2" onclick="history.back()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Atualizar Licença</button>
            </div>
        </form>
    </div>
</div>

<!-- Histórico de Atualizações -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Histórico de Atualizações</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Alteração</th>
                        <th>Usuário</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licenca->historico ?? [] as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->descricao }}</td>
                        <td>{{ $log->usuario->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Nenhum registro de alteração encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
