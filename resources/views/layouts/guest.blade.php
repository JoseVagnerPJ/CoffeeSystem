<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            *, *::before, *::after { box-sizing: border-box; }
            body { margin: 0; min-width: 0; }
            @media (max-width: 767.98px) {
                body { min-height: 100vh; }
                input, select, textarea, button { font-size: 16px; }
                .px-6 { padding-inline: 1rem !important; }
                .sm\:max-w-md { max-width: 100% !important; }
                .sm\:rounded-lg { border-radius: 0.75rem !important; }
                .w-full { max-width: 100%; }
            }
        </style>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
    </body>
</html>
