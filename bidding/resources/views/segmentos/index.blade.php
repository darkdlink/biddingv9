@extends('layouts.app')

@section('title', $titulo)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $titulo }}</h1>

        <a href="{{ route('segmentos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Novo Segmento
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Segmentos Cards -->
    <div class="row">
        @forelse($segmentos as $segmento)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $segmento->nome }}</h5>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $segmento->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $segmento->id }}">
                                <li>
                                    <a class="dropdown-item" href="{{ route('segmentos.show', $segmento->id) }}">
                                        <i class="bi bi-eye me-2"></i> Ver Detalhes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('segmentos.edit', $segmento->id) }}">
                                        <i class="bi bi-pencil me-2"></i> Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('segmentos.destroy', $segmento->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este segmento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i> Excluir
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($segmento->descricao)
                            <p class="card-text">{{ $segmento->descricao }}</p>
                        @else
                            <p class="card-text text-muted"><em>Sem descrição</em></p>
                        @endif

                        <h6 class="mt-3">Palavras-chave:</h6>
                        <div class="mb-3">
                            @forelse($segmento->palavras_chave as $palavra)
                                <span class="badge bg-primary me-1 mb-1">{{ $palavra }}</span>
                            @empty
                                <span class="text-muted"><em>Nenhuma palavra-chave definida</em></span>
                            @endforelse
                        </div>

                        <a href="{{ route('licitacoes.index', ['segmento_id' => $segmento->id]) }}" class="btn btn-outline-primary btn-sm mt-2">
                            <i class="bi bi-search me-1"></i> Ver Licitações Relevantes
                        </a>
                    </div>

                    <div class="card-footer text-muted">
                        @if($escopo === 'empresa' && $segmento->tipo === 'pessoal')
                            <small>Segmento pessoal de {{ $segmento->proprietario }}</small>
                        @elseif($escopo === 'empresa' && $segmento->tipo === 'empresa')
                            <small>Segmento da empresa</small>
                        @else
                            <small>Criado em {{ $segmento->created_at->format('d/m/Y') }}</small>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <p class="mb-0">Nenhum segmento encontrado. Clique em "Novo Segmento" para criar o primeiro.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
