@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="text-center mb-8">
            <p class="uppercase tracking-widest text-green-700 font-bold">Marcadores oficiales</p>
            <h1 class="text-4xl font-extrabold text-[#0a1a44] mt-2">Resultados</h1>
            @if($temporada)
                <p class="mt-2 text-slate-500">{{ $temporada->nombre }} {{ $temporada->tipo }}</p>
            @endif
        </div>

        <div class="space-y-4">
            @forelse($partidos as $partido)
                <article class="bg-white shadow-sm border border-slate-200 rounded-2xl p-5">
                    <p class="text-xs uppercase tracking-wide text-slate-500 text-center mb-3">
                        {{ $partido->fecha->format('d/m/Y · H:i') }}
                        @if($partido->jornada) · Jornada {{ $partido->jornada->numero }} @endif
                    </p>
                    <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                        <div class="flex items-center justify-end gap-3 min-w-0">
                            <span class="font-bold text-right truncate">{{ $partido->equipoLocal->nombre }}</span>
                            <x-equipo-escudo :equipo="$partido->equipoLocal" tamano="h-12 w-12" />
                        </div>
                        <span class="text-2xl md:text-3xl font-black text-[#0a1a44]">
                            {{ $partido->goles_local }} - {{ $partido->goles_visitante }}
                        </span>
                        <div class="flex items-center gap-3 min-w-0">
                            <x-equipo-escudo :equipo="$partido->equipoVisitante" tamano="h-12 w-12" />
                            <span class="font-bold truncate">{{ $partido->equipoVisitante->nombre }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="bg-white rounded-2xl p-10 text-center text-slate-500 shadow">
                    Aún no se han capturado resultados.
                </div>
            @endforelse
        </div>

        @if($eliminatorias->isNotEmpty())
            <section class="mt-12">
                <div class="mb-5 text-center">
                    <p class="text-sm font-bold uppercase tracking-widest text-violet-700">Fase final</p>
                    <h2 class="mt-1 text-3xl font-extrabold text-[#0a1a44]">Resultados de la liguilla</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ $temporadaLiguilla->nombre }} {{ $temporadaLiguilla->tipo }}</p>
                </div>
                <div class="space-y-4">
                    @foreach($eliminatorias as $partido)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="mb-3 text-center text-xs font-extrabold uppercase tracking-widest text-violet-700">
                                {{ $partido->fase }}
                            </p>
                            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-4">
                                <div class="flex min-w-0 items-center justify-end gap-3">
                                    <span class="truncate text-right font-bold {{ $partido->ganador_id === $partido->equipo_local_id ? 'text-emerald-700' : 'text-slate-800' }}">{{ $partido->equipoLocal->nombre }}</span>
                                    <x-equipo-escudo :equipo="$partido->equipoLocal" tamano="h-10 w-10" />
                                </div>
                                <span class="text-2xl font-black text-[#0a1a44]">{{ $partido->goles_local }} - {{ $partido->goles_visitante }}</span>
                                <div class="flex min-w-0 items-center gap-3">
                                    <x-equipo-escudo :equipo="$partido->equipoVisitante" tamano="h-10 w-10" />
                                    <span class="truncate font-bold {{ $partido->ganador_id === $partido->equipo_visitante_id ? 'text-emerald-700' : 'text-slate-800' }}">{{ $partido->equipoVisitante->nombre }}</span>
                                </div>
                            </div>
                            @if($partido->penales_local !== null)
                                <p class="mt-3 text-center text-xs font-semibold text-slate-500">Penales: {{ $partido->penales_local }} - {{ $partido->penales_visitante }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('liguilla') }}" class="font-bold text-violet-700 hover:underline">Ver el cuadro de la liguilla →</a>
                </div>
            </section>
        @endif
    </div>
@endsection
