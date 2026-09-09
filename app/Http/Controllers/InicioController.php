<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;

class InicioController extends Controller
{
    public function index()
    {
        $equipos = Equipo::all();

        foreach ($equipos as $equipo) {

            $pj = 0;
            $gf = 0;
            $gc = 0;
            $puntos = 0;


            // Partidos como local
            $locales = Partido::where('equipo_local_id', $equipo->id)
                ->whereNotNull('goles_local')
                ->get();


            foreach ($locales as $partido) {

                $pj++;

                $gf += $partido->goles_local;
                $gc += $partido->goles_visitante;


                if ($partido->goles_local > $partido->goles_visitante) {
                    $puntos += 3;
                }
                elseif ($partido->goles_local == $partido->goles_visitante) {
                    $puntos += 1;
                }
            }


            // Partidos como visitante
            $visitantes = Partido::where('equipo_visitante_id', $equipo->id)
                ->whereNotNull('goles_visitante')
                ->get();


            foreach ($visitantes as $partido) {

                $pj++;

                $gf += $partido->goles_visitante;
                $gc += $partido->goles_local;


                if ($partido->goles_visitante > $partido->goles_local) {
                    $puntos += 3;
                }
                elseif ($partido->goles_visitante == $partido->goles_local) {
                    $puntos += 1;
                }
            }


            $equipo->pj = $pj;
            $equipo->gf = $gf;
            $equipo->gc = $gc;
            $equipo->puntos = $puntos;
        }


        // Ordenar tabla
        $equipos = $equipos->sortByDesc(function ($equipo) {
            return [
                $equipo->puntos,
                $equipo->gf
            ];
        });


        $resultados = Partido::with([
                'equipoLocal',
                'equipoVisitante'
            ])
            ->latest()
            ->take(5)
            ->get();


        $jornadas = Partido::with([
                'equipoLocal',
                'equipoVisitante'
            ])
            ->where('fecha', '>', now())
            ->orderBy('fecha')
            ->take(5)
            ->get();


        return view('inicio.index', compact(
            'equipos',
            'resultados',
            'jornadas'
        ));
    }
}