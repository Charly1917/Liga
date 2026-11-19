@extends('layouts.app')

@section('content')
    @php
        $cuartos = $temporada ? $temporada->eliminatorias->where('fase', 'Cuartos')->values() : collect();
        $semifinales = $temporada ? $temporada->eliminatorias->where('fase', 'Semifinal')->values() : collect();
        $final = $temporada ? $temporada->eliminatorias->where('fase', 'Final')->values() : collect();
        $crearPartidoCompartible = function ($partido, $fase, $numero, $localAlterno, $visitanteAlterno, $agenda = null) {
            $fecha = $partido ? $partido->fecha : ($agenda['fecha'] ?? null);

            return [
                'fase' => $fase . ' ' . $numero,
                'local' => optional($partido ? $partido->equipoLocal : null)->nombre ?? $localAlterno,
                'visitante' => optional($partido ? $partido->equipoVisitante : null)->nombre ?? $visitanteAlterno,
                'goles_local' => $partido ? $partido->goles_local : null,
                'goles_visitante' => $partido ? $partido->goles_visitante : null,
                'penales_local' => $partido ? $partido->penales_local : null,
                'penales_visitante' => $partido ? $partido->penales_visitante : null,
                'fecha' => $fecha ? $fecha->format('d/m/Y') : null,
                'hora' => $fecha ? $fecha->format('H:i') : null,
                'lugar' => $partido ? $partido->lugar : ($agenda['lugar'] ?? null),
            ];
        };
        $datosCuartos = $cuartos->map(fn ($partido, $indice) => $crearPartidoCompartible(
            $partido,
            'CUARTOS',
            '· CRUCE ' . ($indice + 1),
            'Por definir',
            'Por definir'
        ));
        $datosSemifinales = $semifinales->isNotEmpty()
            ? $semifinales->map(fn ($partido, $indice) => $crearPartidoCompartible(
                $partido,
                'SEMIFINAL',
                $indice + 1,
                'Por definir',
                'Por definir'
            ))
            : collect([
                ['local' => 'Ganador del cruce 1', 'visitante' => 'Ganador del cruce 4'],
                ['local' => 'Ganador del cruce 2', 'visitante' => 'Ganador del cruce 3'],
            ])->map(fn ($cruce, $indice) => $crearPartidoCompartible(
                null,
                'SEMIFINAL',
                $indice + 1,
                $cruce['local'],
                $cruce['visitante'],
                $programacionSemifinales->get($indice)
            ));
        $datosFinal = $final->isNotEmpty()
            ? $final->map(fn ($partido) => $crearPartidoCompartible(
                $partido,
                'FINAL',
                '',
                'Por definir',
                'Por definir'
            ))
            : collect([$crearPartidoCompartible(
                null,
                'FINAL',
                '',
                'Ganador de la semifinal 1',
                'Ganador de la semifinal 2',
                $programacionFinal
            )]);
        $partidosParaCompartir = $datosCuartos
            ->concat($datosSemifinales)
            ->concat($datosFinal)
            ->values();
    @endphp

    <section class="relative overflow-hidden bg-gradient-to-br from-[#071332] via-[#10265b] to-emerald-900 text-white">
        <div class="absolute -right-20 -top-24 h-80 w-80 rounded-full bg-emerald-400/10 blur-3xl"></div>
        <div class="absolute -bottom-36 left-1/4 h-80 w-80 rounded-full bg-yellow-300/10 blur-3xl"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-16 md:py-20">
            <p class="text-sm font-extrabold uppercase tracking-[0.3em] text-emerald-300">Liguilla e historial</p>
            @if($temporada)
                <h1 class="mt-4 max-w-3xl text-4xl font-black leading-tight md:text-6xl">
                    {{ $temporada->campeon ? '¡Tenemos campeón!' : ($temporadaTieneLiguilla ? 'El camino al campeonato' : 'La temporada está en curso') }}
                </h1>
                <p class="mt-4 text-xl font-bold text-emerald-200">{{ $temporada->nombre }} {{ $temporada->tipo }}</p>
                <p class="mt-3 max-w-2xl text-blue-100">
                    @if($temporada->campeon)
                        La temporada concluyó y aquí puedes consultar su cuadro y resultados.
                    @elseif($temporadaTieneLiguilla)
                        Sigue los cruces oficiales, los marcadores y el avance de los equipos.
                    @else
                        La fase regular sigue en disputa. La liguilla se publicará cuando se capturen todas las jornadas y el administrador la cree.
                    @endif
                </p>
            @else
                <h1 class="mt-4 max-w-3xl text-4xl font-black leading-tight md:text-6xl">Las liguillas de la liga</h1>
                <p class="mt-4 max-w-2xl text-lg text-blue-100">Aquí aparecerá el historial de eliminatorias cuando se creen las primeras liguillas.</p>
            @endif
        </div>
    </section>

    <main class="mx-auto max-w-7xl px-4 py-10 md:py-14">
        @if($temporadas->isNotEmpty())
            <section class="mb-10">
                <div class="mb-5">
                    <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-emerald-700">Archivo</p>
                    <h2 class="mt-1 text-2xl font-black text-[#0a1a44]">Temporadas y liguillas</h2>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($temporadas as $item)
                        @php
                            $itemTieneLiguilla = $item->eliminatorias_count > 0;
                            $itemEstado = $item->campeon_id
                                ? 'Temporada concluida'
                                : ($itemTieneLiguilla ? 'Liguilla en curso' : 'Fase regular en curso');
                            $itemSeleccionado = $temporada && $temporada->is($item);
                        @endphp
                        <a href="{{ route('liguilla', ['temporada' => $item->id]) }}"
                           class="rounded-2xl border p-5 transition hover:-translate-y-0.5 hover:shadow-md {{ $itemSeleccionado ? 'border-emerald-500 bg-emerald-50 shadow-sm' : 'border-slate-200 bg-white' }}">
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-extrabold text-[#0a1a44]">{{ $item->nombre }} {{ $item->tipo }}</span>
                                <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $item->campeon_id ? 'bg-yellow-100 text-yellow-800' : ($itemTieneLiguilla ? 'bg-violet-100 text-violet-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $itemEstado }}
                                </span>
                            </div>
                            @if($item->campeon)
                                <p class="mt-3 text-sm text-slate-600">Campeón: <strong class="text-slate-900">{{ $item->campeon->nombre }}</strong></p>
                            @elseif($itemTieneLiguilla)
                                <p class="mt-3 text-sm text-slate-600">Eliminatorias en disputa</p>
                            @else
                                <p class="mt-3 text-sm text-slate-600">La liguilla aún no ha sido creada</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!$temporada)
            <section class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm md:p-12">
                <div class="text-6xl" aria-hidden="true">🏆</div>
                <h2 class="mt-4 text-2xl font-black text-[#0a1a44]">Aún no hay liguillas registradas</h2>
                <p class="mx-auto mt-2 max-w-xl text-slate-600">Cuando la fase regular termine y el administrador cree los cruces, podrás consultarlos aquí.</p>
            </section>
        @elseif($temporada->campeon)
            <section class="relative mb-10 overflow-hidden rounded-3xl border border-yellow-200 bg-gradient-to-r from-amber-100 via-yellow-50 to-white p-7 shadow-lg md:p-10">
                <div class="absolute -right-8 -top-10 text-[10rem] opacity-[0.08]" aria-hidden="true">🏆</div>
                <div class="relative flex flex-col items-center gap-6 text-center md:flex-row md:text-left">
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-yellow-400 text-5xl shadow-lg" aria-hidden="true">🏆</div>
                    <div>
                        <p class="font-black uppercase tracking-[0.25em] text-amber-700">{{ $felicitacionCampeon }}</p>
                        <div class="mt-3 flex flex-col items-center gap-3 md:flex-row">
                            <x-equipo-escudo :equipo="$temporada->campeon" tamano="h-16 w-16" />
                            <h2 class="text-3xl font-black text-[#0a1a44] md:text-4xl">{{ $temporada->campeon->nombre }}</h2>
                        </div>
                        <p class="mt-3 text-lg font-bold text-amber-800">¡Campeón de {{ $temporada->nombre }} {{ $temporada->tipo }}! Felicidades por conquistar el título.</p>
                    </div>
                </div>
            </section>
        @endif

        @if($temporada && !$temporadaTieneLiguilla)
            <section class="rounded-2xl border border-blue-200 bg-blue-50 p-6 text-center">
                <p class="text-sm font-extrabold uppercase tracking-widest text-blue-800">{{ $estadoTemporada }}</p>
                <h2 class="mt-2 text-2xl font-black text-[#0a1a44]">La liguilla todavía no está habilitada</h2>
                <p class="mx-auto mt-2 max-w-2xl text-slate-600">Los cruces se mostrarán aquí después de registrar los resultados de todas las jornadas y de que el administrador cree la liguilla.</p>
            </section>
        @elseif($temporadaTieneLiguilla)
            <section id="cuadro-liguilla" class="scroll-mt-8">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-extrabold uppercase tracking-[0.2em] text-emerald-700">{{ $estadoTemporada }}</p>
                        <h2 class="mt-1 text-3xl font-black text-[#0a1a44]">Cuadro de la liguilla</h2>
                    </div>
                    <div class="flex flex-col gap-2 sm:items-end">
                        <button type="button"
                                data-compartir-rol="liguilla-{{ $temporada->id }}"
                                data-titulo="Liguilla oficial"
                                data-subtitulo="{{ $temporada->nombre }} {{ $temporada->tipo }}"
                                data-nombre-archivo="liguilla-{{ $temporada->id }}"
                                class="inline-flex items-center justify-center rounded-xl bg-green-600 px-4 py-3 font-extrabold text-white shadow hover:bg-green-700">
                            Compartir liguilla por WhatsApp
                        </button>
                        <p class="text-sm text-slate-500">En empate, el ganador se define en penales.</p>
                    </div>
                </div>
                <script type="application/json" id="datos-rol-liguilla-{{ $temporada->id }}">@json($partidosParaCompartir)</script>
                <p id="estado-compartir-liguilla-{{ $temporada->id }}" class="mb-4 text-sm text-slate-600" aria-live="polite"></p>

                <div class="grid gap-5 xl:grid-cols-3">
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <h3 class="text-xl font-black text-[#0a1a44]">Cuartos de final</h3>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-800">8 equipos</span>
                        </div>
                        <div class="space-y-3">
                            @forelse($cuartos as $partido)
                                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="mb-3 text-xs font-extrabold uppercase tracking-wider text-slate-500">Cruce {{ $loop->iteration }}</p>
                                    @if($partido->fecha)
                                        <p class="mb-2 text-xs font-semibold text-slate-500">
                                            Domingo · {{ $partido->fecha->format('d/m/Y · H:i') }}
                                            @if($partido->lugar) · {{ $partido->lugar }} @endif
                                        </p>
                                    @endif
                                    @foreach([
                                        ['equipo' => $partido->equipoLocal, 'id' => $partido->equipo_local_id, 'goles' => $partido->goles_local],
                                        ['equipo' => $partido->equipoVisitante, 'id' => $partido->equipo_visitante_id, 'goles' => $partido->goles_visitante],
                                    ] as $participante)
                                        <div class="flex items-center justify-between gap-3 py-2 {{ !$loop->last ? 'border-b border-slate-200' : '' }} {{ $partido->ganador_id === $participante['id'] ? 'font-extrabold text-emerald-700' : 'text-slate-700' }}">
                                            <span class="flex min-w-0 items-center gap-2">
                                                <x-equipo-escudo :equipo="$participante['equipo']" tamano="h-8 w-8" />
                                                <span class="truncate">{{ $participante['equipo']->nombre }}</span>
                                            </span>
                                            <span class="font-black">{{ $participante['goles'] ?? '—' }}</span>
                                        </div>
                                    @endforeach
                                    @if($partido->penales_local !== null)
                                        <p class="mt-2 text-center text-xs font-semibold text-slate-500">Penales: {{ $partido->penales_local }} - {{ $partido->penales_visitante }}</p>
                                    @endif
                                </article>
                            @empty
                                <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">Cruces por definir.</p>
                            @endforelse
                        </div>
                    </section>

                    @foreach([
                        ['titulo' => 'Semifinales', 'partidos' => $semifinales],
                        ['titulo' => 'Final', 'partidos' => $final],
                    ] as $fase)
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-5 flex items-center justify-between">
                                <h3 class="text-xl font-black text-[#0a1a44]">{{ $fase['titulo'] }}</h3>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold text-blue-800">{{ $fase['partidos']->count() }} {{ $fase['partidos']->count() === 1 ? 'partido' : 'partidos' }}</span>
                            </div>
                            <div class="space-y-3">
                                @forelse($fase['partidos'] as $partido)
                                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="mb-3 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                            {{ $fase['titulo'] === 'Final' ? 'Por el campeonato' : 'Semifinal ' . $loop->iteration }}
                                        </p>
                                        @if($partido->fecha)
                                            <p class="mb-2 text-xs font-semibold text-slate-500">
                                                Domingo · {{ $partido->fecha->format('d/m/Y · H:i') }}
                                                @if($partido->lugar) · {{ $partido->lugar }} @endif
                                            </p>
                                        @endif
                                        @foreach([
                                            ['equipo' => $partido->equipoLocal, 'id' => $partido->equipo_local_id, 'goles' => $partido->goles_local],
                                            ['equipo' => $partido->equipoVisitante, 'id' => $partido->equipo_visitante_id, 'goles' => $partido->goles_visitante],
                                        ] as $participante)
                                            <div class="flex items-center justify-between gap-3 py-2 {{ !$loop->last ? 'border-b border-slate-200' : '' }} {{ $partido->ganador_id === $participante['id'] ? 'font-extrabold text-emerald-700' : 'text-slate-700' }}">
                                                <span class="flex min-w-0 items-center gap-2">
                                                    <x-equipo-escudo :equipo="$participante['equipo']" tamano="h-8 w-8" />
                                                    <span class="truncate">{{ $participante['equipo']->nombre }}</span>
                                                </span>
                                                <span class="font-black">{{ $participante['goles'] ?? '—' }}</span>
                                            </div>
                                        @endforeach
                                        @if($partido->penales_local !== null)
                                            <p class="mt-2 text-center text-xs font-semibold text-slate-500">Penales: {{ $partido->penales_local }} - {{ $partido->penales_visitante }}</p>
                                        @endif
                                    </article>
                                @empty
                                    @if($fase['titulo'] === 'Semifinales')
                                        @foreach([
                                            ['nombre' => 'Semifinal 1', 'local' => 'Ganador del cruce 1', 'visitante' => 'Ganador del cruce 4'],
                                            ['nombre' => 'Semifinal 2', 'local' => 'Ganador del cruce 2', 'visitante' => 'Ganador del cruce 3'],
                                        ] as $cruce)
                                            <article class="rounded-xl border border-dashed border-violet-300 bg-violet-50/60 p-4">
                                                <p class="mb-3 text-xs font-extrabold uppercase tracking-wider text-violet-700">{{ $cruce['nombre'] }}</p>
                                                <p class="font-bold text-slate-700">{{ $cruce['local'] }}</p>
                                                <p class="my-2 text-center text-xs font-black uppercase tracking-widest text-slate-400">enfrenta a</p>
                                                <p class="font-bold text-slate-700">{{ $cruce['visitante'] }}</p>
                                                @if($programacionSemifinales->get($loop->index))
                                                    <p class="mt-3 text-xs font-semibold text-slate-500">
                                                        Domingo · {{ $programacionSemifinales->get($loop->index)['fecha']->format('d/m/Y · H:i') }}
                                                        · {{ $programacionSemifinales->get($loop->index)['lugar'] }}
                                                    </p>
                                                @endif
                                            </article>
                                        @endforeach
                                    @else
                                        <article class="rounded-xl border border-dashed border-yellow-300 bg-yellow-50 p-4">
                                            <p class="mb-3 text-xs font-extrabold uppercase tracking-wider text-yellow-800">Por el campeonato</p>
                                            <p class="font-bold text-slate-700">Ganador de la semifinal 1</p>
                                            <p class="my-2 text-center text-xs font-black uppercase tracking-widest text-slate-400">enfrenta a</p>
                                            <p class="font-bold text-slate-700">Ganador de la semifinal 2</p>
                                            @if($programacionFinal)
                                                <p class="mt-3 text-xs font-semibold text-slate-500">
                                                    Domingo · {{ $programacionFinal['fecha']->format('d/m/Y · H:i') }}
                                                    · {{ $programacionFinal['lugar'] }}
                                                </p>
                                            @endif
                                        </article>
                                    @endif
                                @endforelse
                            </div>
                        </section>
                    @endforeach
                </div>
            </section>
            <script src="{{ asset('js/compartir-rol.js') }}" defer></script>
        @endif
    </main>
@endsection
