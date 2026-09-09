@extends('admin.layouts.app')

@section('content')
    <h1 class="text-4xl font-extrabold text-red-700 mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Tarjeta Temporadas --}}
        <div class="bg-green-600 shadow-lg rounded-lg p-6 text-white">

            <h2 class="text-xl font-bold mb-2">
                Temporadas
            </h2>

            <p class="text-3xl font-extrabold">
                {{ \App\Models\Temporada::count() }}
            </p>

            <a href="{{ route('admin.temporadas.index') }}"
                class="mt-4 inline-block bg-white text-green-600 py-2 px-4 rounded font-semibold hover:bg-gray-100">

                Gestionar Temporadas

            </a>
        </div>

        {{-- Tarjeta Jornadas --}}
        <div class="bg-purple-600 shadow-lg rounded-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-2">Jornadas</h2>
            <p class="text-3xl font-extrabold">{{ \App\Models\Jornada::count() }}</p>

            <a href="{{ route('admin.jornadas.index') }}"
                class="mt-4 inline-block bg-white text-purple-600 py-2 px-4 rounded font-semibold hover:bg-gray-100">
                Ver Jornadas
            </a>
        </div>


        {{-- Tarjeta Equipos --}}
        <div class="bg-red-600 shadow-lg rounded-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-2">Equipos</h2>
            <p class="text-3xl font-extrabold">{{ \App\Models\Equipo::count() }}</p>
            <a href="{{ route('admin.equipos.index') }}"
                class="mt-4 inline-block bg-white text-red-600 py-2 px-4 rounded font-semibold hover:bg-gray-100">
                Gestionar Equipos
            </a>
        </div>

        {{-- Tarjeta Partidos --}}
        <div class="bg-blue-600 shadow-lg rounded-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-2">Partidos</h2>
            <p class="text-3xl font-extrabold">{{ \App\Models\Partido::count() }}</p>
            <a href="{{ route('admin.partidos.index') }}"
                class="mt-4 inline-block bg-white text-blue-600 py-2 px-4 rounded font-semibold hover:bg-gray-100">
                Gestionar Partidos
            </a>
        </div>

        {{-- Tarjeta Opcional --}}
        <div class="bg-yellow-500 shadow-lg rounded-lg p-6 text-white">
            <h2 class="text-xl font-bold mb-2">Usuarios</h2>
            <p class="text-3xl font-extrabold">1</p>
            <a href="#"
                class="mt-4 inline-block bg-white text-yellow-500 py-2 px-4 rounded font-semibold hover:bg-gray-100">
                Gestionar Usuarios
            </a>
        </div>

    </div>
@endsection
