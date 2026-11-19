<?php

namespace App\Services;

use App\Models\Jornada;
use App\Models\Temporada;

class JornadaOrdenService
{
    public function siguiente(Temporada $temporada): ?Jornada
    {
        return $temporada->jornadas()
            ->with('partidos')
            ->orderBy('numero')
            ->get()
            ->first(function (Jornada $jornada) {
                return $jornada->partidos->isNotEmpty()
                    && $jornada->partidos->contains(function ($partido) {
                        return $partido->goles_local === null || $partido->goles_visitante === null;
                    });
            });
    }
}
