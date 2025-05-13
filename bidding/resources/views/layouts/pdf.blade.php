<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Documento') - {{ config('app.name', 'Sistema Bidding') }}</title>

    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #333;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        h1, h2, h3, h4, h5, h6 {
            color: #444;
        }

        h1 {
            font-size: 22pt;
            margin-bottom: 15px;
        }

        h2 {
            font-size: 18pt;
            margin-bottom: 10px;
        }

        h3 {
            font-size: 16pt;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .container {
            padding: 20px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/logo.png') }}" alt="Sistema Bidding" class="logo">
    </header>

    <footer>
        <div>
            <p>Sistema Bidding &copy; {{ date('Y') }} - Documento gerado em {{ now()->format('d/m/Y H:i') }}</p>
            <p>Página <span class="pagenum"></span></p>
        </div>
    </footer>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>
