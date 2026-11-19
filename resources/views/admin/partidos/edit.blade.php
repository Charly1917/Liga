@extends('admin.layouts.app')

@section('content')

    <h1 class="text-3xl font-bold mb-6">Editar Partido</h1>

    {{-- Mensajes de éxito --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.partidos.update', $partido->id) }}" method="POST"
        class="bg-white shadow-lg rounded-lg p-6 space-y-5">
        @csrf
        @method('PUT')

        <!-- Equipo Local -->
        <div>
            <label class="block mb-1 font-semibold">Equipo Local</label>
            <div class="grid grid-cols-3 items-center text-center bg-gray-100 p-5 rounded">

                <div>
                    <h2 class="text-xl font-bold">
                        {{ $partido->equipoLocal->nombre }}
                    </h2>
                </div>


                <div class="text-2xl font-black">
                    VS
                </div>


                <div>
                    <h2 class="text-xl font-bold">
                        {{ $partido->equipoVisitante->nombre }}
                    </h2>
                </div>

            </div>
            </select>
        </div>


        <!-- Fecha -->
        <div>
            <label class="block mb-1 font-semibold">Fecha y Hora</label>
            <p class="text-gray-700">
                Fecha:
                {{ $partido->fecha->format('d/m/Y H:i') }}
            </p>
        </div>

        <!-- Goles -->
        <div class="flex gap-4">

            <div class="w-1/2 text-center">
                <label class="block font-bold mb-2">
                    {{ $partido->equipoLocal->nombre }}
                </label>

                <input type="number" name="goles_local" min="0" value="{{ $partido->goles_local }}"
                    class="w-full border p-3 text-center text-2xl">
            </div>


            <div class="w-1/2 text-center">

                <label class="block font-bold mb-2">
                    {{ $partido->equipoVisitante->nombre }}
                </label>

                <input type="number" name="goles_visitante" min="0" value="{{ $partido->goles_visitante }}"
                    class="w-full border p-3 text-center text-2xl">

            </div>

        </div>

        <!-- Botones -->
        <div class="flex justify-between mt-4">
            <a href="{{ route('admin.partidos.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">
                Volver
            </a>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                Actualizar Partido
            </button>
        </div>

    </form>

@endsection
