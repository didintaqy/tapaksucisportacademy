<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tapak Suci Sport Academy</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
        }
        h1 {
            color: #1a1a1a;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .email-field {
            margin-bottom: 1rem;
        }
        .email-field strong {
            display: block;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }
        .email-field p {
            margin: 0;
            color: #1a1a1a;
            font-size: 0.95rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div style="text-align:center;margin-bottom:1.5rem;">
            <img src="{{ $site['assets']['logo'] ?? asset('images/logo.svg') }}" alt="{{ $site['identity']['name'] ?? 'Tapak Suci Sport Academy' }}" style="max-height:60px;max-width:200px;">
        </div>
        @yield('content')
    </div>
</body>
</html>