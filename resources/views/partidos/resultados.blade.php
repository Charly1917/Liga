@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">Resultados de la Semana</h1>

    @if($partidos->isEmpty())
        <p class="text-gray-600">No hay resultados.</p>
    @else
        <div class="space-y-4">
            @foreach($partidos as $partido)
                <div class="flex justify-between items-center border-b p-3">
                    <span class="font-semibold">{{ $partido->equipoLocal->nombre }}</span>
                    <span class="text-xl font-bold">{{ $partido->goles_local ?? 0 }} - {{ $partido->goles_visitante ?? 0 }}</span>
                    <span class="font-semibold">{{ $partido->equipoVisitante->nombre }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
