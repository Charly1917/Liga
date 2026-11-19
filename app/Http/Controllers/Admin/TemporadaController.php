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
            return back()->with('error', 'Completa los 28 resultados de las 7 jornadas antes de finalizar la temporada.');
        }

        $clasificacion = $clasificacionService->obtener($temporada);
        if ($clasificacion->count() !== 8) {
            return back()->with('error', 'La liguilla requiere exactamente 8 equipos participantes.');
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
        });

        return redirect()
            ->route('admin.temporadas.liguilla', $temporada->id)
            ->with('success', 'Temporada finalizada y liguilla creada.');
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

        $equipos = Equipo::orderBy('id')->limit(8)->get();
        if ($equipos->count() !== 8) {
            return back()->with('error', 'Se necesitan 8 equipos registrados para iniciar una temporada.');
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
        $numEquipos = count($ids);
        $numJornadas = $numEquipos - 1;
        $desplazamiento = Temporada::whereNotNull('campeon_id')->count() % $numJornadas;
        for ($i = 0; $i < $desplazamiento; $i++) {
            $ids = $this->rotarEquipos($ids);
        }

        $fecha = Carbon::now()
            ->next(Carbon::SUNDAY)
            ->setTime(8, 0);
        $horarios = [
            '08:00',
            '10:00',
            '12:00',
            '14:00',
        ];
        $campos = [
            'Cancha Central',
            'Estadio Municipal',
            'Campo Norte',
            'Campo Sur',
        ];

        $temporada = DB::transaction(function () use ($nombre, $tipo, $ids, $numEquipos, $numJornadas, $fecha, $horarios, $campos) {
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

                for ($i = 0; $i < $numEquipos / 2; $i++) {
                    $local = $ids[$i];
                    $visitante = $ids[$numEquipos - 1 - $i];

                    if ($j % 2 === 1) {
                        [$local, $visitante] = [$visitante, $local];
                    }

                    $indiceRotacion = ($i + $j) % count($horarios);
                    $horaPartido = $horarios[$indiceRotacion];
                    Partido::create([
                        'jornada_id' => $jornada->id,
                        'temporada_id' => $temporada->id,
                        'equipo_local_id' => $local,
                        'equipo_visitante_id' => $visitante,
                        'fecha' => $fecha->copy()->setTimeFromTimeString($horaPartido),
                        'lugar' => $campos[$indiceRotacion],
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
            ->with('success', 'Temporada creada con 8 equipos y 7 jornadas. El calendario inicia con cruces distintos a los de la temporada anterior.');
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
        if ($temporada->partidos()->count() !== 28) {
            return false;
        }

        return ! $temporada->partidos()
            ->where(function ($query) {
                $query->whereNull('goles_local')
                    ->orWhereNull('goles_visitante');
            })
            ->exists();
    }
}
