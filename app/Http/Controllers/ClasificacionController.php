<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Temporada;
use App\Services\ClasificacionService;

class ClasificacionController extends Controller
{
    public function index(ClasificacionService $clasificacionService)
    {
        $temporada = Temporada::where('activa', true)->latest('id')->first()
            ?? Temporada::latest('id')->first();

        $equipos = $temporada
            ? $clasificacionService->obtener($temporada)
            : Equipo::all();

        return view('clasificacion.index', compact(
            'equipos',
            'temporada'
        ));
    }
}
