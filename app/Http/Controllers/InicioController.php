<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\Temporada;
use App\Services\CampeonService;
use App\Services\ClasificacionService;

class InicioController extends Controller
{
    public function index(
        ClasificacionService $clasificacionService,
        CampeonService $campeonService
    )
    {
        $temporada = Temporada::with('campeon')
            ->where('activa', true)
            ->latest('id')
            ->first()
            ?? Temporada::with('campeon')->latest('id')->first();
        $campeonAnteriorTemporada = $temporada
            ? Temporada::with('campeon')
                ->where('id', '<', $temporada->id)
                ->whereNotNull('campeon_id')
                ->latest('id')
                ->first()
            : null;
        $campeonAnterior = $campeonAnteriorTemporada
            ? $campeonAnteriorTemporada->campeon
            : null;
        $mostrarLiguillaPublica = $temporada
            ? $temporada->eliminatorias()->exists()
            : false;
        $felicitacionCampeon = $temporada && $temporada->campeon_id
            ? $campeonService->mensajeFelicitacion($temporada)
            : null;

        $equipos = $temporada
            ? $clasificacionService->obtener($temporada)
            : Equipo::all();

        $resultados = Partido::with(['equipoLocal', 'equipoVisitante'])
            ->when($temporada, fn ($query) => $query->where('temporada_id', $temporada->id))
            ->whereNotNull('goles_local')
            ->whereNotNull('goles_visitante')
            ->latest()
            ->take(5)
            ->get();

        $jornadas = Partido::with(['equipoLocal', 'equipoVisitante'])
            ->when($temporada, fn ($query) => $query->where('temporada_id', $temporada->id))
            ->whereNull('goles_local')
            ->whereNull('goles_visitante')
            ->where('fecha', '>', now())
            ->orderBy('fecha')
            ->take(5)
            ->get();

        return view('inicio.index', compact(
            'equipos',
            'resultados',
            'jornadas',
            'temporada',
            'mostrarLiguillaPublica',
            'felicitacionCampeon',
            'campeonAnterior',
            'campeonAnteriorTemporada'
        ));
    }
}