<?php

namespace App\Services;

use App\Models\Temporada;

class CampeonService
{
    public function mensajeFelicitacion(Temporada $temporada): string
    {
        $racha = $this->rachaConsecutiva($temporada);

        return match ($racha) {
            2 => '¡Felicidades al bicampeón!',
            3 => '¡Felicidades al tricampeón!',
            4 => '¡Felicidades al tetracampeón!',
            5 => '¡Felicidades al pentacampeón!',
            6 => '¡Felicidades al hexacampeón!',
            7 => '¡Felicidades al heptacampeón!',
            8 => '¡Felicidades al octacampeón!',
            9 => '¡Felicidades al nonacampeón!',
            10 => '¡Felicidades al decacampeón!',
            default => '¡La liga felicita al campeón!',
        };
    }

    private function rachaConsecutiva(Temporada $temporada): int
    {
        if (! $temporada->campeon_id) {
            return 0;
        }

        $racha = 1;
        $campeonId = (int) $temporada->campeon_id;
        $temporadasAnteriores = Temporada::where('id', '<', $temporada->id)
            ->orderByDesc('id')
            ->get(['campeon_id']);

        foreach ($temporadasAnteriores as $anterior) {
            if ((int) $anterior->campeon_id !== $campeonId) {
                break;
            }

            $racha++;
        }

        return $racha;
    }
}
