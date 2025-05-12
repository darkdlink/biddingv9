@extends('layouts.admin')

@section('title', 'Gerenciamento de Usuários')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Gerenciamento de Usuários</h1>
    <a href="{{ route('admin.usuarios.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="bi bi-plus-lg fa-sm text-white-50"></i> Novo Usuário
    </a>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.usuarios.index') }}" method="GET">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                    <select class="form-select" id="tipo_usuario" name="tipo_usuario">
                        <option value="">Todos</option>
                        <option value="pessoa_fisica" {{ request('tipo_usuario') == 'pessoa_fisica' ? 'selected' : '' }}>Pessoa Física</option>
                        <option value="usuario_master" {{ request('tipo_usuario') == 'usuario_master' ? 'selected' : '' }}>Usuário Master</option>
                        <option value="admin_grupo" {{ request('tipo_usuario') == 'admin_grupo' ? 'selected' : '' }}>Admin de Grupo</option>
                        <option value="admin_sistema" {{ request('tipo_usuario') == 'admin_sistema' ? 'selected' : '' }}>Admin do Sistema</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="empresa_id" class="form-label">Empresa</label>
                    <select class="form-select" id="empresa_id" name="empresa_id">
                        <option value="">Todas</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                {{ $empresa->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nome, email ou ID">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Usuários -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Usuários</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Empresa/Grupo</th>
                        <th>Status</th>
                        <th>Licença</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
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
                        <td>
                            @if($usuario->empresa)
                                {{ $usuario->empresa->nome }}
                                @if($usuario->empresa->grupo)
                                    <br><small class="text-muted">Grupo: {{ $usuario->empresa->grupo->nome }}</small>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($usuario->is_active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-danger">Inativo</span>
                            @endif
                        </td>
                        <td>
                            @if($usuario->licenca)
                                <span class="badge bg-{{ $usuario->licenca->status == 'ativa' ? 'success' : ($usuario->licenca->status == 'pendente' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($usuario->licenca->status) }}
                                </span>
                                <br>
                                <small>{{ $usuario->licenca->plano->nome }}</small>
                            @else
                                <span class="badge bg-secondary">Sem licença</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.usuarios.show', $usuario->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $usuario->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Modal de Exclusão -->
                            <div class="modal fade" id="deleteModal{{ $usuario->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $usuario->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $usuario->id }}">Confirmar Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Tem certeza que deseja excluir o usuário <strong>{{ $usuario->name }}</strong>?</p>
                                            <p class="text-danger">Esta ação não pode ser desfeita e também excluirá todos os dados associados a este usuário.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST">
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
                        <td colspan="8" class="text-center">Nenhum usuário encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $usuarios->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>

<!-- Card de Estatísticas -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Usuários</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Usuários Ativos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['ativos'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Com Licença Ativa</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['com_licenca_ativa'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-key fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Novos (Últimos 30 dias)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['novos'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar3 fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
