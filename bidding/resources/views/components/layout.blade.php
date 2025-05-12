<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema Bidding') }} - @yield('title', 'Painel')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @livewireStyles

    <!-- Styles adicionais -->
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

        .dropdown-menu-end {
            right: 0;
            left: auto;
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

        .card-header h5 {
            color: var(--dark-color);
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #224abe;
            border-color: #224abe;
        }

        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .timeline-item {
            border-left: 3px solid var(--primary-color);
            padding-left: 1rem;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--primary-color);
            left: -7px;
            top: 0;
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

    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <x-sidebar :collapsed="session('sidebar_collapsed', false)" />

        <!-- Page Content -->
        <div class="content {{ session('sidebar_collapsed', false) ? 'expanded' : '' }}" id="page-content-wrapper">
            <!-- Top Navigation Bar -->
            <x-navbar />

            <!-- Main Content -->
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    @livewireScripts

    <script>
        // Configuração do Toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 5000
        };

        // Exibir mensagens flash do Laravel
        @if(session()->has('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session()->has('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if(session()->has('info'))
            toastr.info("{{ session('info') }}");
        @endif

        @if(session()->has('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif

        // Toggle da sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
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
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
