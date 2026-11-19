@extends('admin.layouts.app')

@section('content')
    {{-- BOTÓN REGRESAR DASHBOARD --}}
    <a href="{{ route('admin') }}"
        class="inline-block mb-6 bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow">

        ← Regresar

    </a>



    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">


        <h1 class="text-3xl md:text-4xl font-extrabold text-green-700">

            Temporadas

        </h1>



        @if($puedeCrearTemporada)
            <a href="{{ route('admin.temporadas.generar') }}"
                class="bg-green-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-green-700 text-center">
                Nueva temporada
            </a>
        @else
            <span class="bg-slate-200 text-slate-600 px-5 py-2 rounded-lg font-bold text-center">
                Espera a que se corone al campeón
            </span>
        @endif

    </div>

    <p class="text-gray-600 mb-6">
        Cada temporada se genera con los primeros 8 equipos registrados y 7 jornadas a una vuelta. La siguiente se habilita cuando termina la liguilla y se registra al campeón.
    </p>





    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-5">

            {{ session('success') }}

        </div>
    @endif





    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-5">

            {{ session('error') }}

        </div>
    @endif





    <div class="bg-white shadow-lg rounded-lg overflow-hidden">


        <div class="overflow-x-auto">


            <table class="min-w-full">


                <thead class="bg-gray-100">


                    <tr>


                        <th class="p-3 text-left whitespace-nowrap">
                            Año
                        </th>


                        <th class="p-3 text-left whitespace-nowrap">
                            Tipo
                        </th>


                        <th class="p-3 text-left whitespace-nowrap">
                            Estado
                        </th>


                        <th class="p-3 text-left whitespace-nowrap">
                            Inicio
                        </th>


                        <th class="p-3 text-left whitespace-nowrap">
                            Fin
                        </th>


                        <th class="p-3 text-left whitespace-nowrap">
                            Acciones
                        </th>


                    </tr>


                </thead>




                <tbody>



                    @forelse($temporadas as $temporada)
                        <tr class="border-b hover:bg-gray-50">





                            <td class="p-3 font-bold whitespace-nowrap">

                                {{ $temporada->nombre }}

                            </td>







                            <td class="p-3 whitespace-nowrap">


                                @if ($temporada->tipo == 'Apertura')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm">

                                        {{ $temporada->tipo }}

                                    </span>
                                @else
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded text-sm">

                                        {{ $temporada->tipo }}

                                    </span>
                                @endif


                            </td>









                            <td class="p-3 whitespace-nowrap">


                                @if ($temporada->activa)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm">

                                        Activa

                                    </span>
                                @elseif ($temporada->campeon_id)
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded text-sm">
                                        Campeón definido
                                    </span>
                                @else
                                    <span class="bg-violet-100 text-violet-700 px-3 py-1 rounded text-sm">
                                        Liguilla en curso
                                    </span>
                                @endif


                            </td>









                            <td class="p-3 whitespace-nowrap text-sm">


                                {{ $temporada->fecha_inicio ? \Carbon\Carbon::parse($temporada->fecha_inicio)->format('d/m/Y') : '-' }}


                            </td>









                            <td class="p-3 whitespace-nowrap text-sm">


                                {{ $temporada->fecha_fin ? \Carbon\Carbon::parse($temporada->fecha_fin)->format('d/m/Y') : '-' }}


                            </td>









                            <td class="p-3">


                                <div class="flex flex-wrap gap-2">





                                    <a href="{{ route('admin.temporadas.show', $temporada->id) }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">

                                        Ver

                                    </a>







                                    @if ($temporada->activa)
                                        <a href="{{ route('admin.temporadas.finalizar', $temporada->id) }}"
                                            onclick="return confirm('¿Seguro que deseas finalizar esta temporada?')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">

                                            Cerrar fase regular

                                        </a>
                                    @else
                                        <a href="{{ route('admin.temporadas.liguilla', $temporada->id) }}"
                                            class="bg-violet-700 hover:bg-violet-800 text-white px-3 py-1 rounded text-sm">
                                            {{ $temporada->campeon_id ? 'Ver campeón' : 'Continuar liguilla' }}
                                        </a>
                                    @endif




                                </div>


                            </td>




                        </tr>




                    @empty



                        <tr>


                            <td colspan="6" class="p-6 text-center text-gray-500">

                                No hay temporadas creadas


                            </td>


                        </tr>
                    @endforelse





                </tbody>



            </table>



        </div>



    </div>
@endsection
