<nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
    <div class="container-fluid">
        <button class="btn btn-link text-dark" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    @if(auth()->user()->alertasNaoLidos()->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ auth()->user()->alertasNaoLidos()->count() }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">Alertas</h6>

                    @forelse(auth()->user()->alertasRecentes() as $alerta)
                        <a class="dropdown-item d-flex align-items-center {{ !$alerta->lido ? 'bg-light' : '' }}" href="{{ route('licitacoes.show', $alerta->licitacao_id) }}">
                            <div class="me-3">
                                <div class="icon-circle bg-primary text-white">
                                    <i class="bi bi-bell"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-muted">{{ $alerta->created_at->format('d/m/Y H:i') }}</div>
                                <span class="{{ !$alerta->lido ? 'fw-bold' : '' }}">{{ $alerta->titulo }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="dropdown-item text-center">Nenhum alerta recente</div>
                    @endforelse

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center small text-muted" href="#">Ver todos os alertas</a>
                </div>
            </li>

            <li class="nav-item dropdown ms-3">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-lg-inline text-dark me-2">{{ auth()->user()->name }}</span>
                    <img class="rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4e73df&color=ffffff" alt="Avatar" width="32" height="32">
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('perfil.show') }}">
                        <i class="bi bi-person me-2"></i> Meu Perfil
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-gear me-2"></i> Configurações
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right me-2"></i> Sair
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
