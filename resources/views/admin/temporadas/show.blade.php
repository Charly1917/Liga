@extends('admin.layouts.app')

@section('content')
    {{-- BOTÓN REGRESAR --}}
    <a href="{{ route('admin.temporadas.index') }}"
        class="inline-block mb-6 bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow">

        ← Regresar a temporadas

    </a>



    <div class="flex justify-between items-center mb-6">

        <div>

            <h1 class="text-4xl font-extrabold text-green-700">

                {{ $temporada->nombre }} {{ $temporada->tipo }}

            </h1>

            <p class="text-gray-600 mt-2">

                Detalle de la temporada

            </p>

        </div>


        @if ($temporada->activa)
            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg font-bold">

                Temporada Activa

            </span>
        @else
            <span class="bg-violet-100 text-violet-700 px-4 py-2 rounded-lg font-bold">
                {{ $temporada->campeon_id ? 'Campeón definido' : 'Liguilla en curso' }}
            </span>
        @endif


    </div>




    {{-- INFORMACIÓN GENERAL --}}

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">


        <div class="bg-blue-600 text-white rounded-lg shadow p-6">

            <h2 class="text-lg font-bold">
                Jornadas
            </h2>

            <p class="text-4xl font-extrabold mt-2">

                {{ $temporada->jornadas->count() }}

            </p>

        </div>



        <div class="bg-purple-600 text-white rounded-lg shadow p-6">

            <h2 class="text-lg font-bold">
                Partidos
            </h2>

            <p class="text-4xl font-extrabold mt-2">

                {{ $temporada->partidos->count() }}

            </p>

        </div>




        <div class="bg-green-600 text-white rounded-lg shadow p-6">

            <h2 class="text-lg font-bold">
                Inicio
            </h2>

            <p class="text-xl font-bold mt-2">

                {{ $temporada->fecha_inicio ? \Carbon\Carbon::parse($temporada->fecha_inicio)->format('d/m/Y') : '-' }}

            </p>

        </div>




        <div class="bg-red-600 text-white rounded-lg shadow p-6">

            <h2 class="text-lg font-bold">
                Finalización
            </h2>

            <p class="text-xl font-bold mt-2">

                {{ $temporada->fecha_fin ? \Carbon\Carbon::parse($temporada->fecha_fin)->format('d/m/Y') : '-' }}

            </p>

        </div>


    </div>





    {{-- ACCIONES --}}

    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">


        <h2 class="text-2xl font-bold mb-5 text-gray-700">

            Acciones

        </h2>



        <div class="flex flex-wrap gap-4">


            <a href="{{ route('admin.temporadas.jornadas', $temporada->id) }}"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg">

                Ver jornadas

            </a>


            <a href="{{ route('admin.temporadas.clasificacion', $temporada->id) }}"
                class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700">

                Ver clasificación

            </a>

            @if($temporada->eliminatorias->count() > 0)
                <a href="{{ route('admin.temporadas.liguilla', $temporada->id) }}"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg">
                    Ver liguilla y capturar resultados
                </a>
            @elseif($temporada->activa && $resultadosCompletos)
                <a href="{{ route('admin.temporadas.liguilla', $temporada->id) }}"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg">
                    Vista previa del formato
                </a>
                <a href="{{ route('admin.temporadas.finalizar', $temporada->id) }}"
                    onclick="return confirm('¿Crear y publicar la liguilla de esta temporada?')"
                    class="bg-amber-500 hover:bg-amber-600 text-[#0a1a44] px-5 py-2 rounded-lg font-bold">
                    Crear y publicar liguilla
                </a>
            @endif
        </div>


    </div>





    {{-- LISTA DE JORNADAS --}}

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">


        <div class="p-5 bg-gray-100">

            <h2 class="text-2xl font-bold">

                Jornadas de la temporada

            </h2>

        </div>



        <table class="w-full">


            <thead>

                <tr class="border-b">

                    <th class="p-3 text-left">
                        Jornada
                    </th>


                    <th class="p-3 text-left">
                        Partidos
                    </th>

                    <th class="p-3 text-left">
                        Acción
                    </th>

                </tr>

            </thead>



            <tbody>


                @forelse($temporada->jornadas as $jornada)
                    <tr class="border-b">


                        <td class="p-3 font-bold">

                            Jornada {{ $jornada->numero }}

                        </td>



                        <td class="p-3">

                            {{ $jornada->partidos->count() }} partidos

                        </td>

                        <td class="p-3">

                            <a href="{{ route('admin.temporadas.jornada', [
                                'id' => $temporada->id,
                                'jornada' => $jornada->id,
                            ]) }}"
                                class="bg-blue-600 text-white px-3 py-1 rounded">

                                Ver jornada

                            </a>

                        </td>


                    </tr>


                @empty


                    <tr>

                        <td colspan="2" class="p-5 text-center text-gray-500">
                            No hay jornadas registradas

                        </td>

                    </tr>
                @endforelse


            </tbody>


        </table>


    </div>
@endsection
