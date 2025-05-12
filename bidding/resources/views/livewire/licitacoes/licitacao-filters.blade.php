<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtros</h5>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="aplicarFiltros">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="uf" class="form-label">Estado (UF)</label>
                    <select id="uf" class="form-select" wire:model="uf">
                        <option value="">Todos os estados</option>
                        @foreach($ufs as $estado)
                            <option value="{{ $estado }}">{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="modalidade" class="form-label">Modalidade</label>
                    <select id="modalidade" class="form-select" wire:model="modalidade">
                        <option value="">Todas as modalidades</option>
                        @foreach($modalidades as $mod)
                            <option value="{{ $mod }}">{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="dataMin" class="form-label">Data de Encerramento (De)</label>
                    <input type="date" id="dataMin" class="form-control" wire:model="dataMin">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="dataMax" class="form-label">Data de Encerramento (Até)</label>
                    <input type="date" id="dataMax" class="form-control" wire:model="dataMax">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="valorMin" class="form-label">Valor Estimado (Mínimo)</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" id="valorMin" class="form-control" wire:model="valorMin" min="0" step="100">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="valorMax" class="form-label">Valor Estimado (Máximo)</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" id="valorMax" class="form-control" wire:model="valorMax" min="0" step="100">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="interesse" class="form-label">Status de Interesse</label>
                    <select id="interesse" class="form-select" wire:model="interesse">
                        <option value="">Todos</option>
                        <option value="1">Com interesse</option>
                        <option value="0">Sem interesse</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="termoBusca" class="form-label">Busca por Texto</label>
                    <input type="text" id="termoBusca" class="form-control" wire:model.defer="termoBusca" placeholder="Buscar no objeto ou órgão...">
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-outline-secondary" wire:click="resetarFiltros">
                    <i class="bi bi-x-circle"></i> Limpar Filtros
                </button>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
</div>
