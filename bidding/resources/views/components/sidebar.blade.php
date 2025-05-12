<div class="sidebar {{ $collapsed ? 'collapsed' : '' }}">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo-white.png') }}" alt="Sistema Bidding" height="30">
        <span>Sistema Bidding</span>
    </div>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Principal</div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('licitacoes.index') }}" class="nav-link {{ request()->routeIs('licitacoes.*') ? 'active' : '' }}">
                <i class="bi bi-search"></i>
                <span>Licitações</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('propostas.index') }}" class="nav-link {{ request()->routeIs('propostas.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Minhas Propostas</span>
            </a>
        </li>
    </ul>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Configurações</div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('segmentos.index') }}" class="nav-link {{ request()->routeIs('segmentos.*') ? 'active' : '' }}">
                <i class="bi bi-tag"></i>
                <span>Segmentos</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('perfil.show') }}" class="nav-link {{ request()->routeIs('perfil.*') ? 'active' : '' }}">
                <i class="bi bi-person"></i>
                <span>Meu Perfil</span>
            </a>
        </li>

        @if(auth()->user()->isUsuarioMaster())
            <li class="nav-item">
                <a href="{{ route('empresa.show') }}" class="nav-link {{ request()->routeIs('empresa.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i>
                    <span>Minha Empresa</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->isAdminGrupo())
            <li class="nav-item">
                <a href="{{ route('grupo.show') }}" class="nav-link {{ request()->routeIs('grupo.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i>
                    <span>Meu Grupo</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->isAdmin())
            <hr class="sidebar-divider">

            <div class="sidebar-heading">Administração</div>

            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Painel Admin</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.licencas.index') }}" class="nav-link {{ request()->routeIs('admin.licencas.*') ? 'active' : '' }}">
                    <i class="bi bi-key"></i>
                    <span>Licenças</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Usuários</span>
                </a>
            </li>
        @endif
    </ul>
</div>
