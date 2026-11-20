<?php

use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Partido;
use App\Models\Temporada;
use App\Models\User;
use App\Services\ClasificacionService;
use App\Services\CampeonService;

it('genera una vuelta para todos los equipos y crea la liguilla principal al finalizar', function () {
    $usuario = User::factory()->create();
    collect(range(1, 8))->each(function ($numero) {
        return Equipo::create([
            'nombre' => 'Equipo ' . $numero,
            'logo' => 'equipo-' . $numero . '.png',
        ]);
    });

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.generar'))
        ->assertRedirect(route('admin.temporadas.show', 1));

    $temporada = Temporada::findOrFail(1);
    $partidos = $temporada->partidos;

    expect($temporada->jornadas)->toHaveCount(7)
        ->and($partidos)->toHaveCount(28)
        ->and($partidos->every(fn (Partido $partido) => $partido->fecha->format('w') === '0'))->toBeTrue()
        ->and($partidos->pluck('equipo_local_id')->merge($partidos->pluck('equipo_visitante_id'))->unique())
        ->toHaveCount(8)
        ->and($partidos->pluck('lugar')->unique())
        ->toHaveCount(4);

    $horasEquipoUno = $partidos
        ->filter(fn (Partido $partido) => $partido->equipo_local_id === 1 || $partido->equipo_visitante_id === 1)
        ->map(fn (Partido $partido) => $partido->fecha->format('H:i'))
        ->unique();
    expect($horasEquipoUno)->toHaveCount(4);

    $cruces = $partidos->groupBy(function ($partido) {
        $ids = [$partido->equipo_local_id, $partido->equipo_visitante_id];
        sort($ids);

        return implode('-', $ids);
    });

    expect($cruces)->toHaveCount(28);
    foreach ($cruces as $partidosDelCruce) {
        expect($partidosDelCruce)->toHaveCount(1);
    }

    $primerPartido = $partidos->first();
    $jornadaUno = $temporada->jornadas->first();
    $jornadaDos = $temporada->jornadas->get(1);
    $partidoFuturo = $partidos->firstWhere('jornada_id', $jornadaDos->id);
    $this->actingAs($usuario)
        ->get(route('admin.partidos.index'))
        ->assertOk()
        ->assertSee('Registrar resultados de la jornada 1')
        ->assertDontSee('Guardar resultado');

    $this->actingAs($usuario)
        ->post(route('admin.partidos.guardarJornada'), [
            'jornada_id' => $jornadaDos->id,
            'resultados' => [
                $partidoFuturo->id => [
                    'goles_local' => 0,
                    'goles_visitante' => 0,
                ],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
    expect($partidoFuturo->fresh()->goles_local)->toBeNull();

    $this->actingAs($usuario)
        ->post(route('admin.partidos.guardarJornada'), [
            'jornada_id' => $jornadaUno->id,
            'resultados' => [
                $primerPartido->id => [
                    'goles_local' => 2,
                    'goles_visitante' => 1,
                ],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
    expect($primerPartido->fresh()->goles_local)->toBeNull();

    $resultadosJornadaUno = $partidos
        ->where('jornada_id', $jornadaUno->id)
        ->mapWithKeys(function (Partido $partido) use ($primerPartido) {
            $esPrimerPartido = $partido->id === $primerPartido->id;

            return [$partido->id => [
                'goles_local' => $esPrimerPartido ? 2 : 0,
                'goles_visitante' => $esPrimerPartido ? 1 : 0,
            ]];
        })
        ->all();

    $this->actingAs($usuario)
        ->post(route('admin.partidos.guardarJornada'), [
            'jornada_id' => $jornadaUno->id,
            'resultados' => $resultadosJornadaUno,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($primerPartido->fresh()->goles_local)->toBe(2)
        ->and($primerPartido->fresh()->goles_visitante)->toBe(1)
        ->and($jornadaUno->partidos()->whereNull('goles_local')->count())->toBe(0);

    $this->actingAs($usuario)
        ->get(route('admin'))
        ->assertOk()
        ->assertSee('Jornada 1')
        ->assertSee('Equipo 1')
        ->assertDontSee('Ver formato de liguilla');
    $this->actingAs($usuario)->get(route('admin.equipos.index'))->assertOk();
    $this->actingAs($usuario)->get(route('admin.equipos.create'))->assertOk();
    $this->actingAs($usuario)->get(route('admin.jornadas.index'))->assertOk();
    $this->actingAs($usuario)->get(route('admin.partidos.index'))->assertOk();
    $this->actingAs($usuario)
        ->get(route('admin.temporadas.liguilla', $temporada->id))
        ->assertRedirect(route('admin.temporadas.show', $temporada->id))
        ->assertSessionHas('error');
    $this->actingAs($usuario)
        ->post(route('admin.equipos.store'), [
            'nombre' => 'Equipo agregado',
            'color' => '#123456',
        ])
        ->assertRedirect(route('admin.equipos.index'));
    $this->assertDatabaseHas('equipos', [
        'nombre' => 'Equipo agregado',
        'logo' => '',
    ]);
    $this->get(route('jornadas'))
        ->assertOk()
        ->assertSee('Equipo 1')
        ->assertSee('Equipo 8')
        ->assertSee('2 - 1')
        ->assertSee('Compartir rol por WhatsApp')
        ->assertSee('Cancha Central')
        ->assertSee(asset('js/compartir-rol.js'));
    $this->get(route('resultados'))
        ->assertOk()
        ->assertSee('Equipo 1')
        ->assertSee('2 - 1');
    $this->get(route('liguilla'))
        ->assertOk()
        ->assertSee('La temporada está en curso')
        ->assertDontSee('Ganador del cruce 1');
    $this->get(route('equipos'))->assertOk();
    $this->get(route('equipos.show', 1))->assertOk();

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.finalizar', $temporada->id))
        ->assertRedirect();
    expect((bool) $temporada->fresh()->activa)->toBeTrue();

    $temporada->partidos()->update([
        'goles_local' => 0,
        'goles_visitante' => 0,
    ]);
    $this->actingAs($usuario)
        ->get(route('admin'))
        ->assertOk()
        ->assertSee('Vista previa del formato')
        ->assertSee('Crear y publicar liguilla');
    $this->actingAs($usuario)
        ->get(route('admin.temporadas.show', $temporada->id))
        ->assertOk()
        ->assertSee('Crear y publicar liguilla');
    $this->get(route('inicio'))
        ->assertOk()
        ->assertSee(route('liguilla'))
        ->assertDontSee('Ver resultados de la liguilla');

    $this->actingAs($usuario)
        ->get(route('admin.partidos.index'))
        ->assertOk()
        ->assertSee('Crear y publicar liguilla')
        ->assertSee(route('admin.temporadas.finalizar', $temporada->id));

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.finalizar', $temporada->id))
        ->assertRedirect(route('admin.temporadas.liguilla', $temporada->id));
    $this->get(route('inicio'))
        ->assertOk()
        ->assertSee(route('liguilla'))
        ->assertSee('Ver resultados de la liguilla');

    expect((bool) $temporada->fresh()->activa)->toBeFalse()
        ->and($temporada->eliminatorias()->where('fase', 'Cuartos')->count())->toBe(4);
    $temporada->update(['activa' => true]);
    $this->actingAs($usuario)
        ->get(route('admin.temporadas.finalizar', $temporada->id))
        ->assertRedirect(route('admin.temporadas.liguilla', $temporada->id))
        ->assertSessionHas('success', 'La liguilla ya estaba creada; no se generaron cruces duplicados.');
    expect($temporada->eliminatorias()->where('fase', 'Cuartos')->count())->toBe(4)
        ->and((bool) $temporada->fresh()->activa)->toBeFalse();
    $this->actingAs($usuario)
        ->get(route('admin.partidos.index'))
        ->assertOk()
        ->assertSee('Capturar resultados de liguilla');

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.generar'))
        ->assertRedirect()
        ->assertSessionHas('error');
    expect(Temporada::count())->toBe(1)
        ->and($temporada->fresh()->fecha_fin)->toBeNull();

    $seeds = app(ClasificacionService::class)->obtener($temporada)->values();
    $cuartos = $temporada->eliminatorias()
        ->where('fase', 'Cuartos')
        ->orderBy('id')
        ->get();
    $ultimoPartidoRegular = $partidos->max(fn (Partido $partido) => $partido->fecha);
    expect($cuartos->every(fn ($partido) => $partido->fecha !== null))->toBeTrue()
        ->and($cuartos->every(fn ($partido) => $partido->fecha->format('w') === '0'))->toBeTrue()
        ->and($cuartos->pluck('lugar')->unique())->toHaveCount(4)
        ->and($cuartos[0]->fecha->gt($ultimoPartidoRegular))->toBeTrue();

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.liguilla', $temporada->id))
        ->assertOk()
        ->assertSee('Guardar')
        ->assertSee('Penales (solo si hay empate)');

    expect([$cuartos[0]->equipo_local_id, $cuartos[0]->equipo_visitante_id])
        ->toBe([$seeds[0]->id, $seeds[7]->id])
        ->and([$cuartos[1]->equipo_local_id, $cuartos[1]->equipo_visitante_id])
        ->toBe([$seeds[1]->id, $seeds[6]->id])
        ->and([$cuartos[2]->equipo_local_id, $cuartos[2]->equipo_visitante_id])
        ->toBe([$seeds[2]->id, $seeds[5]->id])
        ->and([$cuartos[3]->equipo_local_id, $cuartos[3]->equipo_visitante_id])
        ->toBe([$seeds[3]->id, $seeds[4]->id]);

    $fechaSemifinalesPropuesta = $cuartos[0]->fecha->copy()
        ->startOfDay()
        ->addWeek()
        ->setTimeFromTimeString('10:00');
    $this->get(route('liguilla'))
        ->assertOk()
        ->assertSee('Compartir liguilla por WhatsApp')
        ->assertSee('datos-rol-liguilla-1')
        ->assertSee('Domingo ·')
        ->assertSee($cuartos[0]->fecha->format('d/m/Y · H:i'))
        ->assertSee($cuartos[0]->lugar)
        ->assertSee($fechaSemifinalesPropuesta->format('d/m/Y · H:i'))
        ->assertSee(asset('js/compartir-rol.js'));

    foreach ($cuartos as $indice => $cuarto) {
        $this->actingAs($usuario)
            ->post(route('admin.eliminatorias.resultado', $cuarto->id), [
                'goles_local' => 1,
                'goles_visitante' => $indice === 0 ? 1 : 0,
                'penales_local' => $indice === 0 ? 4 : null,
                'penales_visitante' => $indice === 0 ? 3 : null,
            ])
            ->assertRedirect();
    }

    $semifinales = $temporada->eliminatorias()
        ->where('fase', 'Semifinal')
        ->orderBy('id')
        ->get();
    expect($semifinales)->toHaveCount(2)
        ->and($semifinales->every(fn ($partido) => $partido->fecha !== null))->toBeTrue()
        ->and($semifinales->every(fn ($partido) => $partido->fecha->format('w') === '0'))->toBeTrue()
        ->and($semifinales[0]->fecha->gt($cuartos[0]->fecha))->toBeTrue()
        ->and([$semifinales[0]->equipo_local_id, $semifinales[0]->equipo_visitante_id])
        ->toBe([$cuartos[0]->equipo_local_id, $cuartos[3]->equipo_local_id])
        ->and([$semifinales[1]->equipo_local_id, $semifinales[1]->equipo_visitante_id])
        ->toBe([$cuartos[1]->equipo_local_id, $cuartos[2]->equipo_local_id]);

    foreach ($semifinales as $semifinal) {
        $this->actingAs($usuario)
            ->post(route('admin.eliminatorias.resultado', $semifinal->id), [
                'goles_local' => 2,
                'goles_visitante' => 1,
            ])
            ->assertRedirect();
    }

    $final = $temporada->eliminatorias()->where('fase', 'Final')->firstOrFail();
    expect($final->fecha->format('w'))->toBe('0')
        ->and($final->fecha->gt($semifinales[0]->fecha))->toBeTrue()
        ->and($final->lugar)->toBe('Cancha Central');
    $this->actingAs($usuario)
        ->post(route('admin.eliminatorias.resultado', $final->id), [
            'goles_local' => 1,
            'goles_visitante' => 0,
        ])
        ->assertRedirect();

    $campeonId = $semifinales[0]->equipo_local_id;
    expect($temporada->fresh()->campeon_id)->toBe($campeonId)
        ->and($temporada->fresh()->fecha_fin)->not->toBeNull();

    $this->get(route('liguilla'))
        ->assertOk()
        ->assertSee('¡Tenemos campeón!')
        ->assertSee('¡Campeón de ' . $temporada->nombre . ' ' . $temporada->tipo . '!');
    $this->get(route('resultados'))
        ->assertOk()
        ->assertSee('Resultados de la liguilla')
        ->assertSee('Cuartos')
        ->assertSee('1 - 0')
        ->assertSee('Penales: 4 - 3');
    $campeon = Equipo::findOrFail($campeonId);
    $this->get(route('inicio'))
        ->assertOk()
        ->assertSee('¡La liga felicita al campeón!')
        ->assertSee($campeon->nombre);

    $crucesDeJornada = function (Temporada $temporada, int $numero) {
        return $temporada->jornadas()
            ->where('numero', $numero)
            ->firstOrFail()
            ->partidos
            ->map(function (Partido $partido) {
                $equipos = [$partido->equipo_local_id, $partido->equipo_visitante_id];
                sort($equipos);

                return implode('-', $equipos);
            })
            ->sort()
            ->values()
            ->all();
    };
    $primeraJornada = $crucesDeJornada($temporada, 1);
    $ultimaJornada = $crucesDeJornada($temporada, 7);

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.generar'))
        ->assertRedirect(route('admin.temporadas.show', 2));
    $siguienteTemporada = Temporada::findOrFail(2);
    $siguienteInicio = $crucesDeJornada($siguienteTemporada, 1);

    expect($siguienteInicio)->not->toBe($primeraJornada)
        ->and($siguienteInicio)->not->toBe($ultimaJornada);

    $this->get(route('liguilla'))
        ->assertOk()
        ->assertSee('La temporada está en curso')
        ->assertSee('Fase regular en curso')
        ->assertDontSee('El campeón defensor')
        ->assertSee(route('liguilla', ['temporada' => $temporada->id]));
    $this->get(route('liguilla', ['temporada' => $temporada->id]))
        ->assertOk()
        ->assertSee('Temporada concluida')
        ->assertSee($campeon->nombre);
    $this->get(route('inicio'))
        ->assertOk()
        ->assertSee('Campeón defensor')
        ->assertSee($campeon->nombre);
});

it('incluye los 20 equipos y mantiene independiente la liguilla de consolación', function () {
    $usuario = User::factory()->create();
    $equipos = collect(range(1, 20))->map(function ($numero) {
        return Equipo::create([
            'nombre' => sprintf('Club %02d', $numero),
            'logo' => 'club-' . $numero . '.png',
        ]);
    });

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.generar'))
        ->assertRedirect(route('admin.temporadas.show', 1));

    $temporada = Temporada::findOrFail(1);
    $partidos = $temporada->partidos;
    expect($temporada->jornadas)->toHaveCount(19)
        ->and($partidos)->toHaveCount(190)
        ->and($partidos->pluck('equipo_local_id')->merge($partidos->pluck('equipo_visitante_id'))->unique())
        ->toHaveCount(20)
        ->and($partidos->groupBy(function (Partido $partido) {
            $ids = [$partido->equipo_local_id, $partido->equipo_visitante_id];
            sort($ids);

            return implode('-', $ids);
        }))->toHaveCount(190);

    foreach ($equipos as $equipo) {
        $partidosDelEquipo = $partidos->filter(fn (Partido $partido) => in_array(
            $equipo->id,
            [$partido->equipo_local_id, $partido->equipo_visitante_id]
        ));
        $posicionesDelRol = $partidosDelEquipo->map(function (Partido $partido) use ($partidos) {
            return $partidos
                ->where('jornada_id', $partido->jornada_id)
                ->sortBy('fecha')
                ->values()
                ->search(fn (Partido $partidoDeJornada) => $partidoDeJornada->id === $partido->id);
        })->unique();

        expect($partidosDelEquipo)->toHaveCount(19)
            ->and($partidosDelEquipo->pluck('fecha')->map(fn ($fecha) => $fecha->format('H:i'))->unique())
            ->toHaveCount(3)
            ->and($posicionesDelRol->count())->toBeGreaterThan(1);
    }

    foreach ($temporada->jornadas as $jornada) {
        $partidosDeJornada = $partidos->where('jornada_id', $jornada->id);
        $horariosYcanchas = $partidosDeJornada->map(
            fn (Partido $partido) => $partido->fecha->format('H:i') . '|' . $partido->lugar
        )->unique();

        expect($horariosYcanchas)->toHaveCount($partidosDeJornada->count());
    }

    $temporada->partidos()->update([
        'goles_local' => 0,
        'goles_visitante' => 0,
    ]);

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.liguilla', $temporada->id))
        ->assertOk()
        ->assertSee('Liguilla de consolación')
        ->assertSee('Club 09')
        ->assertSee('Club 16');

    $this->actingAs($usuario)
        ->get(route('admin.temporadas.finalizar', $temporada->id))
        ->assertRedirect(route('admin.temporadas.liguilla', $temporada->id));

    $clasificacion = app(ClasificacionService::class)->obtener($temporada)->values();
    $cuartos = $temporada->eliminatorias()->where('fase', 'Cuartos')->orderBy('id')->get();
    $cuartosConsolacion = $temporada->eliminatorias()
        ->where('fase', 'Consolación Cuartos')
        ->orderBy('id')
        ->get();

    expect($cuartos)->toHaveCount(4)
        ->and($cuartosConsolacion)->toHaveCount(4)
        ->and([$cuartosConsolacion[0]->equipo_local_id, $cuartosConsolacion[0]->equipo_visitante_id])
        ->toBe([$clasificacion[8]->id, $clasificacion[15]->id])
        ->and([$cuartosConsolacion[3]->equipo_local_id, $cuartosConsolacion[3]->equipo_visitante_id])
        ->toBe([$clasificacion[11]->id, $clasificacion[12]->id]);

    foreach ($cuartos->concat($cuartosConsolacion) as $partido) {
        $this->actingAs($usuario)
            ->post(route('admin.eliminatorias.resultado', $partido->id), [
                'goles_local' => 1,
                'goles_visitante' => 0,
            ])
            ->assertRedirect();
    }

    $semifinales = $temporada->eliminatorias()->where('fase', 'Semifinal')->get();
    $semifinalesConsolacion = $temporada->eliminatorias()
        ->where('fase', 'Consolación Semifinal')
        ->get();
    expect($semifinales)->toHaveCount(2)
        ->and($semifinalesConsolacion)->toHaveCount(2);

    foreach ($semifinalesConsolacion as $partido) {
        $this->actingAs($usuario)
            ->post(route('admin.eliminatorias.resultado', $partido->id), [
                'goles_local' => 2,
                'goles_visitante' => 0,
            ])
            ->assertRedirect();
    }

    $finalConsolacion = $temporada->eliminatorias()
        ->where('fase', 'Consolación Final')
        ->firstOrFail();
    $this->actingAs($usuario)
        ->post(route('admin.eliminatorias.resultado', $finalConsolacion->id), [
            'goles_local' => 1,
            'goles_visitante' => 0,
        ])
        ->assertRedirect();
    expect($temporada->fresh()->campeon_id)->toBeNull();

    foreach ($semifinales as $partido) {
        $this->actingAs($usuario)
            ->post(route('admin.eliminatorias.resultado', $partido->id), [
                'goles_local' => 1,
                'goles_visitante' => 0,
            ])
            ->assertRedirect();
    }

    $finalPrincipal = $temporada->eliminatorias()->where('fase', 'Final')->firstOrFail();
    $this->actingAs($usuario)
        ->post(route('admin.eliminatorias.resultado', $finalPrincipal->id), [
            'goles_local' => 1,
            'goles_visitante' => 0,
        ])
        ->assertRedirect();
    expect($temporada->fresh()->campeon_id)->toBe($finalPrincipal->equipo_local_id);

    $this->get(route('liguilla', ['temporada' => $temporada->id]))
        ->assertOk()
        ->assertSee('Liguilla de consolación')
        ->assertSee('Cuartos de consolación')
        ->assertSee('Final de consolación');
});

it('calcula puntos y goles únicamente con los marcadores completos de la temporada', function () {
    $equipos = collect(['Alfa', 'Bravo', 'Charlie'])->map(function ($nombre) {
        return Equipo::create([
            'nombre' => $nombre,
            'logo' => strtolower($nombre) . '.png',
        ]);
    });

    $temporada = Temporada::create([
        'nombre' => '2026',
        'tipo' => 'Apertura',
        'activa' => true,
    ]);
    $jornada = Jornada::create([
        'numero' => 1,
        'temporada_id' => $temporada->id,
    ]);

    foreach ([
        [$equipos[0]->id, $equipos[1]->id, 2, 1],
        [$equipos[1]->id, $equipos[2]->id, 0, 0],
        [$equipos[0]->id, $equipos[2]->id, null, null],
    ] as [$local, $visitante, $golesLocal, $golesVisitante]) {
        Partido::create([
            'jornada_id' => $jornada->id,
            'temporada_id' => $temporada->id,
            'equipo_local_id' => $local,
            'equipo_visitante_id' => $visitante,
            'fecha' => now(),
            'goles_local' => $golesLocal,
            'goles_visitante' => $golesVisitante,
        ]);
    }

    $tabla = app(ClasificacionService::class)->obtener($temporada)->keyBy('id');

    expect($tabla[$equipos[0]->id]->puntos)->toBe(3)
        ->and($tabla[$equipos[0]->id]->pj)->toBe(1)
        ->and($tabla[$equipos[0]->id]->gf)->toBe(2)
        ->and($tabla[$equipos[0]->id]->gc)->toBe(1)
        ->and($tabla[$equipos[1]->id]->puntos)->toBe(1)
        ->and($tabla[$equipos[1]->id]->gf)->toBe(1)
        ->and($tabla[$equipos[1]->id]->gc)->toBe(2)
        ->and($tabla[$equipos[2]->id]->puntos)->toBe(1)
        ->and($tabla[$equipos[2]->id]->pj)->toBe(1);

    $this->get(route('inicio'))
        ->assertOk()
        ->assertSee('$1,500')
        ->assertSee('$450');
});

it('felicita al bicampeón o tricampeón solo por títulos consecutivos', function () {
    $campeon = Equipo::create([
        'nombre' => 'Equipo Campeón',
        'logo' => 'campeon.png',
    ]);
    $otroEquipo = Equipo::create([
        'nombre' => 'Otro Campeón',
        'logo' => 'otro.png',
    ]);

    $crearTemporada = function (string $nombre, Equipo $equipo) {
        return Temporada::create([
            'nombre' => $nombre,
            'tipo' => 'Apertura',
            'activa' => false,
            'campeon_id' => $equipo->id,
        ]);
    };

    $primera = $crearTemporada('2024', $campeon);
    $segunda = $crearTemporada('2025', $campeon);
    $tercera = $crearTemporada('2026', $campeon);
    $cuarta = $crearTemporada('2027', $otroEquipo);

    $servicio = app(CampeonService::class);
    expect($servicio->mensajeFelicitacion($primera))->toBe('¡La liga felicita al campeón!')
        ->and($servicio->mensajeFelicitacion($segunda))->toBe('¡Felicidades al bicampeón!')
        ->and($servicio->mensajeFelicitacion($tercera))->toBe('¡Felicidades al tricampeón!')
        ->and($servicio->mensajeFelicitacion($cuarta))->toBe('¡La liga felicita al campeón!');
});
