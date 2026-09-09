<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jornada;
use App\Models\Partido;
use App\Models\Temporada;

class JornadaController extends Controller
{
    public function index()
    {
        $temporada = Temporada::where('activa', true)->first();

        if (!$temporada) {

            $jornadas = collect();

        } else {

            $jornadas = Jornada::where('temporada_id', $temporada->id)
                ->orderBy('numero')
                ->get();

        }

        $jornadaSeleccionada = request('jornada');

        $partidos = collect();

        if ($jornadaSeleccionada) {

            $partidos = Partido::with([
                'equipoLocal',
                'equipoVisitante'
            ])
            ->where('jornada_id', $jornadaSeleccionada)
            ->get();

        }

        return view(
            'admin.jornadas.index',
            compact(
                'jornadas',
                'partidos',
                'jornadaSeleccionada',
                'temporada'
            )
        );
    }
}