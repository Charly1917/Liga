<?php

use App\Http\Controllers\Admin\EliminatoriaController;
use App\Http\Controllers\Admin\JornadaController;
use App\Http\Controllers\Admin\TemporadaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\LiguillaController;
use App\Http\Controllers\PartidosController;
use App\Models\Equipo;
use App\Models\Temporada;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');

// Públicas
Route::get('/clasificacion', [ClasificacionController::class, 'index'])->name('clasificacion');
Route::get('/equipos', [EquiposController::class, 'index'])->name('equipos');
Route::get('/equipos/{equipo}', [EquiposController::class, 'show'])->name('equipos.show');
Route::get('/partidos/resultados', [PartidosController::class, 'resultados'])->name('resultados');
Route::get('/partidos/jornadas', [PartidosController::class, 'jornadas'])->name('jornadas');
Route::get('/liguilla', [LiguillaController::class, 'index'])->name('liguilla');

// Admin
Route::middleware('auth')->group(function () {

    Route::get('/admin', function () {
        $temporada = Temporada::where('activa', true)->latest('id')->first();
        $ultimaTemporada = Temporada::with('campeon')->latest('id')->first();
        $jornadas = $temporada
            ? $temporada->jornadas()->withCount('partidos')->orderBy('numero')->get()
            : collect();
        $partidosCount = $temporada ? $temporada->partidos()->count() : 0;
        $temporadaCompleta = $temporada
            && $partidosCount === 28
            && ! $temporada->partidos()
                ->where(function ($query) {
                    $query->whereNull('goles_local')
                        ->orWhereNull('goles_visitante');
                })
                ->exists();
        $idsEquipos = $temporada
            ? $temporada->partidos()->get(['equipo_local_id', 'equipo_visitante_id'])
                ->flatMap(fn ($partido) => [$partido->equipo_local_id, $partido->equipo_visitante_id])
                ->unique()
                ->values()
            : collect();
        $equiposTemporada = $idsEquipos->isNotEmpty()
            ? Equipo::whereIn('id', $idsEquipos)->orderBy('nombre')->get()
            : Equipo::orderBy('nombre')->get();

        return view('admin.index', compact(
            'temporada',
            'ultimaTemporada',
            'jornadas',
            'partidosCount',
            'temporadaCompleta',
            'equiposTemporada'
        ));
    })->name('admin');

    Route::get('/admin/partidos', [PartidosController::class, 'index'])
        ->name('admin.partidos.index');

    Route::get('/admin/jornadas', [JornadaController::class, 'index'])->name('admin.jornadas.index');
    Route::post(
        '/admin/partidos/guardar-jornada',
        [PartidosController::class, 'guardarJornada']
    )->name('admin.partidos.guardarJornada');


    Route::resource('/admin/equipos', EquiposController::class)
        ->names('admin.equipos');

    Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

    Route::get(
        '/admin/temporadas',
        [TemporadaController::class, 'index']
    )->name('admin.temporadas.index');

    Route::get(
        '/admin/temporadas/generar',
        [TemporadaController::class, 'generar']
    )->name('admin.temporadas.generar');


    Route::get(
        '/admin/temporadas/{id}/finalizar',
        [TemporadaController::class, 'finalizar']
    )->name('admin.temporadas.finalizar');

    Route::get(
        '/admin/temporadas/{id}',
        [TemporadaController::class, 'show']
    )->name('admin.temporadas.show');


    Route::get(
        '/admin/temporadas/{id}/jornadas',
        [TemporadaController::class, 'jornadas']
    )
        ->name('admin.temporadas.jornadas');


    Route::get(
        '/admin/temporadas/{id}/jornadas/{jornada}',
        [TemporadaController::class, 'jornadas']
    )
        ->name('admin.temporadas.jornada');

    Route::get(
        '/admin/temporadas/{id}/clasificacion',
        [TemporadaController::class, 'clasificacion']
    )
        ->name('admin.temporadas.clasificacion');


    Route::get(
        '/admin/temporadas/{id}/liguilla',
        [TemporadaController::class, 'liguilla']
    )
        ->name('admin.temporadas.liguilla');


    Route::post(
        '/admin/eliminatorias/{id}/resultado',
        [EliminatoriaController::class, 'guardarResultado']
    )
        ->name('admin.eliminatorias.resultado');
});

require __DIR__ . '/auth.php';


//https://chatgpt.com/s/t_6a6c352e75b48191821706ea755f11c4
