<?php

use App\Http\Controllers\Admin\EliminatoriaController;
use App\Http\Controllers\Admin\JornadaController;
use App\Http\Controllers\Admin\TemporadaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PartidosController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');

// Públicas
Route::get('/clasificacion', [ClasificacionController::class, 'index'])->name('clasificacion');
Route::get('/equipos', [EquiposController::class, 'index'])->name('equipos');
Route::get('/partidos/resultados', [PartidosController::class, 'resultados'])->name('resultados');
Route::get('/partidos/jornadas', [PartidosController::class, 'jornadas'])->name('jornadas');

// Admin
Route::middleware('auth')->group(function () {

    Route::get('/admin', function () {
        return view('admin.index');
    })->name('admin');

    Route::resource('/admin/partidos', PartidosController::class)
        ->names('admin.partidos');

    Route::get('/admin/jornadas', [JornadaController::class, 'index'])->name('admin.jornadas.index');
    Route::get('/admin/jornadas/generar', [JornadaController::class, 'generar'])->name('admin.jornadas.generar');
    Route::post('/admin/jornadas/guardar', [JornadaController::class, 'store'])->name('admin.jornadas.store');
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
