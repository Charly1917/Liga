@extends('admin.layouts.app')

@section('content')
    <div class="mb-8">
        <p class="text-sm uppercase tracking-widest text-green-700 font-extrabold">Calendario de juego</p>
        <h1 class="text-3xl md:text-4xl font-extrabold text-[#0a1a44] mt-1">Jornadas</h1>
        @if($temporada)
            <p class="text-slate-500 mt-2">{{ $temporada->nombre }} {{ $temporada->tipo }} · {{ $jornadas->count() }} jornadas</p>
        @endif
    </div>

    @if(!$temporada)
        <div class="rounded-2xl bg-white p-8 text-center shadow-sm">
            <p class="text-slate-500">No hay una temporada activa.</p>
            <a href="{{ route('admin.temporadas.index') }}" class="mt-4 inline-flex rounded-xl bg-green-700 px-5 py-3 font-bold text-white">Administrar temporadas</a>
        </div>
    @else
        <div class="grid lg:grid-cols-[280px_1fr] gap-6">
            <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-extrabold text-slate-900 mb-4">Jornadas</h2>
                <nav class="space-y-2">
                    @foreach($jornadas as $jornada)
                        <a href="{{ route('admin.jornadas.index', ['jornada' => $jornada->id]) }}"
                           class="flex items-center justify-between gap-2 rounded-xl border px-4 py-3 {{ $jornadaSeleccionada && $jornadaSeleccionada->is($jornada) ? 'border-green-300 bg-green-50 text-green-900' : 'border-slate-100 hover:bg-slate-50' }}">
                            <span class="font-bold">Jornada {{ $jornada->numero }}</span>
                            <span class="text-xs text-slate-500">{{ $jornada->partidos_count }} partidos</span>
                        </a>
                    @endforeach
                </nav>
                @if($jornadaHabilitada)
                    <p class="mt-5 rounded-xl bg-amber-50 p-3 text-sm text-amber-900">
                        Captura actual: <strong>Jornada {{ $jornadaHabilitada->numero }}</strong>
                    </p>
                @else
                    <p class="mt-5 rounded-xl bg-green-50 p-3 text-sm text-green-900">Todas las jornadas tienen resultados.</p>
                @endif
            </aside>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 md:p-7 shadow-sm">
                @if($jornadaSeleccionada)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                        <div>
                            <h2 class="text-2xl font-extrabold text-[#0a1a44]">Jornada {{ $jornadaSeleccionada->numero }}</h2>
                            <p class="text-sm text-slate-500 mt-1">{{ $jornadaSeleccionada->partidos_count }} partidos programados</p>
                        </div>
                        @if($puedeCapturar)
                            <a href="{{ route('admin.partidos.index', ['jornada' => $jornadaSeleccionada->id]) }}" class="inline-flex justify-center rounded-xl bg-green-700 px-4 py-3 text-sm font-extrabold text-white hover:bg-green-800">Capturar resultados</a>
                        @else
                            <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-bold text-slate-600">Solo lectura</span>
                        @endif
                    </div>
                    <div class="space-y-3">
                        @forelse($partidos as $partido)
                            <article class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 rounded-xl border border-slate-100 p-4">
                                <div class="flex items-center justify-end gap-3 min-w-0">
                                    <span class="font-bold text-right truncate">{{ $partido->equipoLocal->nombre }}</span>
                                    <x-equipo-escudo :equipo="$partido->equipoLocal" tamano="h-10 w-10" />
                                </div>
                                <span class="font-extrabold text-[#0a1a44]">
                                    @if($partido->goles_local !== null && $partido->goles_visitante !== null)
                                        {{ $partido->goles_local }} - {{ $partido->goles_visitante }}
                                    @else
                                        vs
                                    @endif
                                </span>
                                <div class="flex items-center gap-3 min-w-0">
                                    <x-equipo-escudo :equipo="$partido->equipoVisitante" tamano="h-10 w-10" />
                                    <span class="font-bold truncate">{{ $partido->equipoVisitante->nombre }}</span>
                                </div>
                            </article>
                        @empty
                            <p class="text-slate-500">Esta jornada aún no tiene partidos.</p>
                        @endforelse
                    </div>
                @else
                    <p class="text-slate-500">No hay jornadas programadas para esta temporada.</p>
                @endif
            </section>
        </div>
    @endif
@endsection
