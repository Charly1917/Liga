@extends('admin.layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <p class="uppercase tracking-widest text-green-700 text-sm font-extrabold">Centro de control</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-[#0a1a44] mt-1">¡Hola, {{ auth()->user()->name }}!</h1>
            <p class="text-slate-500 mt-2">Administra la temporada, revisa el calendario y captura los marcadores.</p>
        </div>
        @if($temporada)
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.temporadas.show', $temporada->id) }}" class="inline-flex justify-center rounded-xl bg-green-700 hover:bg-green-800 text-white px-5 py-3 font-bold shadow">
                    {{ $temporada->nombre }} {{ $temporada->tipo }} · Ver temporada
                </a>
                @if($temporadaCompleta)
                    <a href="{{ route('admin.temporadas.liguilla', $temporada->id) }}" class="inline-flex justify-center rounded-xl bg-violet-700 hover:bg-violet-800 text-white px-5 py-3 font-bold shadow">
                        Vista previa del formato
                    </a>
                    <a href="{{ route('admin.temporadas.finalizar', $temporada->id) }}"
                       onclick="return confirm('¿Crear y publicar la liguilla de esta temporada?')"
                       class="inline-flex justify-center rounded-xl bg-amber-500 hover:bg-amber-600 text-[#0a1a44] px-5 py-3 font-bold shadow">
                        Crear y publicar liguilla
                    </a>
                @endif
            </div>
        @elseif($ultimaTemporada && !$ultimaTemporada->campeon_id)
            <a href="{{ route('admin.temporadas.liguilla', $ultimaTemporada->id) }}" class="inline-flex justify-center rounded-xl bg-violet-700 hover:bg-violet-800 text-white px-5 py-3 font-bold shadow">
                Continuar liguilla
            </a>
        @else
            <a href="{{ route('admin.temporadas.generar') }}" class="inline-flex justify-center rounded-xl bg-green-700 hover:bg-green-800 text-white px-5 py-3 font-bold shadow">
                Crear temporada
            </a>
        @endif
    </div>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => 'Jornadas activas', 'value' => $jornadas->count(), 'color' => 'text-violet-700', 'route' => 'admin.jornadas.index'],
            ['label' => 'Partidos programados', 'value' => $partidosCount, 'color' => 'text-blue-700', 'route' => 'admin.partidos.index'],
            ['label' => 'Equipos participantes', 'value' => $equiposTemporada->count(), 'color' => 'text-green-700', 'route' => 'admin.equipos.index'],
            ['label' => 'Temporadas', 'value' => \App\Models\Temporada::count(), 'color' => 'text-amber-700', 'route' => 'admin.temporadas.index'],
        ] as $card)
            <a href="{{ route($card['route']) }}" class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                <p class="text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
                <p class="text-3xl font-extrabold {{ $card['color'] }} mt-2">{{ $card['value'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid xl:grid-cols-[1.3fr_1fr] gap-6">
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 md:p-6 border-b border-slate-100 flex justify-between items-center gap-3">
                <div>
                    <h2 class="text-xl font-extrabold text-[#0a1a44]">Calendario de la temporada</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $jornadas->count() }} jornadas · todos contra todos</p>
                </div>
                <a href="{{ route('admin.jornadas.index') }}" class="text-sm font-bold text-green-700 hover:underline">Abrir calendario</a>
            </div>
            @if($jornadas->isEmpty())
                <p class="p-6 text-slate-500">
                    @if($ultimaTemporada && !$ultimaTemporada->campeon_id)
                        Completa la liguilla para coronar al campeón y habilitar una nueva temporada.
                    @else
                        Crea una temporada para generar el calendario de todos los equipos registrados.
                    @endif
                </p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($jornadas as $jornada)
                        <a href="{{ route('admin.jornadas.index', ['jornada' => $jornada->id]) }}" class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-slate-50">
                            <span class="font-bold text-slate-800">Jornada {{ $jornada->numero }}</span>
                            <span class="text-sm text-slate-500">{{ $jornada->partidos_count }} partidos</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 md:p-6">
            <div class="flex justify-between items-center mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-[#0a1a44]">Equipos</h2>
                    <p class="text-sm text-slate-500 mt-1">Participantes de la temporada</p>
                </div>
                <a href="{{ route('admin.equipos.index') }}" class="text-sm font-bold text-green-700 hover:underline">Gestionar</a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @forelse($equiposTemporada as $equipo)
                    <div class="flex items-center gap-2 rounded-xl bg-slate-50 p-3 min-w-0">
                        <x-equipo-escudo :equipo="$equipo" tamano="h-9 w-9" />
                        <span class="font-semibold text-sm truncate">{{ $equipo->nombre }}</span>
                    </div>
                @empty
                    <p class="col-span-2 text-slate-500">No hay equipos registrados.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.partidos.index') }}" class="mt-6 block text-center rounded-xl bg-[#0a1a44] hover:bg-blue-900 text-white px-4 py-3 font-bold">
                Capturar resultados
            </a>
        </section>
    </div>
@endsection
