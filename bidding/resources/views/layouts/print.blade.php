<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Imprimir') - {{ config('app.name', 'Sistema Bidding') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS - versão mínima para impressão -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: white;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .print-header img {
            max-height: 60px;
        }

        .print-header h1 {
            font-size: 24px;
            margin: 10px 0 0;
        }

        .print-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }

        .print-date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }

        /* Botão de impressão */
        .print-button {
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #4e73df;
            color: white;
            text-align: center;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        .print-button:hover {
            background-color: #2653d4;
        }
    </style>
</head>
<body>
    <div class="print-header">
        <img src="{{ asset('images/logo.png') }}" alt="Sistema Bidding">
        <h1>@yield('title', 'Documento para Impressão')</h1>
    </div>

    <div class="print-date">
        Gerado em: {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="container">
        @yield('content')
    </div>

    <div class="print-footer">
        &copy; {{ date('Y') }} {{ config('app.name', 'Sistema Bidding') }}. Todos os direitos reservados.
    </div>

    <button class="print-button no-print" onclick="window.print()">
        Imprimir Documento
    </button>

    <script>
        // Auto-imprimir após carregar
        window.onload = function() {
            // Esperar 1 segundo para garantir que todos os estilos foram aplicados
            setTimeout(function() {
                // Perguntar se deseja imprimir automaticamente
                if (confirm('Deseja imprimir este documento agora?')) {
                    window.print();
                }
            }, 1000);
        };
    </script>
</body>
</html>
