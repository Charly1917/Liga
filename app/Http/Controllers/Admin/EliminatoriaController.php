<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eliminatoria;
use Carbon\Carbon;
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

                return back()->withInput()->with(
                    'error',
                    'El partido terminó empatado. Debes capturar los penales.'
                );
            }

            if ($request->penales_local == $request->penales_visitante) {

                return back()->withInput()->with(
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

        if ($eliminatoria->fase === 'Cuartos') {
            $this->crearSemifinales($eliminatoria->temporada_id, 'Cuartos', 'Semifinal');
        } elseif ($eliminatoria->fase === 'Semifinal') {
            $this->crearFinal($eliminatoria->temporada_id, 'Semifinal', 'Final');
        } elseif ($eliminatoria->fase === 'Consolación Cuartos') {
            $this->crearSemifinales(
                $eliminatoria->temporada_id,
                'Consolación Cuartos',
                'Consolación Semifinal'
            );
        } elseif ($eliminatoria->fase === 'Consolación Semifinal') {
            $this->crearFinal(
                $eliminatoria->temporada_id,
                'Consolación Semifinal',
                'Consolación Final'
            );
        }

        if ($eliminatoria->fase === 'Final') {
            $eliminatoria->temporada->update([
                'campeon_id' => $ganador,
                'fecha_fin' => now(),
            ]);
        }

        return back()->with(
            'success',
            'Resultado guardado correctamente.'
        );
    }

    private function crearSemifinales($temporadaId, string $faseCuartos, string $faseSemifinal)
    {

        $cuartos = Eliminatoria::where('temporada_id', $temporadaId)
            ->where('fase', $faseCuartos)
            ->whereNotNull('ganador_id')
            ->orderBy('id')
            ->get();


        // Todavía no terminan los 4 partidos
        if ($cuartos->count() < 4) {
            return;
        }


        // Evitar crear semifinales dos veces
        $existe = Eliminatoria::where('temporada_id', $temporadaId)
            ->where('fase', $faseSemifinal)
            ->exists();


        if ($existe) {
            return;
        }


        $ganadores = $cuartos->pluck('ganador_id')->values();
        $fechaSemifinales = $this->siguienteSemana($cuartos->max(fn (Eliminatoria $partido) => $partido->fecha));
        $horarios = $faseCuartos === 'Cuartos' ? ['10:00', '14:00'] : ['16:00', '18:00'];
        $campos = ['Cancha Central', 'Estadio Municipal'];

        foreach ([[0, 3], [1, 2]] as $indice => [$local, $visitante]) {
            Eliminatoria::create([
                'temporada_id' => $temporadaId,
                'fase' => $faseSemifinal,
                'equipo_local_id' => $ganadores[$local],
                'equipo_visitante_id' => $ganadores[$visitante],
                'fecha' => $fechaSemifinales->copy()
                    ->setTimeFromTimeString($horarios[$indice]),
                'lugar' => $campos[$indice],
            ]);
        }
    }

    private function crearFinal($temporadaId, string $faseSemifinal, string $faseFinal)
    {

        $semifinales = Eliminatoria::where('temporada_id', $temporadaId)
            ->where('fase', $faseSemifinal)
            ->whereNotNull('ganador_id')
            ->orderBy('id')
            ->get();


        // Esperar a que terminen las 2 semifinales
        if ($semifinales->count() < 2) {
            return;
        }


        // Evitar duplicar final
        $existe = Eliminatoria::where('temporada_id', $temporadaId)
            ->where('fase', $faseFinal)
            ->exists();


        if ($existe) {
            return;
        }


        $ganadores = $semifinales
            ->pluck('ganador_id')
            ->values();

        $fechaFinal = $this->siguienteSemana($semifinales->max(fn (Eliminatoria $partido) => $partido->fecha));

        Eliminatoria::create([
            'temporada_id' => $temporadaId,
            'fase' => $faseFinal,
            'equipo_local_id' => $ganadores[0],
            'equipo_visitante_id' => $ganadores[1],
            'fecha' => $fechaFinal->copy()->setTimeFromTimeString(
                $faseFinal === 'Final' ? '14:00' : '18:00'
            ),
            'lugar' => 'Cancha Central',
        ]);
    }

    private function siguienteSemana($fecha): Carbon
    {
        return $fecha
            ? Carbon::parse($fecha)->startOfDay()->addWeek()
            : Carbon::now()->next(Carbon::SUNDAY)->startOfDay();
    }
}
