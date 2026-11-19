@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">Próximas Jornadas</h1>

    @if($partidos->isEmpty())
        <p class="text-gray-600">No hay jornadas programadas.</p>
    @else
        <div class="space-y-4">
            @foreach($partidos as $partido)
                <div class="flex justify-between items-center border-b p-3">
                    <span class="font-semibold">{{ $partido->local->nombre }}</span>
                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($partido->fecha)->format('d M Y H:i') }}</span>
                    <span class="font-semibold">{{ $partido->visitante->nombre }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
