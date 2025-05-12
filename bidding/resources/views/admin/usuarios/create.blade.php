@extends('layouts.admin')

@section('title', 'Novo Usuário')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Novo Usuário</h1>
    <a href="{{ route('admin.usuarios.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="bi bi-arrow-left fa-sm text-white-50"></i> Voltar para Lista
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informações do Usuário</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Senha <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tipo_usuario" class="form-label">Tipo de Usuário <span class="text-danger">*</span></label>
                    <select class="form-select @error('tipo_usuario') is-invalid @enderror" id="tipo_usuario" name="tipo_usuario" required>
                        <option value="pessoa_fisica" {{ old('tipo_usuario') == 'pessoa_fisica' ? 'selected' : '' }}>Pessoa Física</option>
                        <option value="usuario_master" {{ old('tipo_usuario') == 'usuario_master' ? 'selected' : '' }}>Usuário Master (Empresa)</option>
                        <option value="admin_grupo" {{ old('tipo_usuario') == 'admin_grupo' ? 'selected' : '' }}>Administrador de Grupo</option>
                        <option value="admin_sistema" {{ old('tipo_usuario') == 'admin_sistema' ? 'selected' : '' }}>Administrador do Sistema</option>
                    </select>
                    @error('tipo_usuario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="is_active" class="form-label">Status</label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Campos condicionais para empresa/grupo -->
            <div id="empresa_container" class="row mb-3" style="display: none;">
                <div class="col-md-6">
                    <label for="empresa_id" class="form-label">Empresa</label>
                    <select class="form-select @error('empresa_id') is-invalid @enderror" id="empresa_id" name="empresa_id">
                        <option value="">Selecione uma empresa</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                {{ $empresa->nome }} {{ $empresa->grupo ? ' (Grupo: ' . $empresa->grupo->nome . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('empresa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="nova_empresa" class="form-label">Ou Criar Nova Empresa</label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('nova_empresa') is-invalid @enderror" id="nova_empresa" name="nova_empresa" value="{{ old('nova_empresa') }}" placeholder="Nome da empresa">
                        <button class="btn btn-outline-secondary" type="button" id="toggleNovaEmpresa">Nova</button>
                    </div>
                    @error('nova_empresa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="nova_empresa_detalhes" class="row mb-3" style="display: none;">
                <div class="col-md-6">
                    <label for="empresa_cnpj" class="form-label">CNPJ da Empresa</label>
                    <input type="text" class="form-control @error('empresa_cnpj') is-invalid @enderror" id="empresa_cnpj" name="empresa_cnpj" value="{{ old('empresa_cnpj') }}">
                    @error('empresa_cnpj')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="grupo_id" class="form-label">Grupo Empresarial</label>
                    <select class="form-select @error('grupo_id') is-invalid @enderror" id="grupo_id" name="grupo_id">
                        <option value="">Sem grupo</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                {{ $grupo->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('grupo_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Informações da Licença -->
            <div class="mt-4 mb-3">
                <h6 class="font-weight-bold">Informações da Licença</h6>
                <hr>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="criar_licenca" class="form-label">Criar Licença</label>
                    <select class="form-select @error('criar_licenca') is-invalid @enderror" id="criar_licenca" name="criar_licenca">
                        <option value="1" {{ old('criar_licenca', '1') == '1' ? 'selected' : '' }}>Sim, criar licença</option>
                        <option value="0" {{ old('criar_licenca') == '0' ? 'selected' : '' }}>Não, apenas criar usuário</option>
                    </select>
                    @error('criar_licenca')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 licenca-campo">
                    <label for="plano_id" class="form-label">Plano <span class="text-danger">*</span></label>
                    <select class="form-select @error('plano_id') is-invalid @enderror" id="plano_id" name="plano_id">
                        <option value="">Selecione um plano</option>
                        <optgroup label="Planos Pessoa Física">
                            @foreach($planos->where('tipo', 'pessoa_fisica') as $plano)
                                <option value="{{ $plano->id }}" data-tipo="pessoa_fisica" {{ old('plano_id') == $plano->id ? 'selected' : '' }}>
                                    {{ $plano->nome }} ({{ ucfirst($plano->tier) }}) - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Planos Empresa">
                            @foreach($planos->where('tipo', 'empresa') as $plano)
                                <option value="{{ $plano->id }}" data-tipo="empresa" {{ old('plano_id') == $plano->id ? 'selected' : '' }}>
                                    {{ $plano->nome }} ({{ ucfirst($plano->tier) }}) - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Planos Grupo">
                            @foreach($planos->where('tipo', 'grupo') as $plano)
                                <option value="{{ $plano->id }}" data-tipo="grupo" {{ old('plano_id') == $plano->id ? 'selected' : '' }}>
                                    {{ $plano->nome }} ({{ ucfirst($plano->tier) }}) - R$ {{ number_format($plano->preco_mensal, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('plano_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3 licenca-campo">
                <div class="col-md-6">
                    <label for="ciclo_cobranca" class="form-label">Ciclo de Cobrança</label>
                    <select class="form-select @error('ciclo_cobranca') is-invalid @enderror" id="ciclo_cobranca" name="ciclo_cobranca">
                        <option value="mensal" {{ old('ciclo_cobranca', 'mensal') == 'mensal' ? 'selected' : '' }}>Mensal</option>
                        <option value="anual" {{ old('ciclo_cobranca') == 'anual' ? 'selected' : '' }}>Anual (desconto de 20%)</option>
                    </select>
                    @error('ciclo_cobranca')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="status_licenca" class="form-label">Status da Licença</label>
                    <select class="form-select @error('status_licenca') is-invalid @enderror" id="status_licenca" name="status_licenca">
                        <option value="ativa" {{ old('status_licenca', 'ativa') == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="pendente" {{ old('status_licenca') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="inativa" {{ old('status_licenca') == 'inativa' ? 'selected' : '' }}>Inativa</option>
                    </select>
                    @error('status_licenca')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-secondary me-md-2" onclick="history.back()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Usuário</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoUsuarioSelect = document.getElementById('tipo_usuario');
        const empresaContainer = document.getElementById('empresa_container');
        const novaEmpresaDetalhes = document.getElementById('nova_empresa_detalhes');
        const toggleNovaEmpresaBtn = document.getElementById('toggleNovaEmpresa');
        const empresaIdSelect = document.getElementById('empresa_id');
        const novaEmpresaInput = document.getElementById('nova_empresa');
        const criarLicencaSelect = document.getElementById('criar_licenca');
        const licencaCampos = document.querySelectorAll('.licenca-campo');
        const planoIdSelect = document.getElementById('plano_id');

        // Mostrar/ocultar campos de empresa baseado no tipo de usuário
        function atualizarCamposEmpresa() {
            const tipo = tipoUsuarioSelect.value;
            if (tipo === 'usuario_master' || tipo === 'admin_grupo') {
                empresaContainer.style.display = 'flex';
            } else {
                empresaContainer.style.display = 'none';
                novaEmpresaDetalhes.style.display = 'none';
                novaEmpresaInput.value = '';
                empresaIdSelect.value = '';
            }
        }

        // Mostrar/ocultar campos de licença
        function atualizarCamposLicenca() {
            const criarLicenca = criarLicencaSelect.value === '1';
            licencaCampos.forEach(campo => {
                campo.style.display = criarLicenca ? 'block' : 'none';
            });
        }

        // Filtrar planos baseado no tipo de usuário
        function filtrarPlanos() {
            const tipoUsuario = tipoUsuarioSelect.value;
            const tipoPlanoCompativel =
                tipoUsuario === 'pessoa_fisica' ? 'pessoa_fisica' :
                tipoUsuario === 'usuario_master' ? 'empresa' :
                tipoUsuario === 'admin_grupo' ? 'grupo' : '';

            // Percorrer todas as opções do select de planos
            Array.from(planoIdSelect.options).forEach(option => {
                if (option.value === '') return; // Ignorar opção vazia

                const tipoDaOpcao = option.getAttribute('data-tipo');
                option.disabled = tipoDaOpcao !== tipoPlanoCompativel;

                // Se a opção selecionada for incompatível, remover a seleção
                if (option.selected && option.disabled) {
                    option.selected = false;
                }
            });

            // Selecionar o primeiro plano compatível se nenhum estiver selecionado
            if (planoIdSelect.value === '' && tipoPlanoCompativel) {
                const primeiroPlanoPossivel = Array.from(planoIdSelect.options).find(opt =>
                    opt.getAttribute('data-tipo') === tipoPlanoCompativel && !opt.disabled
                );

                if (primeiroPlanoPossivel) {
                    primeiroPlanoPossivel.selected = true;
                }
            }
        }

        // Toggle entre selecionar empresa existente ou criar nova
        toggleNovaEmpresaBtn.addEventListener('click', function() {
            const criarNovaEmpresa = novaEmpresaInput.value !== '';

            if (criarNovaEmpresa) {
                // Mudar para selecionar empresa existente
                novaEmpresaInput.value = '';
                novaEmpresaDetalhes.style.display = 'none';
                toggleNovaEmpresaBtn.textContent = 'Nova';
            } else {
                // Mudar para criar nova empresa
                empresaIdSelect.value = '';
                novaEmpresaDetalhes.style.display = 'flex';
                toggleNovaEmpresaBtn.textContent = 'Existente';
            }
        });

        // Event listeners
        tipoUsuarioSelect.addEventListener('change', function() {
            atualizarCamposEmpresa();
            filtrarPlanos();
        });

        criarLicencaSelect.addEventListener('change', atualizarCamposLicenca);

        // Inicializar estado dos campos
        atualizarCamposEmpresa();
        atualizarCamposLicenca();
        filtrarPlanos();
    });
</script>
@endpush
@endsection
