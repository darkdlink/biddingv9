<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Sistema Bidding') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Toastr para notificações -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            min-height: 100vh;
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            width: 250px;
            transition: all 0.3s;
        }

        .sidebar.collapsed {
            margin-left: -250px;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            font-weight: bold;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }

        .sidebar-brand img {
            margin-right: 1rem;
        }

        .sidebar-heading {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            font-weight: bold;
            padding: 0.5rem 1rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .content {
            margin-left: 250px;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .content.expanded {
            margin-left: 0;
        }

        .navbar {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            background-color: white;
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border: none;
            border-radius: 0.5rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Responsividade */
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar {{ session('sidebar_collapsed', false) ? 'collapsed' : '' }}">
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

                @if(auth()->check() && method_exists(auth()->user(), 'isUsuarioMaster') && auth()->user()->isUsuarioMaster())
                    <li class="nav-item">
                        <a href="{{ route('empresa.show') }}" class="nav-link {{ request()->routeIs('empresa.*') ? 'active' : '' }}">
                            <i class="bi bi-building"></i>
                            <span>Minha Empresa</span>
                        </a>
                    </li>
                @endif

                @if(auth()->check() && method_exists(auth()->user(), 'isAdminGrupo') && auth()->user()->isAdminGrupo())
                    <li class="nav-item">
                        <a href="{{ route('grupo.show') }}" class="nav-link {{ request()->routeIs('grupo.*') ? 'active' : '' }}">
                            <i class="bi bi-diagram-3"></i>
                            <span>Meu Grupo</span>
                        </a>
                    </li>
                @endif

                @if(auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
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

        <!-- Page Content -->
        <div class="content {{ session('sidebar_collapsed', false) ? 'expanded' : '' }}" id="page-content-wrapper">
            <!-- Top Navigation Bar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
                <div class="container-fluid">
                    <button class="btn btn-link text-dark" id="sidebarToggle">
                        <i class="bi bi-list fs-5"></i>
                    </button>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown ms-3">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-none d-lg-inline text-dark me-2">{{ auth()->user()->name }}</span>
                                <img class="rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4e73df&color=ffffff" alt="Avatar" width="32" height="32">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('perfil.show') }}">
                                    <i class="bi bi-person me-2"></i> Meu Perfil
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

            <!-- Main Content -->
            <div class="container-fluid">
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

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts básicos -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Configurações do Toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 5000
        };

        // Toggle da sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    const sidebar = document.querySelector('.sidebar');
                    const content = document.querySelector('.content');

                    sidebar.classList.toggle('collapsed');
                    content.classList.toggle('expanded');

                    // Salvar estado via Ajax
                    fetch('{{ route("sidebar.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            collapsed: sidebar.classList.contains('collapsed')
                        })
                    }).catch(error => console.error('Erro ao salvar estado da sidebar:', error));
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
