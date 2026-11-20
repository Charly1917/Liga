@extends('layouts.app')

@section('content')
    <div class="bg-[#0a1a44] text-white">
        <div class="max-w-7xl mx-auto px-4 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <p class="uppercase tracking-[0.25em] text-red-300 font-bold mb-4">Fútbol · Comunidad · Competencia</p>
                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">Tu equipo merece jugar en grande.</h1>
                <p class="mt-5 text-lg text-blue-100 max-w-xl">
                    Una liga organizada para competir, convivir y llevar el fútbol local al siguiente nivel.
                    Temporadas con todos los equipos registrados, una vuelta de todos contra todos y liguilla por el campeonato.
                </p>
                <a href="https://wa.me/?text={{ urlencode('Hola, quiero informes para inscribir a mi equipo en la liga.') }}"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex mt-8 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg">
                    Solicitar información e inscribir equipo
                </a>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-2xl p-8">
                <div class="text-6xl mb-4" aria-hidden="true">⚽</div>
                <p class="text-sm uppercase tracking-widest text-blue-200">Formato de competencia</p>
                <p class="text-3xl font-extrabold mt-2">Todos los equipos · Todos contra todos</p>
                <p class="text-blue-100 mt-3">Los primeros 8 disputan el campeonato; los puestos 9.º al 16.º tienen liguilla de consolación.</p>
                @if($temporada)
                    <p class="mt-6 pt-5 border-t border-white/20">
                        <span class="text-blue-200">Temporada:</span>
                        <span class="font-bold">{{ $temporada->nombre }} {{ $temporada->tipo }}</span>
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        @if($temporada && $temporada->campeon)
            <section class="mb-6 flex flex-col items-center gap-5 rounded-3xl border border-yellow-200 bg-gradient-to-r from-amber-100 via-yellow-50 to-white p-7 text-center shadow-lg md:flex-row md:text-left">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-yellow-400 text-4xl shadow" aria-hidden="true">🏆</div>
                <div>
                    <p class="text-sm font-black uppercase tracking-widest text-amber-700">{{ $felicitacionCampeon }}</p>
                    <div class="mt-2 flex flex-col items-center gap-3 md:flex-row">
                        <x-equipo-escudo :equipo="$temporada->campeon" tamano="h-14 w-14" />
                        <h2 class="text-2xl font-extrabold text-[#0a1a44] md:text-3xl">{{ $temporada->campeon->nombre }}</h2>
                    </div>
                    <p class="mt-2 text-amber-800">¡Felicidades por conquistar {{ $temporada->nombre }} {{ $temporada->tipo }}!</p>
                </div>
            </section>
        @elseif($campeonAnterior)
            <section class="mb-6 flex flex-col items-center justify-between gap-4 rounded-2xl border border-yellow-200 bg-yellow-50 p-5 md:flex-row">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-amber-700">Campeón defensor</p>
                    <p class="mt-1 text-slate-700">
                        La liga felicita a <strong class="text-[#0a1a44]">{{ $campeonAnterior->nombre }}</strong>,
                        campeón de {{ $campeonAnteriorTemporada->nombre }} {{ $campeonAnteriorTemporada->tipo }}.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <x-equipo-escudo :equipo="$campeonAnterior" tamano="h-12 w-12" />
                    <span class="text-3xl" aria-hidden="true">🏆</span>
                </div>
            </section>
        @endif

        @if($temporada && $mostrarLiguillaPublica)
            <section class="mb-12 flex flex-col gap-5 rounded-2xl bg-[#0a1a44] p-6 text-white shadow md:flex-row md:items-center md:justify-between md:p-8">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-emerald-300">La fase final</p>
                    <h2 class="mt-1 text-2xl font-extrabold">Sigue todos los cruces</h2>
                    <p class="mt-1 text-blue-100">Cuartos, semifinales y final en un solo lugar.</p>
                </div>
                <a href="{{ route('liguilla') }}" class="inline-flex shrink-0 justify-center rounded-xl bg-yellow-400 px-5 py-3 font-extrabold text-[#0a1a44] hover:bg-yellow-300">
                    Ver resultados de la liguilla
                </a>
            </section>
        @endif

        <section class="mb-12">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
                <div>
                    <p class="text-sm uppercase tracking-widest text-green-700 font-bold">La temporada</p>
                    <h2 class="text-3xl font-extrabold text-[#0a1a44]">Tabla de posiciones</h2>
                </div>
                <a href="{{ route('clasificacion') }}" class="text-green-700 font-bold hover:underline">Ver clasificación completa →</a>
            </div>
            <div class="bg-white shadow rounded-xl overflow-x-auto">
                <table class="w-full text-left min-w-[700px]">
                    <thead class="bg-[#0a1a44] text-white">
                        <tr>
                            <th class="p-3 text-center">Pos</th>
                            <th class="p-3">Equipo</th>
                            <th class="p-3 text-center">PJ</th>
                            <th class="p-3 text-center">PG</th>
                            <th class="p-3 text-center">PE</th>
                            <th class="p-3 text-center">PP</th>
                            <th class="p-3 text-center">GF</th>
                            <th class="p-3 text-center">GC</th>
                            <th class="p-3 text-center">DG</th>
                            <th class="p-3 text-center">Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipos as $indice => $equipo)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 text-center font-bold">{{ $indice + 1 }}</td>
                                <td class="p-3 font-semibold whitespace-nowrap">
                                    <span class="inline-flex items-center gap-2">
                                        <x-equipo-escudo :equipo="$equipo" tamano="h-7 w-7" />
                                        {{ $equipo->nombre }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">{{ $equipo->pj ?? 0 }}</td>
                                <td class="p-3 text-center">{{ $equipo->pg ?? 0 }}</td>
                                <td class="p-3 text-center">{{ $equipo->pe ?? 0 }}</td>
                                <td class="p-3 text-center">{{ $equipo->pp ?? 0 }}</td>
                                <td class="p-3 text-center">{{ $equipo->gf ?? 0 }}</td>
                                <td class="p-3 text-center">{{ $equipo->gc ?? 0 }}</td>
                                <td class="p-3 text-center">{{ $equipo->dg ?? 0 }}</td>
                                <td class="p-3 text-center font-extrabold text-green-700">{{ $equipo->puntos ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="p-6 text-center text-gray-500">Aún no hay equipos en una temporada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="text-sm text-gray-500 mt-2">Puntuación: victoria 3 puntos, empate 1 y derrota 0. Desempates por diferencia de goles y goles a favor.</p>
        </section>

        <section class="grid lg:grid-cols-2 gap-8 mb-14">
            <div>
                <h2 class="text-2xl font-extrabold text-[#0a1a44] mb-4">Resultados recientes</h2>
                <div class="bg-white shadow rounded-xl p-5 space-y-3">
                    @forelse($resultados as $partido)
                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 border-b last:border-0 pb-3 last:pb-0">
                            <span class="font-semibold text-right">{{ $partido->equipoLocal->nombre }}</span>
                            <span class="font-extrabold text-lg">{{ $partido->goles_local }} - {{ $partido->goles_visitante }}</span>
                            <span class="font-semibold">{{ $partido->equipoVisitante->nombre }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500">Todavía no hay resultados capturados.</p>
                    @endforelse
                    <a href="{{ route('resultados') }}" class="inline-block text-green-700 font-bold hover:underline pt-2">Ver todos los resultados →</a>
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-[#0a1a44] mb-4">Próximos partidos</h2>
                <div class="bg-white shadow rounded-xl p-5 space-y-3">
                    @forelse($jornadas as $partido)
                        <div class="border-b last:border-0 pb-3 last:pb-0">
                            <div class="flex justify-between gap-3 font-semibold">
                                <span>{{ $partido->equipoLocal->nombre }}</span>
                                <span class="text-gray-400">vs</span>
                                <span>{{ $partido->equipoVisitante->nombre }}</span>
                            </div>
                            <p class="text-sm text-gray-500 text-center mt-1">{{ $partido->fecha->format('d/m/Y · H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">No hay partidos próximos programados.</p>
                    @endforelse
                    <a href="{{ route('jornadas') }}" class="inline-block text-green-700 font-bold hover:underline pt-2">Ver calendario →</a>
                </div>
            </div>
        </section>

        <section class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="bg-green-700 text-white p-7 md:p-9">
                <p class="uppercase tracking-widest text-green-100 text-sm font-bold">Inscripciones</p>
                <h2 class="text-3xl font-extrabold mt-2">Haz que tu equipo sea parte de la liga</h2>
                <p class="mt-2 text-green-50">Costos de referencia para una competencia de ocho equipos. Confirma disponibilidad y costos vigentes antes de inscribirte.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-5 p-7 md:p-9">
                <div class="rounded-xl bg-gray-50 border p-6">
                    <p class="text-gray-600 font-semibold">Inscripción por equipo / temporada</p>
                    <p class="text-4xl font-extrabold text-[#0a1a44] mt-2">$1,500 <span class="text-base font-semibold">MXN</span></p>
                    <p class="text-sm text-gray-500 mt-2">Incluye registro en el torneo y calendario de competencia.</p>
                </div>
                <div class="rounded-xl bg-gray-50 border p-6">
                    <p class="text-gray-600 font-semibold">Arbitraje por partido</p>
                    <p class="text-4xl font-extrabold text-[#0a1a44] mt-2">$450 <span class="text-base font-semibold">MXN</span></p>
                    <p class="text-sm text-gray-500 mt-2">Costo de referencia por encuentro; sujeto a confirmación.</p>
                </div>
                <div class="md:col-span-2 text-center">
                    <a href="https://wa.me/?text={{ urlencode('Hola, quiero informes sobre inscripción, arbitraje y disponibilidad para mi equipo.') }}"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex bg-[#0a1a44] hover:bg-blue-900 text-white font-bold py-3 px-7 rounded-lg">
                        Pedir informes por WhatsApp
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
