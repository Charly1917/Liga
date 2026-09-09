<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Liga Española') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gradient-to-br from-red-700 via-red-900 to-black text-gray-100">

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        <!-- Nombre de la liga -->
        <div class="text-center">
            <h1 class="text-4xl font-extrabold tracking-widest uppercase text-white drop-shadow-lg">
                {{ config('app.name', 'Liga Española') }}
            </h1>
            <p class="text-red-300 text-sm mt-1">Sistema Oficial – Administración</p>
        </div>

        <!-- Contenedor principal -->
        <div class="w-90 sm:max-w-md mt-8 px-6 py-8 bg-white dark:bg-gray-800 shadow-2xl sm:rounded-xl border border-red-600">
            {{ $slot }}
        </div>
    </div>

</body>
</html>
