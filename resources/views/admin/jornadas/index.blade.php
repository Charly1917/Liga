@extends('admin.layouts.app')

@section('content')

    <div class="container mx-auto py-8">


        <a href="{{ route('admin') }}" class="inline-block mb-6 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">

            ← Regresar

        </a>

        <h1 class="text-4xl font-bold mb-8">
            Jornadas
        </h1>


        @if ($temporada)
            <div class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-lg mb-8">

                <strong>
                    Temporada activa:
                </strong>

                {{ $temporada->nombre }}

            </div>
        @endif

        {{-- Selector de jornadas --}}

        <div class="bg-white shadow rounded-lg p-6 mb-8">


            <label class="block font-bold text-lg mb-3">
                Seleccionar Jornada
            </label>


            <form method="GET" action="{{ route('admin.jornadas.index') }}">


                <select name="jornada" onchange="this.form.submit()" class="w-full md:w-1/3 border rounded-lg p-3">


                    <option value="">
                        Seleccione una jornada
                    </option>



                    @foreach ($jornadas as $jornada)
                        <option value="{{ $jornada->id }}" {{ $jornadaSeleccionada == $jornada->id ? 'selected' : '' }}>

                            Jornada {{ $jornada->numero }}

                        </option>
                    @endforeach


                </select>


            </form>


        </div>






        {{-- Mostrar partidos solamente de la jornada elegida --}}

        @if ($jornadaSeleccionada)
            <div class="bg-white shadow rounded-xl overflow-hidden">


                <div class="bg-blue-700 text-white p-5">


                    <h2 class="text-2xl font-bold">

                        Jornada
                        {{ $jornadas->where('id', $jornadaSeleccionada)->first()->numero }}

                    </h2>


                </div>





                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">



                    @foreach ($partidos as $partido)
                        <div class="bg-gray-50 border rounded-xl p-5 shadow-sm">



                            <div class="flex justify-between items-center font-bold text-lg">


                                <span>
                                    {{ $partido->equipoLocal->nombre }}
                                </span>


                                <span class="text-gray-500">
                                    VS
                                </span>


                                <span>
                                    {{ $partido->equipoVisitante->nombre }}
                                </span>


                            </div>






                            <div class="text-center text-4xl font-black my-5">


                                @if ($partido->goles_local !== null)
                                    {{ $partido->goles_local }}

                                    -

                                    {{ $partido->goles_visitante }}
                                @else
                                    <span class="text-gray-400">
                                        Pendiente
                                    </span>
                                @endif


                            </div>





                            <div class="text-center text-gray-600">


                                <p>
                                    📅 {{ $partido->fecha->format('d/m/Y') }}
                                </p>


                                <p>
                                    ⏰ {{ $partido->fecha->format('H:i') }}
                                </p>


                                <p>
                                    🏟 {{ $partido->lugar }}
                                </p>



                            </div>




                        </div>
                    @endforeach



                </div>


            </div>
        @else
            <div class="bg-white shadow rounded-lg p-8 text-center text-gray-500">


                Seleccione una jornada para ver los partidos


            </div>
        @endif




    </div>


@endsection
