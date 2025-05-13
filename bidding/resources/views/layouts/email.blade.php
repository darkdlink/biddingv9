<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ config('app.name', 'Sistema Bidding') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
            line-height: 1.6;
        }

        .wrapper {
            width: 100%;
            background-color: #f4f6f9;
            padding: 30px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #4e73df;
            padding: 20px;
            text-align: center;
            color: white;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #eeeeee;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4e73df;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            margin: 16px 0;
        }

        .button:hover {
            background-color: #2653d4;
        }

        h1, h2, h3, h4 {
            color: #2c3e50;
            margin-top: 0;
        }

        p {
            margin-bottom: 16px;
        }

        .social-links {
            margin-top: 16px;
        }

        .social-links a {
            margin: 0 8px;
            text-decoration: none;
        }

        @media only screen and (max-width: 620px) {
            .container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img src="{{ asset('images/logo-white.png') }}" alt="{{ config('app.name') }}" class="logo">
                <h1>@yield('title', 'Sistema Bidding')</h1>
            </div>

            <div class="content">
                @yield('content')
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
                <p>Este é um email automático, não responda.</p>

                <div class="social-links">
                    <a href="#" title="Facebook">Facebook</a>
                    <a href="#" title="Twitter">Twitter</a>
                    <a href="#" title="Instagram">Instagram</a>
                    <a href="#" title="LinkedIn">LinkedIn</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
