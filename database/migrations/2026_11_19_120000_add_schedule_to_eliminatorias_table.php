<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eliminatorias', function (Blueprint $table) {
            $table->dateTime('fecha')->nullable();
            $table->string('lugar')->nullable();
        });

        $temporadas = DB::table('temporadas')->get(['id', 'fecha_inicio']);
        foreach ($temporadas as $temporada) {
            $ultimoPartido = DB::table('partidos')
                ->where('temporada_id', $temporada->id)
                ->max('fecha');
            $fechaBase = $ultimoPartido
                ? Carbon::parse($ultimoPartido)->next(Carbon::SATURDAY)->startOfDay()
                : Carbon::parse($temporada->fecha_inicio ?? now())->next(Carbon::SATURDAY)->startOfDay();

            $fases = [
                'Cuartos' => [
                    'semanas' => 0,
                    'horarios' => ['08:00', '10:00', '12:00', '14:00'],
                    'campos' => ['Cancha Central', 'Estadio Municipal', 'Campo Norte', 'Campo Sur'],
                ],
                'Semifinal' => [
                    'semanas' => 1,
                    'horarios' => ['10:00', '14:00'],
                    'campos' => ['Cancha Central', 'Estadio Municipal'],
                ],
                'Final' => [
                    'semanas' => 2,
                    'horarios' => ['14:00'],
                    'campos' => ['Cancha Central'],
                ],
            ];

            foreach ($fases as $fase => $programacion) {
                $partidos = DB::table('eliminatorias')
                    ->where('temporada_id', $temporada->id)
                    ->where('fase', $fase)
                    ->orderBy('id')
                    ->get(['id']);

                foreach ($partidos as $indice => $partido) {
                    $fecha = $fechaBase->copy()
                        ->addWeeks($programacion['semanas'])
                        ->setTimeFromTimeString($programacion['horarios'][$indice % count($programacion['horarios'])]);

                    DB::table('eliminatorias')
                        ->where('id', $partido->id)
                        ->update([
                            'fecha' => $fecha,
                            'lugar' => $programacion['campos'][$indice % count($programacion['campos'])],
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('eliminatorias', function (Blueprint $table) {
            $table->dropColumn(['fecha', 'lugar']);
        });
    }
};
