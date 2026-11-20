<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eliminatoria;
use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Partido;
use App\Models\Temporada;
use App\Services\ClasificacionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TemporadaController extends Controller
{
    public function index()
    {
        $temporadas = Temporada::orderByDesc('id')->get();
        $ultimaTemporada = $temporadas->first();
        $puedeCrearTemporada = ! $ultimaTemporada
            || (! $ultimaTemporada->activa && $ultimaTemporada->campeon_id !== null);

        return view('admin.temporadas.index', compact('temporadas', 'puedeCrearTemporada'));
    }

    public function finalizar($id, ClasificacionService $clasificacionService)
    {
        $temporada = Temporada::findOrFail($id);
        if (! $temporada->activa) {
            return back()->with('error', 'Esta temporada ya fue finalizada.');
        }

        if ($temporada->eliminatorias()->exists()) {
            $temporada->update(['activa' => false]);

            return redirect()
                ->route('admin.temporadas.liguilla', $temporada->id)
                ->with('success', 'La liguilla ya estaba creada; no se generaron cruces duplicados.');
        }

        if (! $this->resultadosCompletos($temporada)) {
            return back()->with('error', 'Completa todos los resultados de la fase regular antes de finalizar la temporada.');
        }

        $clasificacion = $clasificacionService->obtener($temporada);
        if ($clasificacion->count() < 8) {
            return back()->with('error', 'La liguilla principal requiere al menos 8 equipos participantes.');
        }

        $ultimoPartido = $temporada->partidos()->max('fecha');
        $fechaCuartos = Carbon::parse($ultimoPartido)
            ->next(Carbon::SUNDAY)
            ->startOfDay();
        $horariosCuartos = ['08:00', '10:00', '12:00', '14:00'];
        $camposCuartos = ['Cancha Central', 'Estadio Municipal', 'Campo Norte', 'Campo Sur'];

        DB::transaction(function () use (
            $temporada,
            $clasificacion,
            $fechaCuartos,
            $horariosCuartos,
            $camposCuartos
        ) {
            $temporada->update([
                'activa' => false,
            ]);

            foreach ([[0, 7], [1, 6], [2, 5], [3, 4]] as $indice => [$local, $visitante]) {
                Eliminatoria::create([
                    'temporada_id' => $temporada->id,
                    'fase' => 'Cuartos',
                    'equipo_local_id' => $clasificacion[$local]->id,
                    'equipo_visitante_id' => $clasificacion[$visitante]->id,
                    'fecha' => $fechaCuartos->copy()
                        ->setTimeFromTimeString($horariosCuartos[$indice]),
                    'lugar' => $camposCuartos[$indice],
                ]);
            }

            if ($clasificacion->count() >= 16) {
                foreach ([[8, 15], [9, 14], [10, 13], [11, 12]] as [$local, $visitante]) {
                    Eliminatoria::create([
                        'temporada_id' => $temporada->id,
                        'fase' => 'Consolación Cuartos',
                        'equipo_local_id' => $clasificacion[$local]->id,
                        'equipo_visitante_id' => $clasificacion[$visitante]->id,
                        'fecha' => $fechaCuartos->copy()
                            ->setTimeFromTimeString('16:00'),
                        'lugar' => $camposCuartos[$indice],
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.temporadas.liguilla', $temporada->id)
            ->with('success', 'Temporada finalizada; se creó la liguilla principal y, cuando hay 16 equipos, la de consolación.');
    }

    public function show($id)
    {
        $temporada = Temporada::with([
            'jornadas',
            'partidos'
        ])
            ->findOrFail($id);

        $resultadosCompletos = $this->resultadosCompletos($temporada);

        return view(
            'admin.temporadas.show',
            compact('temporada', 'resultadosCompletos')
        );
    }

    public function generar()
    {
        if (Temporada::where('activa', true)->exists()) {
            return back()->with('error', 'Finaliza la temporada activa antes de crear otra.');
        }

        $equipos = Equipo::orderBy('id')->get();
        if ($equipos->count() < 8) {
            return back()->with('error', 'Se necesitan al menos 8 equipos registrados para iniciar una temporada.');
        }

        $ultimaTemporada = Temporada::latest('id')->first();

        if ($ultimaTemporada && $ultimaTemporada->campeon_id === null) {
            return back()->with('error', 'Completa la liguilla y registra al campeón antes de iniciar una nueva temporada.');
        }

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

        $ids = $equipos->pluck('id')->toArray();
        if (count($ids) % 2 !== 0) {
            $ids[] = null;
        }
        $numEquipos = count($ids);
        $numJornadas = $numEquipos - 1;
        $desplazamiento = Temporada::whereNotNull('campeon_id')->count() % $numJornadas;
        for ($i = 0; $i < $desplazamiento; $i++) {
            $ids = $this->rotarEquipos($ids);
        }

        $fecha = Carbon::now()
            ->next(Carbon::SUNDAY)
            ->setTime(8, 0);
        $campos = [
            'Cancha Central',
            'Estadio Municipal',
            'Campo Norte',
            'Campo Sur',
        ];

        $temporada = DB::transaction(function () use ($nombre, $tipo, $ids, $numEquipos, $numJornadas, $fecha, $campos) {
            $temporada = Temporada::create([
                'nombre' => $nombre,
                'tipo' => $tipo,
                'activa' => true,
                'fecha_inicio' => now(),
            ]);

            for ($j = 0; $j < $numJornadas; $j++) {
                $jornada = Jornada::create([
                    'numero' => $j + 1,
                    'temporada_id' => $temporada->id,
                ]);

                $partidosJornada = [];
                for ($i = 0; $i < $numEquipos / 2; $i++) {
                    $local = $ids[$i];
                    $visitante = $ids[$numEquipos - 1 - $i];

                    if ($local === null || $visitante === null) {
                        continue;
                    }

                    if ($j % 2 === 1) {
                        [$local, $visitante] = [$visitante, $local];
                    }

                    $partidosJornada[] = [
                        'equipo_local_id' => $local,
                        'equipo_visitante_id' => $visitante,
                    ];
                }

                $desplazamientoPartidos = $j % count($partidosJornada);
                $partidosJornada = array_merge(
                    array_slice($partidosJornada, $desplazamientoPartidos),
                    array_slice($partidosJornada, 0, $desplazamientoPartidos)
                );

                foreach ($partidosJornada as $indice => $partido) {
                    $indiceCancha = ($indice + $j) % count($campos);
                    $indiceHorario = count($partidosJornada) <= count($campos)
                        ? $indice
                        : intdiv($indice, count($campos));
                    $horaPartido = $fecha->copy()
                        ->setTime(8, 0)
                        ->addHours(2 * $indiceHorario);
                    Partido::create([
                        'jornada_id' => $jornada->id,
                        'temporada_id' => $temporada->id,
                        'equipo_local_id' => $partido['equipo_local_id'],
                        'equipo_visitante_id' => $partido['equipo_visitante_id'],
                        'fecha' => $horaPartido,
                        'lugar' => $campos[$indiceCancha],
                        'goles_local' => null,
                        'goles_visitante' => null,
                    ]);
                }

                $fecha = $fecha->copy()->addWeek();
                $ids = $this->rotarEquipos($ids);
            }

            return $temporada;
        });

        return redirect()
            ->route('admin.temporadas.show', $temporada->id)
            ->with('success', 'Temporada creada con todos los equipos registrados y calendario a una vuelta.');
    }

    private function rotarEquipos(array $ids): array
    {
        $primero = $ids[0];
        $resto = array_slice($ids, 1);
        array_unshift($resto, array_pop($resto));

        return array_merge([$primero], $resto);
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

    public function clasificacion($id, ClasificacionService $clasificacionService)
    {
        $temporada = Temporada::findOrFail($id);
        $equipos = $clasificacionService->obtener($temporada);



        return view(
            'admin.temporadas.clasificacion',
            compact(
                'temporada',
                'equipos'
            )
        );
    }

    public function liguilla($id, ClasificacionService $clasificacionService)
    {
        $temporada = Temporada::with([
            'eliminatorias.equipoLocal',
            'eliminatorias.equipoVisitante',
            'eliminatorias.ganador',
            'campeon',
        ])
            ->findOrFail($id);

        $vistaPrevia = $temporada->eliminatorias->isEmpty();
        if ($vistaPrevia && ! $this->resultadosCompletos($temporada)) {
            return redirect()
                ->route('admin.temporadas.show', $temporada->id)
                ->with('error', 'El formato de liguilla estará disponible cuando se registren todos los resultados de la fase regular.');
        }

        $clasificacion = $vistaPrevia
            ? $clasificacionService->obtener($temporada)
            : collect();

        return view(
            'admin.temporadas.liguilla',
            compact('temporada', 'vistaPrevia', 'clasificacion')
        );
    }

    private function resultadosCompletos(Temporada $temporada): bool
    {
        $partidos = $temporada->partidos();
        $totalPartidos = (clone $partidos)->count();
        $equiposParticipantes = (clone $partidos)
            ->get(['equipo_local_id', 'equipo_visitante_id'])
            ->flatMap(fn (Partido $partido) => [$partido->equipo_local_id, $partido->equipo_visitante_id])
            ->filter()
            ->unique()
            ->count();

        $partidosEsperados = intdiv($equiposParticipantes * ($equiposParticipantes - 1), 2);
        if ($equiposParticipantes < 8 || $totalPartidos !== $partidosEsperados) {
            return false;
        }

        return ! $partidos
            ->where(function ($query) {
                $query->whereNull('goles_local')
                    ->orWhereNull('goles_visitante');
            })
            ->exists();
    }
}
