<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eliminatoria;
use Illuminate\Http\Request;

class EliminatoriaController extends Controller
{

    public function guardarResultado(Request $request, $id)
    {
        $eliminatoria = Eliminatoria::findOrFail($id);

        $request->validate([
            'goles_local' => 'required|integer|min:0',
            'goles_visitante' => 'required|integer|min:0',
            'penales_local' => 'nullable|integer|min:0',
            'penales_visitante' => 'nullable|integer|min:0',
        ]);

        $ganador = null;

        // Ganador en tiempo normal
        if ($request->goles_local > $request->goles_visitante) {

            $ganador = $eliminatoria->equipo_local_id;
        } elseif ($request->goles_visitante > $request->goles_local) {

            $ganador = $eliminatoria->equipo_visitante_id;
        } else {

            // Hubo empate, revisar penales
            if (
                $request->penales_local === null ||
                $request->penales_visitante === null
            ) {

                return back()->with(
                    'error',
                    'El partido terminó empatado. Debes capturar los penales.'
                );
            }

            if ($request->penales_local == $request->penales_visitante) {

                return back()->with(
                    'error',
                    'Los penales no pueden terminar empatados.'
                );
            }

            if ($request->penales_local > $request->penales_visitante) {

                $ganador = $eliminatoria->equipo_local_id;
            } else {

                $ganador = $eliminatoria->equipo_visitante_id;
            }
        }

        $eliminatoria->update([

            'goles_local' => $request->goles_local,
            'goles_visitante' => $request->goles_visitante,
            'penales_local' => $request->penales_local,
            'penales_visitante' => $request->penales_visitante,
            'ganador_id' => $ganador,
            'estado' => 'Finalizado',

        ]);

        $this->crearSemifinales($eliminatoria->temporada_id);

        $this->crearFinal($eliminatoria->temporada_id);

        if ($eliminatoria->fase == 'Final') {

            $eliminatoria->temporada->update([
                'campeon_id' => $ganador
            ]);
        }

        return back()->with(
            'success',
            'Resultado guardado correctamente.'
        );
    }

    private function crearSemifinales($temporadaId)
    {

        $cuartos = Eliminatoria::where('temporada_id', $temporadaId)
            ->where('fase', 'Cuartos')
            ->whereNotNull('ganador_id')
            ->get();


        // Todavía no terminan los 4 partidos
        if ($cuartos->count() < 4) {
            return;
        }


        // Evitar crear semifinales dos veces
        $existe = Eliminatoria::where('temporada_id', $temporadaId)
            ->where('fase', 'Semifinal')
            ->exists();


        if ($existe) {
            return;
        }


        $ganadores = $cuartos->pluck('ganador_id')->values();


        Eliminatoria::create([

            'temporada_id' => $temporadaId,
            'fase' => 'Semifinal',
            'equipo_local_id' => $ganadores[0],
            'equipo_visitante_id' => $ganadores[3],

        ]);


        Eliminatoria::create([

            'temporada_id' => $temporadaId,
            'fase' => 'Semifinal',
            'equipo_local_id' => $ganadores[1],
            'equipo_visitante_id' => $ganadores[2],

        ]);
    }

    private function crearFinal($temporadaId)
    {

        $semifinales = Eliminatoria::where('temporada_id', $temporadaId)
            ->where('fase', 'Semifinal')
            ->whereNotNull('ganador_id')
            ->get();


        // Esperar a que terminen las 2 semifinales
        if ($semifinales->count() < 2) {
            return;
        }


        // Evitar duplicar final
        $existe = Eliminatoria::where('temporada_id', $temporadaId)
            ->where('fase', 'Final')
            ->exists();


        if ($existe) {
            return;
        }


        $ganadores = $semifinales
            ->pluck('ganador_id')
            ->values();



        Eliminatoria::create([

            'temporada_id' => $temporadaId,

            'fase' => 'Final',

            'equipo_local_id' => $ganadores[0],

            'equipo_visitante_id' => $ganadores[1],

        ]);
    }
}
