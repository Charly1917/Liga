@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <div class="bg-white shadow rounded-lg p-6 flex flex-col md:flex-row items-center gap-6">
        <img src="{{ asset('img/equipos/' . $equipo->logo) }}" class="w-32 h-32 object-contain" alt="{{ $equipo->nombre }}">
        <div>
            <h1 class="text-3xl font-bold">{{ $equipo->nombre }}</h1>
            @if($equipo->entrenador)
                <p class="text-gray-700 mt-2">Entrenador: {{ $equipo->entrenador }}</p>
            @endif
        </div>
    </div>

    {{-- Partidos del equipo --}}
    <section class="mt-10">
        <h2 class="text-2xl font-semibold mb-4">Partidos</h2>
        <div class="bg-white shadow rounded-lg p-6">
            @if($partidos->isEmpty())
                <p class="text-gray-600">No hay partidos registrados.</p>
            @else
                <div class="space-y-4">
                    @foreach($partidos as $partido)
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">{{ $partido->local->nombre }}</span>
                            <span class="text-gray-700">{{ $partido->goles_local ?? '-' }} - {{ $partido->goles_visita ?? '-' }}</span>
                            <span class="font-semibold">{{ $partido->visita->nombre }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</div>
@endsection
