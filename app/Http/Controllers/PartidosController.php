<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Equipo;
use Illuminate\Http\Request;

class PartidosController extends Controller
{
    public function index()
    {
        $temporada = \App\Models\Temporada::where('activa', true)->first();

        $jornadas = $temporada
            ? \App\Models\Jornada::where('temporada_id', $temporada->id)
            ->orderBy('numero')
            ->get()
            : collect();
        $jornadaSeleccionada = request('jornada');


        if ($jornadaSeleccionada) {

            $partidos = Partido::with([
                'equipoLocal',
                'equipoVisitante',
                'jornada'
            ])
                ->where('jornada_id', $jornadaSeleccionada)
                ->get();
        } else {

            $partidos = collect();
        }


        return view('admin.partidos.index', compact(
            'jornadas',
            'partidos',
            'jornadaSeleccionada'
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
        $partidos = Partido::where('fecha', '>', now())
            ->orderBy('fecha')
            ->get();

        $equipos = Equipo::all(); // Para el slider del layout

        return view('jornadas.index', compact('partidos', 'equipos'));
    }

    // Resultados pasados (opcional)
    public function resultados()
    {
        $partidos = Partido::where('fecha', '<=', now())
            ->orderByDesc('fecha')
            ->get();

        $equipos = Equipo::all(); // Si tu layout los necesita
        return view('partidos.resultados', compact('partidos', 'equipos'));
    }

    public function guardarJornada(Request $request)
    {
        $resultados = $request->resultados;

        foreach ($resultados as $id => $resultado) {

            $partido = Partido::find($id);

            if ($partido) {

                $partido->update([
                    'goles_local' => $resultado['goles_local'],
                    'goles_visitante' => $resultado['goles_visitante'],
                ]);
            }
        }


        return back()->with(
            'success',
            'Resultados de la jornada guardados correctamente.'
        );
    }
}
