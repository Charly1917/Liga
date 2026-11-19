<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Temporada;
use App\Services\CampeonService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LiguillaController extends Controller
{
    public function index(Request $request, CampeonService $campeonService)
    {
        $temporadas = Temporada::with([
            'eliminatorias.equipoLocal',
            'eliminatorias.equipoVisitante',
            'eliminatorias.ganador',
            'campeon',
        ])
            ->withCount('eliminatorias')
            ->where(function ($query) {
                $query->where('activa', true)
                    ->orWhereHas('eliminatorias');
            })
            ->latest('id')
            ->get();

        if ($request->filled('temporada')) {
            $temporada = $temporadas->firstWhere('id', (int) $request->query('temporada'));
            abort_if(! $temporada, 404);
        } else {
            $temporada = $temporadas->first(fn (Temporada $item) => $item->activa)
                ?? $temporadas->first();
        }

        $equipos = Equipo::all();
        $temporadaTieneLiguilla = $temporada && $temporada->eliminatorias->isNotEmpty();
        $programacionSemifinales = collect();
        $programacionFinal = null;

        if ($temporadaTieneLiguilla) {
            $primerCuarto = $temporada->eliminatorias
                ->where('fase', 'Cuartos')
                ->sortBy('id')
                ->first();

            if ($primerCuarto && $primerCuarto->fecha) {
                $fechaSemifinales = $primerCuarto->fecha->copy()->startOfDay()->addWeek();
                foreach ([
                    ['hora' => '10:00', 'lugar' => 'Cancha Central'],
                    ['hora' => '14:00', 'lugar' => 'Estadio Municipal'],
                ] as $programacion) {
                    $programacionSemifinales->push([
                        'fecha' => $fechaSemifinales->copy()
                            ->setTimeFromTimeString($programacion['hora']),
                        'lugar' => $programacion['lugar'],
                    ]);
                }

                $programacionFinal = [
                    'fecha' => $primerCuarto->fecha->copy()
                        ->startOfDay()
                        ->addWeeks(2)
                        ->setTimeFromTimeString('14:00'),
                    'lugar' => 'Cancha Central',
                ];
            }
        }

        $estadoTemporada = ! $temporada
            ? 'Sin temporadas'
            : ($temporada->campeon_id
                ? 'Temporada concluida'
                : ($temporadaTieneLiguilla ? 'Liguilla en curso' : 'Fase regular en curso'));

        $campeonAnteriorTemporada = $temporada
            ? $temporadas->first(fn (Temporada $item) => $item->id < $temporada->id && $item->campeon_id)
            : null;
        $campeonAnterior = $campeonAnteriorTemporada
            ? $campeonAnteriorTemporada->campeon
            : null;
        $temporadaActual = $temporadas->first();
        $mostrarCampeonDefensor = $temporada
            && $temporadaActual
            && $temporada->is($temporadaActual)
            && ! $temporada->campeon_id
            && $campeonAnterior;
        $felicitacionCampeon = $temporada && $temporada->campeon_id
            ? $campeonService->mensajeFelicitacion($temporada)
            : null;

        return view('liguilla.index', compact(
            'temporadas',
            'temporada',
            'equipos',
            'temporadaTieneLiguilla',
            'programacionSemifinales',
            'programacionFinal',
            'estadoTemporada',
            'campeonAnterior',
            'campeonAnteriorTemporada',
            'mostrarCampeonDefensor',
            'felicitacionCampeon'
        ));
    }
}
