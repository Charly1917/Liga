@extends('admin.layouts.app')

@section('content')
    <a href="{{ route('admin.temporadas.show', $temporada->id) }}"
        class="inline-block mb-6 bg-gray-600 text-white px-4 py-2 rounded-lg">

        ← Regresar temporada

    </a>



    <h1 class="text-3xl md:text-4xl font-extrabold text-green-700 mb-6">

        {{ $temporada->nombre }} {{ $temporada->tipo }}

    </h1>




    <div class="max-w-5xl mx-auto">


        {{-- CONTROL DEL CARRUSEL --}}

        <div class="flex justify-between items-center mb-5">


            <button onclick="anteriorJornada()" class="bg-gray-700 text-white px-5 py-2 rounded-lg hover:bg-gray-800">

                ← Anterior

            </button>



            <h2 id="tituloJornada" class="text-2xl font-bold text-gray-700">

            </h2>




            <button onclick="siguienteJornada()" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700">

                Siguiente →

            </button>


        </div>





        {{-- JORNADAS --}}


        @foreach ($temporada->jornadas as $index => $jornada)
            <div class="jornada hidden" data-numero="{{ $jornada->numero }}">



                <div class="bg-white shadow-lg rounded-lg overflow-hidden">



                    <div class="bg-green-600 text-white p-4">

                        <h3 class="text-2xl font-bold">

                            Jornada {{ $jornada->numero }}

                        </h3>


                    </div>




                    <div class="overflow-x-auto">


                        <table class="w-full">


                            <thead class="bg-gray-100">


                                <tr>

                                    <th class="p-3 text-left">
                                        Local
                                    </th>


                                    <th class="p-3">
                                        Resultado
                                    </th>


                                    <th class="p-3 text-left">
                                        Visitante
                                    </th>


                                    <th class="p-3">
                                        Fecha
                                    </th>


                                </tr>


                            </thead>



                            <tbody>



                                @foreach ($jornada->partidos as $partido)
                                    <tr class="border-b">


                                        <td class="p-3 font-bold">

                                            {{ $partido->equipoLocal->nombre }}

                                        </td>




                                        <td class="p-3 text-center text-xl font-bold">


                                            {{ $partido->goles_local ?? '-' }}

                                            -

                                            {{ $partido->goles_visitante ?? '-' }}


                                        </td>




                                        <td class="p-3 font-bold">


                                            {{ $partido->equipoVisitante->nombre }}


                                        </td>




                                        <td class="p-3">


                                            {{ $partido->fecha ? $partido->fecha->format('d/m/Y H:i') : '-' }}


                                        </td>



                                    </tr>
                                @endforeach




                            </tbody>


                        </table>



                    </div>


                </div>


            </div>
        @endforeach



    </div>





    <script>
        let jornadaActual = 0;

        @if (isset($jornadaSeleccionada))

            jornadaActual =
                {{ $temporada->jornadas->search(function ($item) use ($jornadaSeleccionada) {
                    return $item->id == $jornadaSeleccionada->id;
                }) }};
        @endif

        const jornadas = document.querySelectorAll('.jornada');



        function mostrarJornada(index) {

            jornadas.forEach((jornada, i) => {


                if (i === index) {

                    jornada.classList.remove('hidden');

                } else {

                    jornada.classList.add('hidden');

                }


            });



            document.getElementById('tituloJornada').innerHTML =
                "Jornada " + (index + 1);


        }





        function siguienteJornada() {

            jornadaActual++;


            if (jornadaActual >= jornadas.length) {

                jornadaActual = 0;

            }


            mostrarJornada(jornadaActual);

        }





        function anteriorJornada() {

            jornadaActual--;


            if (jornadaActual < 0) {

                jornadaActual = jornadas.length - 1;

            }


            mostrarJornada(jornadaActual);

        }





        // cargar primera jornada

        // cargar jornada seleccionada

        if (jornadas.length > 0) {

            mostrarJornada(jornadaActual);

        }
    </script>
@endsection
