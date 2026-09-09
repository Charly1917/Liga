<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liga Española</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Montserrat --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">

    {{-- SwiperJS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f6f7fb;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">

    {{-- NAVBAR --}}
    <nav class="bg-[#0a1a44] text-white shadow-xl border-b-4 border-red-600">
        <div class="max-w-7xl mx-auto px-5 py-4 flex justify-between items-center">

            {{-- Logo + Nombre --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-600 flex justify-center items-center font-bold text-lg">⚽
                </div>
                <span class="text-2xl font-extrabold tracking-wide">Liga Española</span>
            </div>

            {{-- Links PC --}}
            <div class="hidden md:flex gap-8 text-lg font-medium">
                <a href="{{ route('inicio') }}" class="hover:text-red-400 transition-all">Inicio</a>
                <a href="{{ route('clasificacion') }}" class="hover:text-red-400 transition-all">Clasificación</a>
                <a href="{{ route('jornadas') }}" class="hover:text-red-400 transition-all">Jornadas</a>
                <a href="{{ route('resultados') }}" class="hover:text-red-400 transition-all">Resultados</a>
            </div>

            {{-- Botón móvil --}}
            <button id="menu-btn" class="md:hidden text-3xl">&#9776;</button>
        </div>

        {{-- Menú móvil --}}
        <div id="mobile-menu" class="hidden bg-[#0a1a44] md:hidden px-6 pb-4">
            <a href="{{ route('inicio') }}" class="block py-2">Inicio</a>
            <a href="{{ route('clasificacion') }}" class="block py-2">Clasificación</a>
            <a href="{{ route('jornadas') }}" class="block py-2">Jornadas</a>
            <a href="{{ route('resultados') }}" class="block py-2">Resultados</a>
        </div>
    </nav>

    {{-- CONTENIDO --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- CARRUSEL DE EQUIPOS --}}
    <section class="bg-white py-10 mt-10 shadow-inner">
        <h2 class="text-center text-2xl font-bold text-[#0a1a44] mb-8 tracking-wide">Equipos Participantes</h2>

        <div class="max-w-7xl mx-auto px-4">
            <!-- Swiper -->
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    @foreach ($equipos as $equipo)
                        <div class="swiper-slide flex flex-col items-center">
                            <img src="{{ asset('img/equipos/' . $equipo->logo) }}"
                                class="w-28 h-28 object-contain mb-2 drop-shadow-lg" alt="{{ $equipo->nombre }}">
                            <p class="text-center text-sm font-semibold text-gray-700">{{ $equipo->nombre }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-[#0a1a44] text-white py-8 mt-12 border-t-4 border-red-600">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-sm">&copy; {{ date('Y') }} Liga Española – Proyecto de Carlos</p>
            <p class="text-xs opacity-80 mt-1">Sitio informativo deportivo – No oficial</p>
        </div>
    </footer>

    {{-- Scripts --}}
    <script>
        // Menú móvil
        const btn = document.getElementById('menu-btn');
        const mobile = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => mobile.classList.toggle('hidden'));

        // Carrusel Swiper mostrando 3 y avanzando otros 3
        const swiper = new Swiper(".mySwiper", {
            slidesPerView: 3, 
            slidesPerGroup: 3,
            spaceBetween: 30,
            loop: true,
            centeredSlides: true, 
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            breakpoints: {
                0: {
                    slidesPerView: 3,
                    slidesPerGroup: 3
                },
                640: {
                    slidesPerView: 3,
                    slidesPerGroup: 3
                },
                1024: {
                    slidesPerView: 3,
                    slidesPerGroup: 3
                }
            }
        });
    </script>


</body>

</html>
