@extends('layouts.admin')

@section('title', 'Gerenciamento de Licenças')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gerenciamento de Licenças</h1>
    <a href="{{ route('admin.licencas.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="bi bi-plus-lg fa-sm text-white-50"></i> Nova Licença
    </a>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.licencas.index') }}" method="GET">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="ativa" {{ request('status') == 'ativa' ? 'selected' : '' }}>Ativa</option>
                        <option value="inativa" {{ request('status') == 'inativa' ? 'selected' : '' }}>Inativa</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tipo_plano" class="form-label">Tipo de Plano</label>
                    <select class="form-select" id="tipo_plano" name="tipo_plano">
                        <option value="">Todos</option>
                        <option value="pessoa_fisica" {{ request('tipo_plano') == 'pessoa_fisica' ? 'selected' : '' }}>Pessoa Física</option>
                        <option value="empresa" {{ request('tipo_plano') == 'empresa' ? 'selected' : '' }}>Empresa</option>
                        <option value="grupo" {{ request('tipo_plano') == 'grupo' ? 'selected' : '' }}>Grupo</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tier" class="form-label">Tier</label>
                    <select class="form-select" id="tier" name="tier">
                        <option value="">Todos</option>
                        <option value="basico" {{ request('tier') == 'basico' ? 'selected' : '' }}>Básico</option>
                        <option value="intermediario" {{ request('tier') == 'intermediario' ? 'selected' : '' }}>Intermediário</option>
                        <option value="avancado" {{ request('tier') == 'avancado' ? 'selected' : '' }}>Avançado</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nome ou email do usuário">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.licencas.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Licenças -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Licenças</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Plano</th>
                        <th>Status</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
                        <th>Ciclo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licencas as $licenca)
                    <tr>
                        <td>{{ $licenca->id }}</td>
                        <td>
                            <a href="{{ route('admin.usuarios.show', $licenca->user_id) }}">
                                {{ $licenca->user->name }}
                            </a>
                            <br>
                            <small class="text-muted">{{ $licenca->user->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $licenca->plano->tipo == 'pessoa_fisica' ? 'primary' : ($licenca->plano->tipo == 'empresa' ? 'success' : 'info') }}">
                                {{ ucfirst(str_replace('_', ' ', $licenca->plano->tipo)) }}
                            </span>
                            <br>
                            {{ $licenca->plano->nome }} ({{ ucfirst($licenca->plano->tier) }})
                        </td>
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
                        <td>{{ $licenca->data_inicio->format('d/m/Y') }}</td>
                        <td>
                            @if($licenca->data_expiracao)
                                {{ $licenca->data_expiracao->format('d/m/Y') }}
                                @if($licenca->isProximaExpirar())
                                    <span class="badge bg-warning">Expira em breve</span>
                                @elseif($licenca->isExpirada())
                                    <span class="badge bg-danger">Expirada</span>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($licenca->ciclo_cobranca) }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.licencas.show', $licenca->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.licencas.edit', $licenca->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $licenca->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Modal de Exclusão -->
                            <div class="modal fade" id="deleteModal{{ $licenca->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $licenca->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $licenca->id }}">Confirmar Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja excluir a licença de <strong>{{ $licenca->user->name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('admin.licencas.destroy', $licenca->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Excluir</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Nenhuma licença encontrada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $licencas->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection
