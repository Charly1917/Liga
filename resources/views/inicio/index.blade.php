@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- TÍTULO --}}
    <h1 class="text-4xl font-bold text-center mb-10">Liga Española - Resultados, Jornadas y Clasificación</h1>

    {{-- RESULTADOS DE LA SEMANA --}}
    <section class="mb-16">
        <h2 class="text-2xl font-semibold mb-4">Resultados Recientes</h2>

        <div class="bg-white shadow rounded-lg p-6">
            @if($resultados->isEmpty())
                <p class="text-gray-600">No hay resultados registrados esta semana.</p>
            @else
                <div class="space-y-4">
                    @foreach($resultados as $partido)
                        <div class="flex justify-between items-center border-b pb-3">
                            <span class="font-semibold">{{ $partido->equipoLocal->nombre ?? 'Sin equipo' }}</span>

                            <span class="text-xl font-bold">
                                {{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visitante ?? '-' }}
                            </span>

                            <span class="font-semibold">{{ $partido->equipoVisitante->nombre ?? 'Sin equipo' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- PRÓXIMAS JORNADAS --}}
    <section class="mb-16">
        <h2 class="text-2xl font-semibold mb-4">Próximas Jornadas</h2>

        <div class="bg-white shadow rounded-lg p-6">
            @if($jornadas->isEmpty())
                <p class="text-gray-600">No hay jornadas programadas.</p>
            @else
                <div class="space-y-4">
                    @foreach($jornadas as $partido)
                        <div class="flex justify-between items-center border-b pb-3">
                            <span class="font-semibold">{{ $partido->equipoLocal->nombre ?? 'Sin equipo' }}</span>

                            <span class="text-lg text-gray-700">
                                {{ \Carbon\Carbon::parse($partido->fecha)->format('d M Y H:i') }}
                            </span>

                            <span class="font-semibold">{{ $partido->equipoVisitante->nombre ?? 'Sin equipo' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- TABLA DE CLASIFICACIÓN --}}
    <section class="mb-16">
        <h2 class="text-2xl font-semibold mb-4">Tabla de Clasificación</h2>

        <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="p-2">Equipo</th>
                        <th class="p-2 text-center">PJ</th>
                        <th class="p-2 text-center">GF</th>
                        <th class="p-2 text-center">GC</th>
                        <th class="p-2 text-center">Pts</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipos as $equipo)
                        <tr class="border-b">
                            <td class="p-2 font-semibold flex items-center gap-2">
                                <img src="{{ asset('img/equipos/' . $equipo->logo) }}" class="w-6 h-6" alt="">
                                {{ $equipo->nombre }}
                            </td>
                            <td class="p-2 text-center">{{ $equipo->pj ?? 0 }}</td>
                            <td class="p-2 text-center">{{ $equipo->gf ?? 0 }}</td>
                            <td class="p-2 text-center">{{ $equipo->gc ?? 0 }}</td>
                            <td class="p-2 text-center font-bold">{{ $equipo->puntos ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- BOTÓN DE INSCRIPCIÓN --}}
    <div class="text-center mt-10">
        <a href="https://wa.me/5210000000000?text=Hola%20quiero%20inscribir%20un%20equipo"
           target="_blank"
           class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-full shadow">
            Inscribir equipo vía WhatsApp
        </a>
    </div>

</div>
@endsection
