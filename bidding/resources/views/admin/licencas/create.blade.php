@extends('layouts.admin')

@section('title', 'Nova Licença')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nova Licença</h1>
    <a href="{{ route('admin.licencas.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="bi bi-arrow-left fa-sm text-white-50"></i> Voltar para Lista
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informações da Licença</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.licencas.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="user_id" class="form-label">Usuário <span class="text-danger">*</span></label>
                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="">Selecione um usuário</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ old('user_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }} ({{ $usuario->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="plano_id" class="form-label">Plano <span class="text-danger">*</span></label>
                    <select class="form-select @error('plano_id') is-invalid @enderror" id="plano_id" name="plano_id" required>
                        <option value="">Selecione um plano</option>
                        <optgroup label="Planos Pessoa Física">
                            @foreach($planos->where('tipo', 'pessoa_fisica') as $plano)
<option value="{{ $plano->id }}" {{ old('plano_id') == $plano->id ? 'selected' : '' }}>
                                    {{ $plano->nome }} - {{ ucfirst($plano->tier) }} - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Planos Empresa">
                            @foreach($planos->where('tipo', 'empresa') as $plano)
                                <option value="{{ $plano->id }}" {{ old('plano_id') == $plano->id ? 'selected' : '' }}>
                                    {{ $plano->nome }} - {{ ucfirst($plano->tier) }} - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Planos Grupo">
                            @foreach($planos->where('tipo', 'grupo') as $plano)
                                <option value="{{ $plano->id }}" {{ old('plano_id') == $plano->id ? 'selected' : '' }}>
                                    {{ $plano->nome }} - {{ ucfirst($plano->tier) }} - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('plano_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ciclo_cobranca" class="form-label">Ciclo de Cobrança <span class="text-danger">*</span></label>
                    <select class="form-select @error('ciclo_cobranca') is-invalid @enderror" id="ciclo_cobranca" name="ciclo_cobranca" required>
                        <option value="mensal" {{ old('ciclo_cobranca') == 'mensal' ? 'selected' : '' }}>Mensal</option>
                        <option value="anual" {{ old('ciclo_cobranca') == 'anual' ? 'selected' : '' }}>Anual (desconto de 20%)</option>
                    </select>
                    @error('ciclo_cobranca')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="ativa" {{ old('status') == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="pendente" {{ old('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="inativa" {{ old('status') == 'inativa' ? 'selected' : '' }}>Inativa</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="data_inicio" class="form-label">Data de Início <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('data_inicio') is-invalid @enderror" id="data_inicio" name="data_inicio" value="{{ old('data_inicio', now()->format('Y-m-d')) }}" required>
                    @error('data_inicio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="data_expiracao" class="form-label">Data de Expiração</label>
                    <input type="date" class="form-control @error('data_expiracao') is-invalid @enderror" id="data_expiracao" name="data_expiracao" value="{{ old('data_expiracao') }}">
                    <small class="form-text text-muted">Se não for definida, será calculada automaticamente com base no ciclo de cobrança.</small>
                    @error('data_expiracao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ultimo_pagamento" class="form-label">Data do Último Pagamento</label>
                    <input type="date" class="form-control @error('ultimo_pagamento') is-invalid @enderror" id="ultimo_pagamento" name="ultimo_pagamento" value="{{ old('ultimo_pagamento', now()->format('Y-m-d')) }}">
                    @error('ultimo_pagamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="proximo_pagamento" class="form-label">Data do Próximo Pagamento</label>
                    <input type="date" class="form-control @error('proximo_pagamento') is-invalid @enderror" id="proximo_pagamento" name="proximo_pagamento" value="{{ old('proximo_pagamento') }}">
                    <small class="form-text text-muted">Se não for definida, será calculada automaticamente com base no ciclo de cobrança.</small>
                    @error('proximo_pagamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3">{{ old('observacoes') }}</textarea>
                    @error('observacoes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-secondary me-md-2" onclick="history.back()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Licença</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Script para atualizar automaticamente as datas de expiração e próximo pagamento
    document.addEventListener('DOMContentLoaded', function() {
        const dataInicioInput = document.getElementById('data_inicio');
        const cicloCobrancaSelect = document.getElementById('ciclo_cobranca');
        const dataExpiracaoInput = document.getElementById('data_expiracao');
        const proximoPagamentoInput = document.getElementById('proximo_pagamento');

        function atualizarDatas() {
            const dataInicio = new Date(dataInicioInput.value);
            if (!isNaN(dataInicio.getTime())) {
                const ciclo = cicloCobrancaSelect.value;

                // Calcular próximo pagamento
                const proximoPagamento = new Date(dataInicio);
                if (ciclo === 'mensal') {
                    proximoPagamento.setMonth(proximoPagamento.getMonth() + 1);
                } else {
                    proximoPagamento.setFullYear(proximoPagamento.getFullYear() + 1);
                }

                // Calcular data de expiração (igual ao próximo pagamento)
                if (!dataExpiracaoInput.value) {
                    dataExpiracaoInput.value = proximoPagamento.toISOString().split('T')[0];
                }

                if (!proximoPagamentoInput.value) {
                    proximoPagamentoInput.value = proximoPagamento.toISOString().split('T')[0];
                }
            }
        }

        dataInicioInput.addEventListener('change', atualizarDatas);
        cicloCobrancaSelect.addEventListener('change', atualizarDatas);

        // Executar uma vez no carregamento
        atualizarDatas();
    });
</script>
@endpush
@endsection
