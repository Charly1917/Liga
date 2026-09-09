@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-10">

        <h1 class="text-4xl font-bold text-center mb-10">Todos los Equipos</h1>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach ($equipos as $equipo)
                <a href="{{ route('equipos.show', $equipo->id) }}"
                    class="bg-white shadow rounded-lg p-4 hover:scale-105 transition">
                    <img src="{{ asset('img/equipos/' . $equipo->logo) }}" class="w-full h-24 object-contain mb-2"
                        alt="{{ $equipo->nombre }}">
                    <h2 class="text-center font-semibold">{{ $equipo->nombre }}</h2>
                </a>
            @endforeach
        </div>

    </div>
@endsection
