<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - {{ config('app.name', 'Sistema Bidding') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Additional CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
        }

        .sidebar {
            background: linear-gradient(135deg, #212529 0%, #000000 100%);
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

        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }

        .card-header h5, .card-header h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 0;
        }

        .admin-page-title {
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            font-weight: 700;
        }

        .stats-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card.primary {
            border-left-color: var(--primary-color);
        }

        .stats-card.success {
            border-left-color: var(--secondary-color);
        }

        .stats-card.warning {
            border-left-color: var(--warning-color);
        }

        .stats-card.danger {
            border-left-color: var(--danger-color);
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-success {
            color: var(--secondary-color) !important;
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

    @livewireStyles
    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar {{ session('sidebar_collapsed', false) ? 'collapsed' : '' }}">
            <div class="sidebar-brand">
                <img src="{{ asset('images/logo-white.png') }}" alt="Admin Bidding" height="30">
                <span>Admin Bidding</span>
            </div>

            <hr class="sidebar-divider">

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

<li class="nav-item">
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Usuários</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.licencas.index') }}" class="nav-link {{ request()->routeIs('admin.licencas.*') ? 'active' : '' }}">
                        <i class="bi bi-key"></i>
                        <span>Licenças</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.empresas.index') }}" class="nav-link {{ request()->routeIs('admin.empresas.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Empresas</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.grupos.index') }}" class="nav-link {{ request()->routeIs('admin.grupos.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i>
                        <span>Grupos</span>
                    </a>
                </li>

                <hr class="sidebar-divider">

                <li class="nav-item">
                    <a href="{{ route('admin.licitacoes.index') }}" class="nav-link {{ request()->routeIs('admin.licitacoes.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Licitações</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.sincronizacao.index') }}" class="nav-link {{ request()->routeIs('admin.sincronizacao.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Sincronização PNCP</span>
                    </a>
                </li>

                <hr class="sidebar-divider">

                <li class="nav-item">
                    <a href="{{ route('admin.configuracoes') }}" class="nav-link {{ request()->routeIs('admin.configuracoes') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        <span>Configurações</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i>
                        <span>Logs do Sistema</span>
                    </a>
                </li>
            </ul>

            <hr class="sidebar-divider">

            <div class="text-center mt-3 mb-3">
                <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left"></i> Voltar ao Sistema
                </a>
            </div>
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

    <!-- Scripts -->
    @livewireScripts

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
                    fetch('{{ route("admin.sidebar.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            collapsed: sidebar.classList.contains('collapsed')
                        })
                    });
                });
            }

            // Inicializar DataTables se existirem na página
            if ($.fn.DataTable) {
                $('.datatable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
                    },
                    responsive: true
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
