<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partido;
use App\Models\Temporada;
use App\Services\JornadaOrdenService;

class JornadaController extends Controller
{
    public function index(JornadaOrdenService $jornadaOrdenService)
    {
        $temporada = Temporada::where('activa', true)->latest('id')->first();
        $jornadas = $temporada
            ? $temporada->jornadas()->withCount('partidos')->orderBy('numero')->get()
            : collect();
        $jornadaHabilitada = $temporada
            ? $jornadaOrdenService->siguiente($temporada)
            : null;
        $jornadaSeleccionada = $jornadas->firstWhere('id', (int) request('jornada'))
            ?? $jornadaHabilitada
            ?? $jornadas->last();
        $partidos = $jornadaSeleccionada
            ? Partido::with(['equipoLocal', 'equipoVisitante'])
                ->where('jornada_id', $jornadaSeleccionada->id)
                ->orderBy('fecha')
                ->orderBy('id')
                ->get()
            : collect();
        $puedeCapturar = $jornadaSeleccionada
            && $jornadaHabilitada
            && $jornadaSeleccionada->is($jornadaHabilitada);

        return view(
            'admin.jornadas.index',
            compact(
                'jornadas',
                'partidos',
                'jornadaSeleccionada',
                'temporada',
                'jornadaHabilitada',
                'puedeCapturar'
            )
        );
    }
}