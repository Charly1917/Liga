<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;

class ClasificacionController extends Controller
{
    public function index($temporada_id = null)
    {

        $temporada = null;

        if ($temporada_id) {

            $temporada = \App\Models\Temporada::findOrFail($temporada_id);
        }
        $equipos = Equipo::all();

        foreach ($equipos as $equipo) {

            $pj = 0;
            $gf = 0;
            $gc = 0;
            $puntos = 0;


            $locales = Partido::where('equipo_local_id', $equipo->id)
                ->when($temporada_id, function ($query) use ($temporada_id) {

                    $query->where('temporada_id', $temporada_id);
                })
                ->whereNotNull('goles_local')
                ->get();

            foreach ($locales as $partido) {

                $pj++;

                $gf += $partido->goles_local;
                $gc += $partido->goles_visitante;


                if ($partido->goles_local > $partido->goles_visitante) {
                    $puntos += 3;
                } elseif ($partido->goles_local == $partido->goles_visitante) {
                    $puntos += 1;
                }
            }



            $visitantes = Partido::where('equipo_visitante_id', $equipo->id)
                ->when($temporada_id, function ($query) use ($temporada_id) {

                    $query->where('temporada_id', $temporada_id);
                })
                ->whereNotNull('goles_visitante')
                ->get();


            foreach ($visitantes as $partido) {

                $pj++;

                $gf += $partido->goles_visitante;
                $gc += $partido->goles_local;


                if ($partido->goles_visitante > $partido->goles_local) {
                    $puntos += 3;
                } elseif ($partido->goles_visitante == $partido->goles_local) {
                    $puntos += 1;
                }
            }


            // Agregamos datos temporales al equipo
            $equipo->pj = $pj;
            $equipo->gf = $gf;
            $equipo->gc = $gc;
            $equipo->puntos = $puntos;
        }


        // Ordenar clasificación
        $equipos = $equipos->sortByDesc(function ($equipo) {

            return [
                $equipo->puntos,
                $equipo->gf
            ];
        });


        return view('clasificacion.index', compact(
            'equipos',
            'temporada'
        ));
    }
}
