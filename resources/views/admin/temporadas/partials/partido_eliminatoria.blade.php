<div class="bg-white rounded-xl shadow-lg border p-4">


    <div class="text-center mb-3">

        <span class="text-xs font-bold text-gray-500 uppercase">
            {{ $numeroCruce ?? $partido->fase }}
        </span>

    </div>

    @if($partido->fecha)
        <p class="mb-4 text-center text-xs font-semibold text-slate-500">
            {{ $partido->fecha->format('d/m/Y · H:i') }}
            @if($partido->lugar) · {{ $partido->lugar }} @endif
        </p>
    @endif



    <form method="POST" action="{{ route('admin.eliminatorias.resultado', $partido->id) }}">

        @csrf



        <div class="space-y-3">


            <div class="flex justify-between items-center">

                <span class="flex items-center gap-2 font-bold text-gray-800">
                    @if($partido->equipoLocal)
                        <x-equipo-escudo :equipo="$partido->equipoLocal" tamano="h-8 w-8" />
                    @endif
                    {{ $partido->equipoLocal->nombre ?? 'Pendiente' }}
                </span>


                <input type="number" name="goles_local" min="0" required value="{{ old('goles_local', $partido->goles_local) }}"
                    class="w-14 text-center border rounded-lg font-bold">

            </div>





            <div class="flex justify-between items-center">


                <span class="flex items-center gap-2 font-bold text-gray-800">
                    @if($partido->equipoVisitante)
                        <x-equipo-escudo :equipo="$partido->equipoVisitante" tamano="h-8 w-8" />
                    @endif
                    {{ $partido->equipoVisitante->nombre ?? 'Pendiente' }}
                </span>


                <input type="number" name="goles_visitante" min="0" required value="{{ old('goles_visitante', $partido->goles_visitante) }}"
                    class="w-14 text-center border rounded-lg font-bold">


            </div>


        </div>





        <div class="mt-4 border-t pt-3">
            <h3 class="text-center text-slate-600 font-bold text-sm">Penales (solo si hay empate)</h3>
            <div class="flex justify-center items-center gap-3 mt-2">
                <input type="number" name="penales_local" min="0"
                    value="{{ old('penales_local', $partido->penales_local) }}"
                    aria-label="Penales del equipo local"
                    class="w-16 text-center border rounded">
                <span>-</span>
                <input type="number" name="penales_visitante" min="0"
                    value="{{ old('penales_visitante', $partido->penales_visitante) }}"
                    aria-label="Penales del equipo visitante"
                    class="w-16 text-center border rounded">
            </div>
        </div>





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
