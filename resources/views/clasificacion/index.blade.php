@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold">Tabla de Clasificación</h1>
        @if($temporada)
            <p class="text-gray-600 mt-2">{{ $temporada->nombre }} {{ $temporada->tipo }}</p>
        @endif
    </div>

    <div class="bg-white shadow rounded-lg p-6 overflow-x-auto">
        <table class="w-full text-left min-w-[700px]">
            <thead class="bg-gray-100">
                <tr class="border-b">
                    <th class="p-2 text-center">Pos</th>
                    <th class="p-2">Equipo</th>
                    <th class="p-2 text-center">PJ</th>
                    <th class="p-2 text-center">PG</th>
                    <th class="p-2 text-center">PE</th>
                    <th class="p-2 text-center">PP</th>
                    <th class="p-2 text-center">GF</th>
                    <th class="p-2 text-center">GC</th>
                    <th class="p-2 text-center">DG</th>
                    <th class="p-2 text-center">Pts</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipos as $indice => $equipo)
                    <tr class="border-b">
                        <td class="p-2 text-center font-bold">{{ $indice + 1 }}</td>
                        <td class="p-2 font-semibold">
                            <span class="inline-flex items-center gap-2">
                                <x-equipo-escudo :equipo="$equipo" tamano="h-7 w-7" />
                                {{ $equipo->nombre }}
                            </span>
                        </td>
                        <td class="p-2 text-center">{{ $equipo->pj ?? 0 }}</td>
                        <td class="p-2 text-center">{{ $equipo->pg ?? 0 }}</td>
                        <td class="p-2 text-center">{{ $equipo->pe ?? 0 }}</td>
                        <td class="p-2 text-center">{{ $equipo->pp ?? 0 }}</td>
                        <td class="p-2 text-center">{{ $equipo->gf ?? 0 }}</td>
                        <td class="p-2 text-center">{{ $equipo->gc ?? 0 }}</td>
                        <td class="p-2 text-center">{{ $equipo->dg ?? 0 }}</td>
                        <td class="p-2 text-center font-bold">{{ $equipo->puntos ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="p-6 text-center text-gray-500">Aún no hay equipos con partidos en esta temporada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-sm text-gray-500 mt-3">Victoria: 3 puntos · Empate: 1 punto · Derrota: 0 puntos. La tabla desempata por diferencia de goles y goles a favor.</p>

</div>
@endsection
