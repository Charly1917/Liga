@extends('admin.layouts.app')

@section('content')
    <div class="mb-8">
        <p class="text-sm uppercase tracking-widest text-green-700 font-extrabold">Acta de partido</p>
        <h1 class="text-3xl md:text-4xl font-extrabold text-[#0a1a44] mt-1">Captura de resultados</h1>
        <p class="text-slate-500 mt-2">Registra en un solo envío los marcadores de todos los partidos de la jornada habilitada.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    @if(!$temporada)
        @if($temporadaLiguilla)
            <div class="rounded-2xl border border-violet-200 bg-white p-8 text-center shadow-sm">
                <p class="text-sm font-extrabold uppercase tracking-widest text-violet-700">Fase regular terminada</p>
                <h2 class="mt-2 text-2xl font-extrabold text-slate-800">La liguilla está lista para capturar resultados</h2>
                <p class="mt-2 text-slate-500">Registra los marcadores de cuartos, semifinales y final para definir al campeón.</p>
                <a href="{{ route('admin.temporadas.liguilla', $temporadaLiguilla->id) }}" class="mt-5 inline-flex rounded-xl bg-violet-700 px-5 py-3 font-bold text-white hover:bg-violet-800">
                    Capturar resultados de liguilla
                </a>
            </div>
        @else
            <div class="rounded-2xl bg-white p-8 text-center shadow-sm">
                <h2 class="text-xl font-extrabold text-slate-800">No hay temporada activa</h2>
                <p class="mt-2 text-slate-500">Crea una temporada para generar el calendario y capturar resultados.</p>
                <a href="{{ route('admin.temporadas.index') }}" class="mt-5 inline-flex rounded-xl bg-green-700 px-5 py-3 font-bold text-white hover:bg-green-800">Ir a temporadas</a>
            </div>
        @endif
    @else
        <div class="grid md:grid-cols-[1fr_auto] items-end gap-5 rounded-2xl bg-white border border-slate-200 p-5 md:p-6 shadow-sm mb-6">
            <div>
                <label for="jornada" class="mb-2 block text-sm font-bold text-slate-700">Selecciona una jornada</label>
                <form method="GET" action="{{ route('admin.partidos.index') }}">
                    <select id="jornada" name="jornada" onchange="this.form.submit()" class="w-full md:max-w-md rounded-xl border-slate-300 bg-slate-50 p-3 focus:border-green-600 focus:ring-green-600">
                        @forelse($jornadas as $jornada)
                            <option value="{{ $jornada->id }}" @selected($jornadaSeleccionada && $jornadaSeleccionada->is($jornada))>
                                Jornada {{ $jornada->numero }}
                                @if($jornadaHabilitada && $jornadaHabilitada->is($jornada)) · Captura actual
                                @elseif($jornadaHabilitada && $jornada->numero > $jornadaHabilitada->numero) · Bloqueada
                                @else · Cerrada
                                @endif
                            </option>
                        @empty
                            <option value="">No hay jornadas</option>
                        @endforelse
                    </select>
                </form>
            </div>
            <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-900">
                @if($jornadaHabilitada)
                    <span class="font-extrabold">Jornada habilitada: {{ $jornadaHabilitada->numero }}</span>
                    <p class="mt-1">Completa sus partidos para continuar.</p>
                @else
                    @if($liguillaCreada)
                        <span class="font-extrabold">Liguilla creada</span>
                        <p class="mt-1">Continúa capturando los resultados de eliminación directa.</p>
                        <a href="{{ route('admin.temporadas.liguilla', $temporada->id) }}"
                           class="mt-3 inline-flex rounded-lg bg-violet-700 px-4 py-2 font-bold text-white hover:bg-violet-800">
                            Ir a captura de liguilla
                        </a>
                    @else
                        <span class="font-extrabold">Temporada completa</span>
                        <p class="mt-1">Cierra la fase regular para generar la liguilla y capturar sus marcadores.</p>
                        <a href="{{ route('admin.temporadas.finalizar', $temporada->id) }}"
                           onclick="return confirm('¿Cerrar la fase regular y crear los cuartos de final?')"
                           class="mt-3 inline-flex rounded-lg bg-violet-700 px-4 py-2 font-bold text-white hover:bg-violet-800">
                            Crear y publicar liguilla
                        </a>
                    @endif
                @endif
            </div>
        </div>

        @if($jornadaSeleccionada)
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-extrabold text-[#0a1a44]">Jornada {{ $jornadaSeleccionada->numero }}</h2>
                @if($puedeCapturar)
                    <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-bold text-green-800">Lista para capturar</span>
                @else
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-600">Solo lectura</span>
                @endif
            </div>

            @if($puedeCapturar)
                <form action="{{ route('admin.partidos.guardarJornada') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jornada_id" value="{{ $jornadaSeleccionada->id }}">
                    <div class="grid xl:grid-cols-2 gap-4">
                    @foreach($partidos as $partido)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6 shadow-sm">
                                <div class="mb-5 flex items-center justify-between text-xs font-bold uppercase tracking-widest text-slate-400">
                                    <span>Partido {{ $loop->iteration }}</span>
                                    <span>{{ $partido->fecha->format('d/m/Y · H:i') }}</span>
                                </div>
                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                                    <div class="flex flex-col items-center gap-2 text-center">
                                        <x-equipo-escudo :equipo="$partido->equipoLocal" tamano="h-14 w-14" />
                                        <span class="font-bold text-slate-800">{{ $partido->equipoLocal->nombre }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="number" min="0" max="99" inputmode="numeric" required
                                               name="resultados[{{ $partido->id }}][goles_local]"
                                               value="{{ old('resultados.' . $partido->id . '.goles_local', $partido->goles_local) }}"
                                               aria-label="Goles de {{ $partido->equipoLocal->nombre }}"
                                               class="h-14 w-16 rounded-xl border-slate-300 text-center text-2xl font-extrabold focus:border-green-600 focus:ring-green-600">
                                        <span class="font-extrabold text-slate-300">-</span>
                                        <input type="number" min="0" max="99" inputmode="numeric" required
                                               name="resultados[{{ $partido->id }}][goles_visitante]"
                                               value="{{ old('resultados.' . $partido->id . '.goles_visitante', $partido->goles_visitante) }}"
                                               aria-label="Goles de {{ $partido->equipoVisitante->nombre }}"
                                               class="h-14 w-16 rounded-xl border-slate-300 text-center text-2xl font-extrabold focus:border-green-600 focus:ring-green-600">
                                    </div>
                                    <div class="flex flex-col items-center gap-2 text-center">
                                        <x-equipo-escudo :equipo="$partido->equipoVisitante" tamano="h-14 w-14" />
                                        <span class="font-bold text-slate-800">{{ $partido->equipoVisitante->nombre }}</span>
                                    </div>
                                </div>
                                @if($partido->lugar)
                                    <p class="mt-4 text-center text-xs text-slate-500">{{ $partido->lugar }}</p>
                                @endif
                        </article>
                    @endforeach
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="rounded-xl bg-green-700 px-6 py-3 font-extrabold text-white shadow hover:bg-green-800">
                            Registrar resultados de la jornada {{ $jornadaSeleccionada->numero }}
                        </button>
                    </div>
                </form>
            @else
                <div class="grid xl:grid-cols-2 gap-4">
                    @foreach($partidos as $partido)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <x-equipo-escudo :equipo="$partido->equipoLocal" tamano="h-12 w-12" />
                                    <span class="font-bold">{{ $partido->equipoLocal->nombre }}</span>
                                </div>
                                <span class="text-2xl font-extrabold text-[#0a1a44]">
                                    {{ $partido->goles_local ?? '—' }} - {{ $partido->goles_visitante ?? '—' }}
                                </span>
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <x-equipo-escudo :equipo="$partido->equipoVisitante" tamano="h-12 w-12" />
                                    <span class="font-bold">{{ $partido->equipoVisitante->nombre }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        @else
            <div class="rounded-2xl bg-white p-8 text-center text-slate-500 shadow-sm">No hay jornadas generadas para esta temporada.</div>
        @endif
    @endif
@endsection
