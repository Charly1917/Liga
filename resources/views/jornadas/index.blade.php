@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-10 md:py-14">
        <div class="mb-8 text-center">
            <p class="font-bold uppercase tracking-[0.2em] text-emerald-700">Calendario oficial</p>
            <h1 class="mt-2 text-4xl font-black text-[#0a1a44]">Partidos y jornadas</h1>
            @if($temporada)
                <p class="mt-2 text-slate-500">{{ $temporada->nombre }} {{ $temporada->tipo }} · Consulta los enfrentamientos y marcadores.</p>
            @endif
        </div>

        @if($jornadas->isNotEmpty())
            <nav class="mb-8 flex flex-wrap justify-center gap-2" aria-label="Seleccionar jornada">
                @foreach($jornadas as $jornada)
                    <a href="#jornada-{{ $jornada->id }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-emerald-700">
                        Jornada {{ $jornada->numero }}
                    </a>
                @endforeach
            </nav>
        @endif

        <div class="space-y-8">
            @forelse($jornadas as $jornada)
                @php
                    $partidosParaCompartir = $jornada->partidos->map(function ($partido) {
                        return [
                            'fase' => 'PARTIDO',
                            'local' => $partido->equipoLocal->nombre,
                            'visitante' => $partido->equipoVisitante->nombre,
                            'goles_local' => $partido->goles_local,
                            'goles_visitante' => $partido->goles_visitante,
                            'fecha' => $partido->fecha->format('d/m/Y'),
                            'hora' => $partido->fecha->format('H:i'),
                            'lugar' => $partido->lugar,
                        ];
                    })->values();
                @endphp
                <section id="jornada-{{ $jornada->id }}" class="scroll-mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-4 bg-[#0a1a44] px-5 py-4 text-white sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-extrabold">Jornada {{ $jornada->numero }}</h2>
                            <span class="text-sm text-blue-100">{{ $jornada->partidos->count() }} partidos</span>
                        </div>
                        <button type="button"
                                data-compartir-rol="jornada-{{ $jornada->id }}"
                                data-titulo="Jornada {{ $jornada->numero }}"
                                data-subtitulo="{{ $temporada ? $temporada->nombre . ' ' . $temporada->tipo : '' }}"
                                data-nombre-archivo="jornada-{{ $jornada->numero }}"
                                class="inline-flex items-center justify-center rounded-xl bg-green-500 px-4 py-2 font-extrabold text-white shadow hover:bg-green-600">
                            Compartir rol por WhatsApp
                        </button>
                    </div>

                    @if($jornada->partidos->isNotEmpty())
                        <div class="divide-y divide-slate-100">
                            @foreach($jornada->partidos as $partido)
                                @php
                                    $resultadoCapturado = $partido->goles_local !== null && $partido->goles_visitante !== null;
                                @endphp
                                <article class="grid gap-3 px-4 py-5 sm:grid-cols-[1fr_auto_1fr] sm:items-center sm:px-6">
                                    <div class="flex min-w-0 items-center justify-center gap-3 sm:justify-end">
                                        <span class="truncate text-right font-bold text-slate-800">{{ $partido->equipoLocal->nombre }}</span>
                                        <x-equipo-escudo :equipo="$partido->equipoLocal" tamano="h-10 w-10" />
                                    </div>
                                    <div class="text-center">
                                        <p class="text-2xl font-black text-[#0a1a44]">
                                            @if($resultadoCapturado)
                                                {{ $partido->goles_local }} - {{ $partido->goles_visitante }}
                                            @else
                                                <span class="text-slate-300">VS</span>
                                            @endif
                                        </p>
                                        <p class="mt-1 text-xs font-semibold {{ $resultadoCapturado ? 'text-emerald-700' : 'text-slate-500' }}">
                                            {{ $resultadoCapturado ? 'Resultado final' : 'Por jugar' }}
                                        </p>
                                    </div>
                                    <div class="flex min-w-0 items-center justify-center gap-3 sm:justify-start">
                                        <x-equipo-escudo :equipo="$partido->equipoVisitante" tamano="h-10 w-10" />
                                        <span class="truncate font-bold text-slate-800">{{ $partido->equipoVisitante->nombre }}</span>
                                    </div>
                                    <p class="text-center text-xs text-slate-500 sm:col-span-3">
                                        Domingo · {{ $partido->fecha->format('d/m/Y · H:i') }}
                                        @if($partido->lugar) · {{ $partido->lugar }} @endif
                                    </p>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="p-6 text-center text-slate-500">Aún no hay partidos asignados a esta jornada.</p>
                    @endif
                    <script type="application/json" id="datos-rol-jornada-{{ $jornada->id }}">@json($partidosParaCompartir)</script>
                    <p id="estado-compartir-jornada-{{ $jornada->id }}" class="px-5 pb-4 text-sm text-slate-600" aria-live="polite"></p>
                </section>
            @empty
                <div class="rounded-2xl bg-white p-10 text-center text-slate-500 shadow-sm">
                    No hay jornadas programadas.
                </div>
            @endforelse
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('resultados') }}" class="inline-flex rounded-xl bg-emerald-700 px-5 py-3 font-bold text-white hover:bg-emerald-800">
                Ver todos los resultados
            </a>
        </div>
    </div>

    <script src="{{ asset('js/compartir-rol.js') }}" defer></script>
@endsection
