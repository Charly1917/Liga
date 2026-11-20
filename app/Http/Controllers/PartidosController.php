<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Equipo;
use App\Models\Jornada;
use App\Models\Temporada;
use App\Services\JornadaOrdenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartidosController extends Controller
{
    public function index(JornadaOrdenService $jornadaOrdenService)
    {
        $temporada = Temporada::where('activa', true)->latest('id')->first();
        $liguillaCreada = $temporada
            ? $temporada->eliminatorias()->exists()
            : false;
        $temporadaLiguilla = $temporada
            ? null
            : Temporada::with('eliminatorias')
                ->whereNull('campeon_id')
                ->whereHas('eliminatorias')
                ->latest('id')
                ->first();
        $jornadas = $temporada
            ? $temporada->jornadas()->orderBy('numero')->get()
            : collect();
        $jornadaHabilitada = $temporada
            ? $jornadaOrdenService->siguiente($temporada)
            : null;
        $jornadaSolicitada = $jornadas->firstWhere('id', (int) request('jornada'));
        $jornadaSeleccionada = $jornadaSolicitada
            ?? $jornadaHabilitada
            ?? $jornadas->last();
        $partidos = $jornadaSeleccionada
            ? $jornadaSeleccionada->partidos()
                ->with(['equipoLocal', 'equipoVisitante'])
                ->orderBy('fecha')
                ->orderBy('id')
                ->get()
            : collect();
        $puedeCapturar = $jornadaSeleccionada
            && $jornadaHabilitada
            && $jornadaSeleccionada->is($jornadaHabilitada);

        return view('admin.partidos.index', compact(
            'temporada',
            'liguillaCreada',
            'temporadaLiguilla',
            'jornadas',
            'partidos',
            'jornadaSeleccionada',
            'jornadaHabilitada',
            'puedeCapturar'
        ));
    }

    public function show(Partido $partido)
    {
        return view('admin.partidos.show', compact('partido'));
    }

    public function create()
    {
        $equipos = Equipo::all();
        return view('admin.partidos.create', compact('equipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'goles_local' => 'nullable|integer|min:0',
            'goles_visitante' => 'nullable|integer|min:0',
            'fecha' => 'required|date',
        ]);

        Partido::create($request->all());
        return redirect()->route('partidos.index')->with('success', 'Partido creado');
    }

    public function edit(Partido $partido)
    {
        $equipos = Equipo::all();
        return view('admin.partidos.edit', compact('partido', 'equipos'));
    }

    public function update(Request $request, Partido $partido)
    {
        $request->validate([
            'goles_local' => 'required|integer|min:0',
            'goles_visitante' => 'required|integer|min:0',
        ]);

        $partido->update([
            'goles_local' => $request->goles_local,
            'goles_visitante' => $request->goles_visitante,
        ]);

        return redirect()->route('admin.partidos.index', [
            'jornada' => $partido->jornada_id
        ])->with('success', 'Resultado guardado correctamente');
    }


    public function destroy(Partido $partido)
    {
        $partido->delete();
        return redirect()->route('partidos.index')->with('success', 'Partido eliminado');
    }

    // Página pública de próximas jornadas
    public function jornadas()
    {
        $temporada = Temporada::where('activa', true)->latest('id')->first()
            ?? Temporada::latest('id')->first();
        $jornadas = $temporada
            ? $temporada->jornadas()
                ->with([
                    'partidos' => fn ($query) => $query->orderBy('fecha')->orderBy('id'),
                    'partidos.equipoLocal',
                    'partidos.equipoVisitante',
                ])
                ->orderBy('numero')
                ->get()
            : collect();

        $equipos = Equipo::all();

        return view('jornadas.index', compact('jornadas', 'equipos', 'temporada'));
    }

    // Resultados pasados (opcional)
    public function resultados()
    {
        $temporada = Temporada::where('activa', true)->latest('id')->first()
            ?? Temporada::latest('id')->first();
        $partidos = Partido::with(['equipoLocal', 'equipoVisitante', 'jornada'])
            ->when($temporada, fn ($query) => $query->where('temporada_id', $temporada->id))
            ->whereNotNull('goles_local')
            ->whereNotNull('goles_visitante')
            ->orderByDesc('fecha')
            ->get();

        $temporadaLiguilla = $temporada && $temporada->eliminatorias()->exists()
            ? $temporada
            : null;
        $eliminatorias = $temporadaLiguilla
            ? $temporadaLiguilla->eliminatorias()
                ->with(['equipoLocal', 'equipoVisitante', 'ganador'])
                ->whereNotNull('goles_local')
                ->whereNotNull('goles_visitante')
                ->orderBy('id')
                ->get()
            : collect();

        $equipos = Equipo::all();
        return view('partidos.resultados', compact(
            'partidos',
            'equipos',
            'eliminatorias',
            'temporadaLiguilla',
            'temporada'
        ));
    }

    public function guardarJornada(Request $request, JornadaOrdenService $jornadaOrdenService)
    {
        $temporada = Temporada::where('activa', true)->latest('id')->first();
        if (! $temporada) {
            return back()->with('error', 'No hay una temporada activa para guardar resultados.');
        }

        $datos = $request->validate([
            'jornada_id' => 'required|integer',
            'resultados' => 'required|array',
            'resultados.*.goles_local' => 'required|integer|min:0',
            'resultados.*.goles_visitante' => 'required|integer|min:0',
        ]);

        $jornadaHabilitada = $jornadaOrdenService->siguiente($temporada);
        if (! $jornadaHabilitada || (int) $datos['jornada_id'] !== $jornadaHabilitada->id) {
            return back()->with('error', 'Captura primero todos los resultados de la jornada habilitada.');
        }

        $jornada = Jornada::where('temporada_id', $temporada->id)
            ->findOrFail($datos['jornada_id']);
        $idsSolicitados = array_keys($datos['resultados']);
        $totalPartidos = $jornada->partidos()->count();
        if (count($idsSolicitados) !== $totalPartidos) {
            return back()->with('error', 'Registra el marcador de todos los partidos antes de enviar la jornada.');
        }

        $idsPermitidos = $jornada->partidos()
            ->whereIn('id', $idsSolicitados)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        if (count($idsPermitidos) !== count($idsSolicitados)) {
            return back()->with('error', 'Solo puedes guardar partidos de la jornada seleccionada.');
        }

        DB::transaction(function () use ($datos, $jornada, $temporada) {
            foreach ($datos['resultados'] as $id => $resultado) {
                Partido::where('id', $id)
                    ->where('jornada_id', $jornada->id)
                    ->where('temporada_id', $temporada->id)
                    ->update([
                        'goles_local' => $resultado['goles_local'],
                        'goles_visitante' => $resultado['goles_visitante'],
                    ]);
            }
        });


        $partidosPendientes = $jornada->partidos()
            ->where(function ($query) {
                $query->whereNull('goles_local')
                    ->orWhereNull('goles_visitante');
            })
            ->exists();

        return back()->with(
            'success',
            $partidosPendientes
                ? 'Resultado guardado. Captura los demás partidos para habilitar la siguiente jornada.'
                : 'Jornada completada. Ya puedes capturar la siguiente jornada.'
        );
    }
}
