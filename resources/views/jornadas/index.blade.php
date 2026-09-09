@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-10">
        <h1 class="text-4xl font-bold text-center mb-10">Jornadas Generadas</h1>

        @if ($jornadas->isEmpty())
            <p class="text-gray-600 text-center">No hay jornadas programadas.</p>
        @else
            @foreach ($jornadas as $jornada)
                <div class="mb-8 p-6 bg-white shadow rounded-lg">
                    <h2 class="text-2xl font-semibold mb-4">Jornada {{ $jornada->numero }}</h2>
                    <div class="space-y-2">
                        @foreach ($jornada->partidos as $partido)
                            <div class="flex justify-between border-b p-2">
                                <span class="font-semibold">{{ $partido->equipoLocal->nombre }}</span>
                                <span
                                    class="text-gray-700">{{ \Carbon\Carbon::parse($partido->fecha)->format('d/m/Y H:i') }}</span>
                                <span class="font-semibold">{{ $partido->equipoVisitante->nombre }}</span>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
