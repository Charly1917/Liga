<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Liga Española</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:Montserrat,sans-serif;background:#f4f7f6}</style>
</head>
<body class="min-h-screen text-slate-800">
    <header class="bg-[#0a1a44] text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="h-16 flex items-center justify-between gap-4">
                <a href="{{ route('admin') }}" class="flex items-center gap-3 min-w-0">
                    <span class="h-10 w-10 rounded-xl bg-green-500 flex items-center justify-center text-xl">⚽</span>
                    <span class="font-extrabold tracking-wide truncate">Liga <span class="text-green-300">·</span> Admin</span>
                </a>
                <div class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('admin') }}" class="px-3 py-2 rounded-lg text-sm font-semibold hover:bg-white/10 {{ request()->routeIs('admin') ? 'bg-white/10 text-green-200' : '' }}">Panel</a>
                    <a href="{{ route('admin.temporadas.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold hover:bg-white/10 {{ request()->routeIs('admin.temporadas.*') ? 'bg-white/10 text-green-200' : '' }}">Temporadas</a>
                    <a href="{{ route('admin.jornadas.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold hover:bg-white/10 {{ request()->routeIs('admin.jornadas.*') ? 'bg-white/10 text-green-200' : '' }}">Jornadas</a>
                    <a href="{{ route('admin.partidos.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold hover:bg-white/10 {{ request()->routeIs('admin.partidos.*') ? 'bg-white/10 text-green-200' : '' }}">Resultados</a>
                    <a href="{{ route('admin.equipos.index') }}" class="px-3 py-2 rounded-lg text-sm font-semibold hover:bg-white/10 {{ request()->routeIs('admin.equipos.*') ? 'bg-white/10 text-green-200' : '' }}">Equipos</a>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    <span class="text-sm text-blue-100">{{ auth()->user()->name ?? 'Administrador' }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm font-bold transition">Cerrar sesión</button>
                    </form>
                </div>
                <button id="admin-menu-btn" class="md:hidden rounded-lg p-2 text-2xl hover:bg-white/10" aria-label="Abrir menú" aria-expanded="false">☰</button>
            </div>
            <div id="admin-mobile-menu" class="hidden md:hidden pb-4">
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('admin') }}" class="rounded-lg px-3 py-2 hover:bg-white/10">Panel</a>
                    <a href="{{ route('admin.temporadas.index') }}" class="rounded-lg px-3 py-2 hover:bg-white/10">Temporadas</a>
                    <a href="{{ route('admin.jornadas.index') }}" class="rounded-lg px-3 py-2 hover:bg-white/10">Jornadas</a>
                    <a href="{{ route('admin.partidos.index') }}" class="rounded-lg px-3 py-2 hover:bg-white/10">Resultados</a>
                    <a href="{{ route('admin.equipos.index') }}" class="rounded-lg px-3 py-2 hover:bg-white/10">Equipos</a>
                    <a href="{{ route('inicio') }}" class="rounded-lg px-3 py-2 hover:bg-white/10">Sitio público</a>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 font-bold">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto w-full px-4 py-8 md:py-10">
        @yield('content')
    </main>

    <script>
        const adminMenuButton = document.getElementById('admin-menu-btn');
        const adminMobileMenu = document.getElementById('admin-mobile-menu');
        adminMenuButton.addEventListener('click', () => {
            const isHidden = adminMobileMenu.classList.toggle('hidden');
            adminMenuButton.setAttribute('aria-expanded', String(!isHidden));
        });
    </script>
</body>
</html>
