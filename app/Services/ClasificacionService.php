<?php

namespace App\Services;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Temporada;
use Illuminate\Support\Collection;

class ClasificacionService
{
    public function obtener(Temporada $temporada): Collection
    {
        $partidos = Partido::where('temporada_id', $temporada->id)->get([
            'equipo_local_id',
            'equipo_visitante_id',
            'goles_local',
            'goles_visitante',
        ]);

        $ids = $partidos->flatMap(function ($partido) {
            return [$partido->equipo_local_id, $partido->equipo_visitante_id];
        })->unique()->values();

        $equipos = Equipo::whereIn('id', $ids)->get();
        $estadisticas = [];

        foreach ($equipos as $equipo) {
            $estadisticas[$equipo->id] = [
                'pj' => 0,
                'pg' => 0,
                'pe' => 0,
                'pp' => 0,
                'gf' => 0,
                'gc' => 0,
                'puntos' => 0,
            ];
        }

        foreach ($partidos as $partido) {
            if ($partido->goles_local === null || $partido->goles_visitante === null) {
                continue;
            }

            $localId = $partido->equipo_local_id;
            $visitanteId = $partido->equipo_visitante_id;
            $local = &$estadisticas[$localId];
            $visitante = &$estadisticas[$visitanteId];

            $local['pj']++;
            $visitante['pj']++;
            $local['gf'] += $partido->goles_local;
            $local['gc'] += $partido->goles_visitante;
            $visitante['gf'] += $partido->goles_visitante;
            $visitante['gc'] += $partido->goles_local;

            if ($partido->goles_local > $partido->goles_visitante) {
                $local['pg']++;
                $local['puntos'] += 3;
                $visitante['pp']++;
            } elseif ($partido->goles_local < $partido->goles_visitante) {
                $visitante['pg']++;
                $visitante['puntos'] += 3;
                $local['pp']++;
            } else {
                $local['pe']++;
                $visitante['pe']++;
                $local['puntos']++;
                $visitante['puntos']++;
            }

            unset($local, $visitante);
        }

        foreach ($equipos as $equipo) {
            foreach ($estadisticas[$equipo->id] as $campo => $valor) {
                $equipo->{$campo} = $valor;
            }
            $equipo->dg = $equipo->gf - $equipo->gc;
        }

        return $equipos->sort(function ($a, $b) {
            return ($b->puntos <=> $a->puntos)
                ?: ($b->dg <=> $a->dg)
                ?: ($b->gf <=> $a->gf)
                ?: ($b->pg <=> $a->pg)
                ?: strcmp($a->nombre, $b->nombre);
        })->values();
    }
}
