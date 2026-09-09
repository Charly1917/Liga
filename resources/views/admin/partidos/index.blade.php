@extends('admin.layouts.app')

@section('content')

    <div class="container mx-auto py-8">


        {{-- BOTÓN REGRESAR DASHBOARD --}}
        <a href="{{ route('admin') }}"
            class="inline-block mb-6 bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow">

            ← Regresar

        </a>



        <h1 class="text-4xl font-extrabold text-blue-700 mb-8">
            Gestión de Partidos
        </h1>



        {{-- SELECTOR DE JORNADA --}}
        <div class="bg-white shadow rounded-xl p-6 mb-8">

            <form method="GET">

                <label class="font-bold text-lg">

                    Seleccione la jornada

                </label>


                <select name="jornada" onchange="this.form.submit()" class="border rounded-lg p-3 ml-3 shadow-sm">


                    <option value="">
                        Seleccione...
                    </option>


                    @foreach ($jornadas as $jornada)
                        <option value="{{ $jornada->id }}" {{ $jornadaSeleccionada == $jornada->id ? 'selected' : '' }}>

                            Jornada {{ $jornada->numero }}

                        </option>
                    @endforeach


                </select>


            </form>

        </div>




        {{-- MENSAJE --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-lg mb-6">

                {{ session('success') }}

            </div>
        @endif






        {{-- PARTIDOS DE LA JORNADA --}}
        @if ($jornadaSeleccionada)
            <form action="{{ route('admin.partidos.guardarJornada') }}" method="POST">

                @csrf



                @foreach ($partidos as $partido)
                    <div class="bg-white shadow-lg rounded-xl p-6 mb-5">


                        <div class="flex flex-col md:flex-row justify-between items-center gap-6">



                            {{-- EQUIPOS --}}
                            <div class="text-xl font-bold text-gray-800">


                                {{ $partido->equipoLocal->nombre }}


                                <span class="mx-3 text-gray-500">

                                    VS

                                </span>


                                {{ $partido->equipoVisitante->nombre }}


                            </div>





                            {{-- MARCADOR --}}
                            <div class="flex items-center gap-4">


                                <input type="number" min="0"
                                    class="w-20 border rounded-lg text-center p-3 text-xl font-bold"
                                    name="resultados[{{ $partido->id }}][goles_local]" value="{{ $partido->goles_local }}">



                                <span class="text-2xl font-black">

                                    -

                                </span>



                                <input type="number" min="0"
                                    class="w-20 border rounded-lg text-center p-3 text-xl font-bold"
                                    name="resultados[{{ $partido->id }}][goles_visitante]"
                                    value="{{ $partido->goles_visitante }}">



                            </div>





                            {{-- FECHA --}}
                            <div class="text-gray-600 text-center">


                                <p class="font-semibold">

                                    Fecha

                                </p>


                                {{ $partido->fecha->format('d/m/Y') }}


                                <br>


                                {{ $partido->fecha->format('H:i') }}


                            </div>



                        </div>


                    </div>
                @endforeach






                {{-- GUARDAR TODOS --}}
                <div class="text-right mt-8">


                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-bold shadow">


                        Guardar resultados de la jornada


                    </button>


                </div>



            </form>
        @else
            <div class="bg-gray-100 p-6 rounded-lg text-center text-gray-600">

                Selecciona una jornada para ver sus partidos.


            </div>
        @endif



    </div>


@endsection
