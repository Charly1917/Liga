@extends('admin.layouts.app')


@section('content')
    <a href="{{ route('admin.temporadas.show', $temporada->id) }}"
        class="inline-flex items-center bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg mb-6 shadow">

        ← Regresar temporada

    </a>


    <div class="mb-8">

        <h1 class="text-4xl font-extrabold text-green-700">

            {{ $temporada->nombre }} {{ $temporada->tipo }}

        </h1>

        <p class="text-gray-600 mt-2 text-lg">

            Liguilla del campeonato

        </p>

    </div>



    @if (session('success'))
        <div class="bg-green-100 text-green-700 border border-green-300 p-4 rounded-lg mb-5">

            {{ session('success') }}

        </div>
    @endif



    @if (session('error'))
        <div class="bg-red-100 text-red-700 border border-red-300 p-4 rounded-lg mb-5">

            {{ session('error') }}

        </div>
    @endif




    @php

        $cuartos = $temporada->eliminatorias->where('fase', 'Cuartos');

        $semis = $temporada->eliminatorias->where('fase', 'Semifinal');

        $final = $temporada->eliminatorias->where('fase', 'Final');

    @endphp





    <div class="overflow-x-auto pb-10">


        <div class="flex gap-10 min-w-[1000px]">





            {{-- CUARTOS --}}

            <div class="w-80">

                <h2 class="text-center text-xl font-bold mb-6 text-gray-700">

                    CUARTOS DE FINAL

                </h2>


                <div class="space-y-8">


                    @foreach ($cuartos as $partido)
                        @include('admin.temporadas.partials.partido_eliminatoria', [
                            'partido' => $partido,
                        ])
                    @endforeach


                </div>

            </div>






            {{-- SEMIFINAL --}}

            <div class="w-80 pt-16">

                <h2 class="text-center text-xl font-bold mb-6 text-gray-700">

                    SEMIFINAL

                </h2>


                <div class="space-y-32">


                    @foreach ($semis as $partido)
                        @include('admin.temporadas.partials.partido_eliminatoria', [
                            'partido' => $partido,
                        ])
                    @endforeach


                </div>

            </div>








            {{-- FINAL --}}

            <div class="w-80 pt-40">

                <h2 class="text-center text-xl font-bold mb-6 text-gray-700">

                    FINAL

                </h2>


                @foreach ($final as $partido)
                    @include('admin.temporadas.partials.partido_eliminatoria', [
                        'partido' => $partido,
                    ])
                @endforeach


            </div>





            {{-- CAMPEON --}}

            <div class="w-80 pt-52">


                <h2 class="text-center text-xl font-bold mb-6 text-yellow-600">

                    CAMPEÓN

                </h2>



                @if ($temporada->campeon)
                    <div class="bg-yellow-100 border-2 border-yellow-400 rounded-xl p-8 text-center shadow-lg">


                        <div class="text-5xl mb-4">

                            🏆

                        </div>


                        <p class="text-sm text-gray-600">

                            Campeón {{ $temporada->nombre }}

                        </p>


                        <p class="text-3xl font-black text-yellow-700 mt-3">

                            {{ $temporada->campeon->nombre }}

                        </p>


                    </div>
                @else
                    <div class="bg-gray-100 border rounded-xl p-8 text-center text-gray-400">

                        <div class="text-5xl mb-3">

                            🏆

                        </div>


                        <p>

                            Pendiente

                        </p>


                    </div>
                @endif
 @endsection
