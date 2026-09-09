<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eliminatoria;
use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Partido;
use App\Models\Temporada;
use Carbon\Carbon;

class TemporadaController extends Controller
{
    public function index()
    {
        $temporadas = Temporada::orderByDesc('id')->get();

        return view('admin.temporadas.index', compact('temporadas'));
    }

    public function finalizar($id)
    {

        $temporada = Temporada::findOrFail($id);


        $temporada->update([

            'activa' => false,

            'fecha_fin' => now()

        ]);



        // Obtener clasificación final

        $equipos = Equipo::all();



        foreach ($equipos as $equipo) {


            $puntos = 0;
            $gf = 0;


            $partidos = Partido::where('temporada_id', $temporada->id)
                ->whereNotNull('goles_local')
                ->where(function ($query) use ($equipo) {

                    $query->where('equipo_local_id', $equipo->id)
                        ->orWhere('equipo_visitante_id', $equipo->id);
                })
                ->get();



            foreach ($partidos as $partido) {


                if ($partido->equipo_local_id == $equipo->id) {


                    $gf += $partido->goles_local;


                    if ($partido->goles_local > $partido->goles_visitante) {

                        $puntos += 3;
                    } elseif ($partido->goles_local == $partido->goles_visitante) {

                        $puntos += 1;
                    }
                } else {


                    $gf += $partido->goles_visitante;


                    if ($partido->goles_visitante > $partido->goles_local) {

                        $puntos += 3;
                    } elseif ($partido->goles_visitante == $partido->goles_local) {

                        $puntos += 1;
                    }
                }
            }



            $equipo->puntos = $puntos;
            $equipo->gf = $gf;
        }



        $clasificacion = $equipos
            ->sortByDesc(function ($equipo) {

                return [
                    $equipo->puntos,
                    $equipo->gf
                ];
            })
            ->values();



        // Crear cuartos de final

        $cruces = [

            [0, 7],
            [1, 6],
            [2, 5],
            [3, 4]

        ];



        foreach ($cruces as $cruce) {


            Eliminatoria::create([

                'temporada_id' => $temporada->id,

                'fase' => 'Cuartos',

                'equipo_local_id' => $clasificacion[$cruce[0]]->id,

                'equipo_visitante_id' => $clasificacion[$cruce[1]]->id

            ]);
        }

        return redirect()
            ->route('admin.temporadas.index')
            ->with(
                'success',
                'Temporada finalizada y liguilla creada.'
            );
    }

    public function show($id)
    {
        $temporada = Temporada::with([
            'jornadas',
            'partidos'
        ])
            ->findOrFail($id);


        return view(
            'admin.temporadas.show',
            compact('temporada')
        );
    }

    public function generar()
    {
        // Cerrar temporada activa
        Temporada::where('activa', true)
            ->update([
                'activa' => false,
                'fecha_fin' => now()
            ]);

        // Obtener última temporada
        $ultimaTemporada = Temporada::latest('id')->first();

        if (!$ultimaTemporada) {

            $nombre = date('Y');
            $tipo = 'Apertura';
        } elseif ($ultimaTemporada->tipo == 'Apertura') {

            $nombre = $ultimaTemporada->nombre;
            $tipo = 'Clausura';
        } else {

            $nombre = $ultimaTemporada->nombre + 1;
            $tipo = 'Apertura';
        }

        // Crear nueva temporada
        $temporada = Temporada::create([
            'nombre' => $nombre,
            'tipo' => $tipo,
            'activa' => true,
            'fecha_inicio' => now()
        ]);

        $equipos = Equipo::all();

        if ($equipos->count() < 2) {

            return back()->with(
                'error',
                'Se necesitan mínimo 2 equipos.'
            );
        }

        $ids = $equipos->pluck('id')->toArray();

        $numEquipos = count($ids);

        // Si hay número impar agregamos descanso
        if ($numEquipos % 2 != 0) {

            $ids[] = null;
            $numEquipos++;
        }

        $numJornadas = $numEquipos - 1;

        $fecha = Carbon::now()
            ->next(Carbon::SATURDAY)
            ->setTime(8, 0);

        $horarios = [
            '08:00',
            '10:00',
            '12:00',
            '14:00',
            '16:00'
        ];

        for ($j = 0; $j < $numJornadas; $j++) {


            $contadorHorario = 0;


            $jornada = Jornada::create([
                'numero' => $j + 1,
                'temporada_id' => $temporada->id
            ]);

            for ($i = 0; $i < $numEquipos / 2; $i++) {

                $posicionHorario = ($i + $j) % count($horarios);

                $horaPartido = $horarios[$posicionHorario];

                $local = $ids[$i];
                $visitante = $ids[$numEquipos - 1 - $i];

                if (is_null($local) || is_null($visitante)) {
                    continue;
                }

                if ($j % 2 == 1) {

                    [$local, $visitante] = [$visitante, $local];
                }

                Partido::create([

                    'jornada_id' => $jornada->id,
                    'temporada_id' => $temporada->id,
                    'equipo_local_id' => $local,
                    'equipo_visitante_id' => $visitante,
                    'fecha' => $fecha->copy()->setTimeFromTimeString($horaPartido),
                    'lugar' => 'Estadio ' . Equipo::find($local)->nombre,
                    'goles_local' => null,
                    'goles_visitante' => null

                ]);
            }

            $fecha = $fecha
                ->next(Carbon::SATURDAY)
                ->setTime(8, 0);

            $primero = $ids[0];

            $resto = array_slice($ids, 1);

            array_unshift(
                $resto,
                array_pop($resto)
            );

            $ids = array_merge([$primero], $resto);
        }

        return redirect()
            ->route('admin.temporadas.index')
            ->with(
                'success',
                'Temporada creada correctamente.'
            );
    }

    public function jornadas($id, $jornada = null)
    {

        $temporada = Temporada::with([
            'jornadas.partidos.equipoLocal',
            'jornadas.partidos.equipoVisitante'
        ])
            ->findOrFail($id);


        $jornadaSeleccionada = null;


        if ($jornada) {

            $jornadaSeleccionada = Jornada::where('temporada_id', $temporada->id)
                ->where('id', $jornada)
                ->firstOrFail();
        }


        return view(
            'admin.temporadas.jornadas',
            compact(
                'temporada',
                'jornadaSeleccionada'
            )
        );
    }

    public function clasificacion($id)
    {

        $temporada = Temporada::findOrFail($id);


        $equipos = Equipo::all();



        foreach ($equipos as $equipo) {

            $pj = 0;
            $gf = 0;
            $gc = 0;
            $puntos = 0;



            $partidos = Partido::where('temporada_id', $temporada->id)
                ->where(function ($query) use ($equipo) {

                    $query->where('equipo_local_id', $equipo->id)
                        ->orWhere('equipo_visitante_id', $equipo->id);
                })
                ->whereNotNull('goles_local')
                ->get();




            foreach ($partidos as $partido) {


                $pj++;



                if ($partido->equipo_local_id == $equipo->id) {

                    $gf += $partido->goles_local;
                    $gc += $partido->goles_visitante;


                    if ($partido->goles_local > $partido->goles_visitante)
                        $puntos += 3;


                    elseif ($partido->goles_local == $partido->goles_visitante)
                        $puntos += 1;
                } else {

                    $gf += $partido->goles_visitante;
                    $gc += $partido->goles_local;


                    if ($partido->goles_visitante > $partido->goles_local)
                        $puntos += 3;


                    elseif ($partido->goles_visitante == $partido->goles_local)
                        $puntos += 1;
                }
            }




            $equipo->pj = $pj;
            $equipo->gf = $gf;
            $equipo->gc = $gc;
            $equipo->puntos = $puntos;
        }



        $equipos = $equipos->sortByDesc(function ($equipo) {

            return [
                $equipo->puntos,
                $equipo->gf
            ];
        });



        return view(
            'admin.temporadas.clasificacion',
            compact(
                'temporada',
                'equipos'
            )
        );
    }

    public function liguilla($id)
    {

        $temporada = Temporada::with([
            'eliminatorias.equipoLocal',
            'eliminatorias.equipoVisitante',
            'eliminatorias.ganador'
        ])
            ->findOrFail($id);



        return view(
            'admin.temporadas.liguilla',
            compact('temporada')
        );
    }
}
