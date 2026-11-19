<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso administrador | {{ config('app.name', 'Liga Española') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-900">
    <div class="min-h-screen grid lg:grid-cols-2">
        <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-[#0a1a44] via-[#123b46] to-green-800 p-12 text-white">
            <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full border-[40px] border-white/5"></div>
            <div class="absolute -bottom-28 -left-20 h-96 w-96 rounded-full border-[50px] border-white/5"></div>
            <a href="{{ route('inicio') }}" class="relative flex items-center gap-3 text-xl font-extrabold tracking-wide">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-400 text-2xl">⚽</span>
                Liga Española
            </a>
            <div class="relative max-w-lg py-16">
                <p class="text-sm font-extrabold uppercase tracking-[0.3em] text-green-200">Panel de administración</p>
                <h1 class="mt-5 text-5xl font-extrabold leading-tight">Cada jornada, bajo control.</h1>
                <p class="mt-5 text-lg leading-relaxed text-blue-100">Organiza equipos, jornadas y resultados desde un solo lugar.</p>
                <div class="mt-10 flex gap-3">
                    <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm">8 equipos</span>
                    <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm">7 jornadas</span>
                    <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm">Liguilla</span>
                </div>
            </div>
            <p class="relative text-sm text-blue-100/70">Acceso privado para la administración de la liga</p>
        </aside>

        <main class="flex items-center justify-center bg-slate-50 px-5 py-10 sm:px-8 lg:px-12">
            <div class="w-full max-w-md">
                <a href="{{ route('inicio') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-bold text-slate-700 hover:text-green-800 lg:hidden">
                    <span class="text-xl">⚽</span> Liga Española
                </a>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl sm:p-8">
                    {{ $slot }}
                </div>
                <p class="mt-8 text-center text-xs text-slate-500">&copy; {{ date('Y') }} Liga Española · Administración</p>
            </div>
        </main>
    </div>
</body>
</html>
