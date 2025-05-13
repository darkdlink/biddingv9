@extends('layouts.app')

@section('title', 'Novo Segmento')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Novo Segmento</h1>

        <a href="{{ route('segmentos.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informações do Segmento</h6>
        </div>

        <div class="card-body">
            <form action="{{ route('segmentos.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Segmento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome') }}" required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="palavras_chave" class="form-label">Palavras-chave <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('palavras_chave') is-invalid @enderror" id="palavras_chave" name="palavras_chave" value="{{ old('palavras_chave') }}" required>
                    <div class="form-text">
                        Informe palavras-chave separadas por vírgula. Estas palavras serão usadas para identificar licitações relevantes.
                    </div>
                    @error('palavras_chave')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('segmentos.index') }}" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Salvar Segmento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
