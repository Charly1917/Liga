<div class="bg-white rounded-xl shadow-lg border p-4">


    <div class="text-center mb-3">

        <span class="text-xs font-bold text-gray-500 uppercase">
            {{ $partido->fase }}
        </span>

    </div>



    <form method="POST" action="{{ route('admin.eliminatorias.resultado', $partido->id) }}">

        @csrf



        <div class="space-y-3">


            <div class="flex justify-between items-center">

                <span class="font-bold text-gray-800">

                    {{ $partido->equipoLocal->nombre ?? 'Pendiente' }}

                </span>


                <input type="number" name="goles_local" min="0" value="{{ $partido->goles_local }}"
                    class="w-14 text-center border rounded-lg font-bold">

            </div>





            <div class="flex justify-between items-center">


                <span class="font-bold text-gray-800">

                    {{ $partido->equipoVisitante->nombre ?? 'Pendiente' }}

                </span>


                <input type="number" name="goles_visitante" min="0" value="{{ $partido->goles_visitante }}"
                    class="w-14 text-center border rounded-lg font-bold">


            </div>


        </div>





        @if (
            $partido->goles_local !== null &&
                $partido->goles_visitante !== null &&
                $partido->goles_local == $partido->goles_visitante)
            <div class="mt-4 border-t pt-3">


                <h3 class="text-center text-red-600 font-bold text-sm">

                    ⚽ Penales

                </h3>


                <div class="flex justify-center gap-3 mt-2">


                    <input type="number" name="penales_local" min="0" value="{{ $partido->penales_local }}"
                        class="w-14 text-center border rounded">


                    <span>-</span>


                    <input type="number" name="penales_visitante" min="0"
                        value="{{ $partido->penales_visitante }}" class="w-14 text-center border rounded">


                </div>


            </div>
        @endif





        <button class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white rounded-lg py-2 font-bold">

            Guardar

        </button>



    </form>





    @if ($partido->ganador)
        <div class="mt-4 bg-green-50 border border-green-300 rounded-lg p-3 text-center">


            <p class="text-xs text-gray-600">

                Clasificado

            </p>


            <p class="font-extrabold text-green-700">

                🏆 {{ $partido->ganador->nombre }}

            </p>


        </div>
    @endif



</div>
