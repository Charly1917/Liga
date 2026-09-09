@extends('admin.layouts.app')

@section('content')
    <a href="{{ route('admin.temporadas.show', $temporada->id) }}"
        class="inline-block mb-6 bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow">

        ← Regresar temporada

    </a>



    <div class="mb-6">

        <h1 class="text-3xl md:text-4xl font-extrabold text-green-700">

            Clasificación

        </h1>


        <p class="text-gray-600 mt-2 text-lg">

            {{ $temporada->nombre }} {{ $temporada->tipo }}

        </p>


    </div>





    <div class="bg-white shadow-lg rounded-lg overflow-hidden">


        <div class="overflow-x-auto">


            <table class="min-w-full">


                <thead class="bg-green-600 text-white">


                    <tr>


                        <th class="p-3 text-center">
                            Pos
                        </th>


                        <th class="p-3 text-left">
                            Equipo
                        </th>


                        <th class="p-3 text-center">
                            PJ
                        </th>


                        <th class="p-3 text-center">
                            GF
                        </th>


                        <th class="p-3 text-center">
                            GC
                        </th>


                        <th class="p-3 text-center">
                            Puntos
                        </th>


                    </tr>


                </thead>





                <tbody>



                    @php

                        $posicion = 1;

                    @endphp




                    @forelse($equipos as $equipo)
                        <tr class="border-b hover:bg-gray-50">





                            <td class="p-3 text-center font-bold">

                                {{ $posicion }}

                            </td>






                            <td class="p-3 font-bold">


                                <div class="flex items-center gap-3">


                                    @if ($equipo->logo)
                                        <img src="{{ asset('img/equipos/' . $equipo->logo) }}" class="w-6 h-6" alt="">
                                    @endif



                                    <span>

                                        {{ $equipo->nombre }}

                                    </span>



                                </div>


                            </td>






                            <td class="p-3 text-center">

                                {{ $equipo->pj }}

                            </td>






                            <td class="p-3 text-center text-green-600 font-bold">

                                {{ $equipo->gf }}

                            </td>






                            <td class="p-3 text-center text-red-600 font-bold">

                                {{ $equipo->gc }}

                            </td>






                            <td class="p-3 text-center">


                                <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full font-bold">

                                    {{ $equipo->puntos }}

                                </span>


                            </td>





                        </tr>



                        @php

                            $posicion++;

                        @endphp



                    @empty



                        <tr>


                            <td colspan="6" class="p-6 text-center text-gray-500">

                                No hay datos de clasificación para esta temporada.


                            </td>


                        </tr>
                    @endforelse





                </tbody>



            </table>



        </div>



    </div>
@endsection
