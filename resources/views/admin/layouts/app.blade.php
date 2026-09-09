<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Liga Española</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Montserrat', sans-serif; background: #f4f4f4; }
        .nav-link:hover { color: #facc15; } /* amarillo al pasar */
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- NAVBAR SUPERIOR --}}
    <nav class="bg-[#1e3a8a] text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl font-bold">Admin Panel - Liga Española</span>
            </div>

            {{-- Menú para pantallas grandes --}}
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('admin') }}" class="nav-link px-3 py-2 rounded font-medium">Inicio</a>
                <a href="{{ route('admin.equipos.index') }}" class="nav-link px-3 py-2 rounded font-medium">Equipos</a>
                <a href="{{ route('admin.partidos.index') }}" class="nav-link px-3 py-2 rounded font-medium">Partidos</a>
                <a href="{{ route('inicio') }}" class="nav-link px-3 py-2 rounded font-medium">Sitio Público</a>

                {{-- Cerrar sesión --}}
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white font-semibold transition">
                        Cerrar sesión
                    </button>
                </form>
            </div>

            {{-- Botón hamburguesa para móvil --}}
            <button id="menu-btn" class="md:hidden text-3xl">&#9776;</button>
        </div>

        {{-- Menú móvil --}}
        <div id="mobile-menu" class="hidden md:hidden bg-gray-800 px-4 pt-2 pb-4 flex flex-col gap-2">
            <a href="{{ route('admin') }}" class="nav-link px-3 py-2 rounded font-medium">Inicio</a>
            <a href="{{ route('admin.equipos.index') }}" class="nav-link px-3 py-2 rounded font-medium">Equipos</a>
            <a href="{{ route('admin.partidos.index') }}" class="nav-link px-3 py-2 rounded font-medium">Partidos</a>
            <a href="{{ route('inicio') }}" class="nav-link px-3 py-2 rounded font-medium">Sitio Público</a>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white font-semibold transition w-full">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </nav>

    {{-- CONTENIDO --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    {{-- Script para menú hamburguesa --}}
    <script>
        const btn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>

</body>
</html>
