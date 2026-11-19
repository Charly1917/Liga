@extends('admin.layouts.app')

@section('content')
    @php
        $cuartos = $temporada->eliminatorias->where('fase', 'Cuartos')->sortBy('id')->values();
        $semis = $temporada->eliminatorias->where('fase', 'Semifinal')->sortBy('id')->values();
        $final = $temporada->eliminatorias->where('fase', 'Final')->sortBy('id')->values();
    @endphp

    <a href="{{ route('admin.temporadas.show', $temporada->id) }}"
       class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-slate-900 mb-6">
        ← Regresar a temporada
    </a>

    <div class="mb-8">
        <p class="text-sm uppercase tracking-widest text-violet-700 font-extrabold">Fase final</p>
        <h1 class="text-3xl md:text-4xl font-extrabold text-[#0a1a44] mt-1">
            Liguilla {{ $temporada->nombre }} {{ $temporada->tipo }}
        </h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-5">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 mb-5">{{ session('error') }}</div>
    @endif

    @if($vistaPrevia)
        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 md:p-6 mb-6">
            <h2 class="font-extrabold text-violet-950">Vista previa del formato mexicano</h2>
            <p class="text-sm text-violet-900 mt-1">
                Los cruces son provisionales y se actualizan con la clasificación actual. Al terminar la temporada se fijarán las posiciones y se habilitará la captura de la liguilla.
            </p>
        </div>

        <div class="grid xl:grid-cols-4 gap-5">
            <section class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                <h2 class="font-extrabold text-[#0a1a44] text-lg mb-4">Cuartos de final</h2>
                <div class="space-y-3">
                    @foreach([
                        ['numero' => 1, 'local' => 0, 'visitante' => 7, 'localSeed' => 1, 'visitanteSeed' => 8],
                        ['numero' => 2, 'local' => 1, 'visitante' => 6, 'localSeed' => 2, 'visitanteSeed' => 7],
                        ['numero' => 3, 'local' => 2, 'visitante' => 5, 'localSeed' => 3, 'visitanteSeed' => 6],
                        ['numero' => 4, 'local' => 3, 'visitante' => 4, 'localSeed' => 4, 'visitanteSeed' => 5],
                    ] as $cruce)
                        @php
                            $local = $clasificacion->get($cruce['local']);
                            $visitante = $clasificacion->get($cruce['visitante']);
                        @endphp
                        <article class="rounded-xl border border-slate-100 p-3">
                            <p class="text-xs uppercase tracking-wide text-slate-400 font-extrabold mb-2">Cruce {{ $cruce['numero'] }}</p>
                            <div class="flex items-center justify-between gap-2">
                                <span class="flex items-center gap-2 font-bold">
                                    <span class="text-xs text-slate-400">{{ $cruce['localSeed'] }}.º</span>
                                    @if($local)<x-equipo-escudo :equipo="$local" tamano="h-8 w-8" />{{ $local->nombre }}
                                    @else lugar @endif
                                </span>
                                <span class="text-sm text-slate-400">vs</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between gap-2">
                                <span class="flex items-center gap-2 font-bold">
                                    <span class="text-xs text-slate-400">{{ $cruce['visitanteSeed'] }}.º</span>
                                    @if($visitante)<x-equipo-escudo :equipo="$visitante" tamano="h-8 w-8" />{{ $visitante->nombre }}
                                    @else lugar @endif
                                </span>
                                <span class="text-xs font-bold text-slate-400">
                                    {{ $local ? $local->puntos . ' pts' : '' }}
                                    @if($visitante) · {{ $visitante->puntos }} pts @endif
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                <h2 class="font-extrabold text-[#0a1a44] text-lg mb-4">Semifinales</h2>
                <article class="rounded-xl border border-slate-100 p-4 mb-4">
                    <p class="text-xs uppercase tracking-wide text-violet-700 font-extrabold">Semifinal 1</p>
                    <p class="mt-2 font-bold">Ganador del cruce 1</p>
                    <p class="text-center text-slate-400 my-1">vs</p>
                    <p class="font-bold">Ganador del cruce 4</p>
                </article>
                <article class="rounded-xl border border-slate-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-violet-700 font-extrabold">Semifinal 2</p>
                    <p class="mt-2 font-bold">Ganador del cruce 2</p>
                    <p class="text-center text-slate-400 my-1">vs</p>
                    <p class="font-bold">Ganador del cruce 3</p>
                </article>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                <h2 class="font-extrabold text-[#0a1a44] text-lg mb-4">Final</h2>
                <article class="rounded-xl border border-slate-100 p-4">
                    <p class="font-bold">Ganador de la semifinal 1</p>
                    <p class="text-center text-slate-400 my-2">vs</p>
                    <p class="font-bold">Ganador de la semifinal 2</p>
                </article>
            </section>

            <section class="rounded-2xl bg-yellow-50 border border-yellow-200 p-5 shadow-sm text-center">
                <h2 class="font-extrabold text-yellow-900 text-lg mb-4">Campeón</h2>
                <div class="text-5xl" aria-hidden="true">🏆</div>
                <p class="text-sm text-yellow-800 mt-3">Ganador de la final</p>
            </section>
        </div>
    @else
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 mb-6 text-blue-950">
            <p class="font-extrabold">Orden de los cruces</p>
            <p class="text-sm mt-1">Semifinal 1: ganador del cruce 1 contra ganador del cruce 4. Semifinal 2: ganador del cruce 2 contra ganador del cruce 3.</p>
        </div>

        <div class="overflow-x-auto pb-10">
            <div class="flex gap-8 min-w-[1000px]">
                <section class="w-80">
                    <h2 class="text-center text-xl font-extrabold mb-6 text-slate-700">CUARTOS DE FINAL</h2>
                    <div class="space-y-8">
                        @foreach($cuartos as $index => $partido)
                            @include('admin.temporadas.partials.partido_eliminatoria', [
                                'partido' => $partido,
                                'numeroCruce' => 'Cruce ' . ($index + 1),
                            ])
                        @endforeach
                    </div>
                </section>

                <section class="w-80 pt-16">
                    <h2 class="text-center text-xl font-extrabold mb-6 text-slate-700">SEMIFINALES</h2>
                    <div class="space-y-32">
                        @foreach($semis as $index => $partido)
                            @include('admin.temporadas.partials.partido_eliminatoria', [
                                'partido' => $partido,
                                'numeroCruce' => 'Semifinal ' . ($index + 1),
                            ])
                        @endforeach
                    </div>
                </section>

                <section class="w-80 pt-40">
                    <h2 class="text-center text-xl font-extrabold mb-6 text-slate-700">FINAL</h2>
                    @foreach($final as $partido)
                        @include('admin.temporadas.partials.partido_eliminatoria', [
                            'partido' => $partido,
                            'numeroCruce' => 'Final',
                        ])
                    @endforeach
                </section>

                <section class="w-80 pt-52 text-center">
                    <h2 class="text-center text-xl font-extrabold mb-6 text-yellow-700">CAMPEÓN</h2>
                    @if($temporada->campeon)
                        <div class="rounded-2xl border-2 border-yellow-400 bg-yellow-50 p-7 shadow">
                            <div class="text-5xl mb-3" aria-hidden="true">🏆</div>
                            <x-equipo-escudo :equipo="$temporada->campeon" tamano="h-16 w-16 mx-auto" />
                            <p class="font-extrabold text-yellow-900 mt-3">{{ $temporada->campeon->nombre }}</p>
                        </div>
                    @else
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-7 text-slate-400">
                            <div class="text-5xl mb-3" aria-hidden="true">🏆</div>
                            <p>Por definir</p>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    @endif
@endsection
