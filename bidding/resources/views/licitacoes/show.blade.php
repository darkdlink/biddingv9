@extends('components.layout')

@section('title', 'Detalhes da Licitação')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detalhes da Licitação</h1>
            <p class="text-muted mb-0">{{ $licitacao->numero_controle_pncp }}</p>
        </div>
        <a href="{{ route('licitacoes.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Voltar para lista
        </a>
    </div>

    @livewire('licitacoes.licitacao-show', ['licitacao' => $licitacao])

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Detalhes Completos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 200px;">Número de Controle PNCP</th>
                            <td>{{ $licitacao->numero_controle_pncp }}</td>
                        </tr>
                        <tr>
                            <th>Órgão/Entidade</th>
                            <td>{{ $licitacao->orgao_entidade }}</td>
                        </tr>
                        <tr>
                            <th>Unidade do Órgão</th>
                            <td>{{ $licitacao->unidade_orgao }}</td>
                        </tr>
                        <tr>
                            <th>CNPJ</th>
                            <td>{{ $licitacao->cnpj }}</td>
                        </tr>
                        <tr>
                            <th>Número da Compra</th>
                            <td>{{ $licitacao->numero_compra }}</td>
                        </tr>
                        <tr>
                            <th>Ano</th>
                            <td>{{ $licitacao->ano_compra }}</td>
                        </tr>
                        <tr>
                            <th>Sequencial</th>
                            <td>{{ $licitacao->sequencial_compra }}</td>
                        </tr>
                        <tr>
                            <th>Objeto</th>
                            <td>{{ $licitacao->objeto_compra }}</td>
                        </tr>
                        <tr>
                            <th>Modalidade</th>
                            <td>{{ $licitacao->modalidade_nome }}</td>
                        </tr>
                        <tr>
                            <th>Modo de Disputa</th>
                            <td>{{ $licitacao->modo_disputa_nome }}</td>
                        </tr>
                        <tr>
                            <th>Sistema de Registro de Preços</th>
                            <td>{{ $licitacao->is_srp ? 'Sim' : 'Não' }}</td>
                        </tr>
                        <tr>
                            <th>Valor Total Estimado</th>
                            <td>R$ {{ number_format($licitacao->valor_total_estimado, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Situação</th>
                            <td>{{ $licitacao->situacao_compra_nome }}</td>
                        </tr>
                        <tr>
                            <th>Data de Inclusão</th>
                            <td>{{ $licitacao->data_inclusao ? $licitacao->data_inclusao->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Data de Publicação no PNCP</th>
                            <td>{{ $licitacao->data_publicacao_pncp ? $licitacao->data_publicacao_pncp->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Data de Abertura</th>
                            <td>{{ $licitacao->data_abertura_proposta ? $licitacao->data_abertura_proposta->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Data de Encerramento</th>
                            <td>{{ $licitacao->data_encerramento_proposta ? $licitacao->data_encerramento_proposta->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>UF</th>
                            <td>{{ $licitacao->uf }}</td>
                        </tr>
                        <tr>
                            <th>Município</th>
                            <td>{{ $licitacao->municipio }}</td>
                        </tr>
                        <tr>
                            <th>Link do Sistema de Origem</th>
                            <td>
                                @if($licitacao->link_sistema_origem)
                                    <a href="{{ $licitacao->link_sistema_origem }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-link-45deg"></i> Acessar no Sistema de Origem
                                    </a>
                                @else
                                    <span class="text-muted">Não disponível</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline-item {
        border-left: 3px solid #4e73df;
        padding-left: 1rem;
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #4e73df;
        left: -7px;
        top: 0;
    }

    .progress {
        height: 10px;
        border-radius: 5px;
    }

    .relevancia-badge {
        height: 20px;
        width: 20px;
        line-height: 20px;
        text-align: center;
        border-radius: 50%;
        font-size: 0.7rem;
        color: white;
        margin-right: 5px;
        display: inline-block;
    }

    .relevancia-alta {
        background-color: #1cc88a;
    }

    .relevancia-media {
        background-color: #f6c23e;
    }

    .relevancia-baixa {
        background-color: #e74a3b;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Inicializar datepickers
        var datepickers = document.querySelectorAll('.datepicker');
        if (datepickers.length > 0) {
            // Usar código de inicialização do datepicker aqui
            // Exemplo para flatpickr:
            // flatpickr('.datepicker', { dateFormat: 'd/m/Y' });
        }
    });

    // Escutar eventos Livewire para notificações
    document.addEventListener('livewire:load', function() {
        Livewire.on('notify', function(message) {
            toastr.success(message);
        });

        Livewire.on('error', function(message) {
            toastr.error(message);
        });
    });
</script>
@endpush
