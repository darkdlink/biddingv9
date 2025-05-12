@extends('layouts.guest')

@section('title', 'Registro')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Registro</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Senha</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                                <select class="form-select @error('tipo_usuario') is-invalid @enderror" id="tipo_usuario" name="tipo_usuario" required>
                                    <option value="">Selecione...</option>
                                    <option value="pessoa_fisica" {{ old('tipo_usuario') == 'pessoa_fisica' ? 'selected' : '' }}>Pessoa Física</option>
                                    <option value="usuario_master" {{ old('tipo_usuario') == 'usuario_master' ? 'selected' : '' }}>Empresa</option>
                                </select>
                            </div>
                        </div>

                        <div id="empresa_campos" class="row mb-3" style="display: none;">
                            <div class="col-md-6">
                                <label for="empresa_nome" class="form-label">Nome da Empresa</label>
                                <input type="text" class="form-control @error('empresa_nome') is-invalid @enderror" id="empresa_nome" name="empresa_nome" value="{{ old('empresa_nome') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="empresa_cnpj" class="form-label">CNPJ</label>
                                <input type="text" class="form-control @error('empresa_cnpj') is-invalid @enderror" id="empresa_cnpj" name="empresa_cnpj" value="{{ old('empresa_cnpj') }}">
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Selecione seu Plano</h5>

                        @if(isset($planos) && $planos->count() > 0)
                            <div class="row mb-3">
                                @foreach($planos as $plano)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 {{ (isset($planoPreSelecionado) && $planoPreSelecionado == $plano->id) ? 'border-primary' : '' }}">
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
                                                    <input class="form-check-input" type="radio" name="plano_id" id="plano_{{ $plano->id }}" value="{{ $plano->id }}" {{ (old('plano_id') == $plano->id || (isset($planoPreSelecionado) && $planoPreSelecionado == $plano->id)) ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="plano_{{ $plano->id }}">
                                                        Selecionar este plano
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Nenhum plano disponível no momento. Por favor, entre em contato com o suporte.
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ciclo de Cobrança</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ciclo_cobranca" id="mensal" value="mensal" {{ old('ciclo_cobranca', 'mensal') == 'mensal' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="mensal">
                                        Mensal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ciclo_cobranca" id="anual" value="anual" {{ old('ciclo_cobranca') == 'anual' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="anual">
                                        Anual (20% de desconto)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="termos" name="termos" required>
                            <label class="form-check-label" for="termos">
                                Eu concordo com os <a href="#" target="_blank">Termos de Uso</a> e <a href="#" target="_blank">Política de Privacidade</a>
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>

                    <hr>

                    <div class="text-center">
                        <p>Já tem uma conta? <a href="{{ route('login') }}" class="text-decoration-none">Faça login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoUsuarioSelect = document.getElementById('tipo_usuario');
        const empresaCampos = document.getElementById('empresa_campos');

        function toggleEmpresaCampos() {
            if (tipoUsuarioSelect.value === 'usuario_master') {
                empresaCampos.style.display = 'flex';
                document.getElementById('empresa_nome').required = true;
                document.getElementById('empresa_cnpj').required = true;
            } else {
                empresaCampos.style.display = 'none';
                document.getElementById('empresa_nome').required = false;
                document.getElementById('empresa_cnpj').required = false;
            }
        }

        tipoUsuarioSelect.addEventListener('change', toggleEmpresaCampos);
        toggleEmpresaCampos(); // Para o estado inicial
    });
</script>
@endsection
