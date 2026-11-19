<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquiposSeeder extends Seeder
{
    public function run(): void
    {
        $equipos = [
            ['nombre' => 'Real Madrid', 'logo' => 'real_madrid.png', 'color' => '#febd00'],
            ['nombre' => 'FC Barcelona', 'logo' => 'barcelona.png', 'color' => '#a50044'],
            ['nombre' => 'Atlético de Madrid', 'logo' => 'atletico.png', 'color' => '#c8102e'],
            ['nombre' => 'Athletic Club', 'logo' => 'athletic.png', 'color' => '#e30613'],
            ['nombre' => 'Real Sociedad', 'logo' => 'real_sociedad.png', 'color' => '#0054a6'],
            ['nombre' => 'Villarreal', 'logo' => 'villarreal.png', 'color' => '#ffea00'],
            ['nombre' => 'Real Betis', 'logo' => 'betis.png', 'color' => '#00954c'],
            ['nombre' => 'Valencia CF', 'logo' => 'valencia.png', 'color' => '#f58220'],
            ['nombre' => 'Sevilla FC', 'logo' => 'sevilla.png', 'color' => '#ff0000'],
            ['nombre' => 'Osasuna', 'logo' => 'osasuna.png', 'color' => '#d00000'],
            ['nombre' => 'Rayo Vallecano', 'logo' => 'rayo.png', 'color' => '#e60026'],
            ['nombre' => 'Getafe', 'logo' => 'getafe.png', 'color' => '#003399'],
            ['nombre' => 'Celta de Vigo', 'logo' => 'celta.png', 'color' => '#00a3e0'],
            ['nombre' => 'UD Las Palmas', 'logo' => 'las_palmas.png', 'color' => '#ffdd00'],
            ['nombre' => 'Mallorca', 'logo' => 'mallorca.png', 'color' => '#d00000'],
            ['nombre' => 'Alavés', 'logo' => 'alaves.png', 'color' => '#0033a0'],
            ['nombre' => 'Girona', 'logo' => 'girona.png', 'color' => '#d6001c'],
            ['nombre' => 'Granada', 'logo' => 'granada.png', 'color' => '#ff0000'],
            ['nombre' => 'Cádiz CF', 'logo' => 'cadiz.png', 'color' => '#f7d300'],
            ['nombre' => 'Espanyol', 'logo' => 'espanyol.png', 'color' => '#0070bb'],
        ];

        DB::table('equipos')->insert($equipos);
    }
}
