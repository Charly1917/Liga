@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <h1 class="text-4xl font-bold text-center mb-10">Tabla de Clasificación</h1>

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

</div>
@endsection
