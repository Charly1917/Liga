<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Partido;
use Illuminate\Http\Request;

class EquiposController extends Controller
{
    public function index(Request $request)
    {
        $equipos = Equipo::orderBy('nombre')->get();

        if ($request->routeIs('admin.equipos.*')) {
            return view('admin.equipos.index', compact('equipos'));
        }

        return view('equipos.index', compact('equipos'));
    }

    public function show(Equipo $equipo)
    {
        $partidos = Partido::with(['equipoLocal', 'equipoVisitante'])
            ->where(function ($query) use ($equipo) {
                $query->where('equipo_local_id', $equipo->id)
                    ->orWhere('equipo_visitante_id', $equipo->id);
            })
            ->orderByDesc('fecha')
            ->get();

        $equipos = Equipo::orderBy('nombre')->get();

        return view('equipos.show', compact('equipo', 'partidos', 'equipos'));
    }

    public function create()
    {
        return view('admin.equipos.form', ['equipo' => new Equipo()]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255|unique:equipos,nombre',
            'color' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
        ]);

        $datos['logo'] = $request->file('logo')
            ? $this->guardarLogo($request)
            : '';
        $datos['color'] = $datos['color'] ?? '#0a1a44';

        Equipo::create($datos);

        return redirect()->route('admin.equipos.index')->with('success', 'Equipo agregado correctamente.');
    }

    public function edit(Equipo $equipo)
    {
        return view('admin.equipos.form', compact('equipo'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255|unique:equipos,nombre,' . $equipo->id,
            'color' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->file('logo')) {
            $datos['logo'] = $this->guardarLogo($request);
        } else {
            unset($datos['logo']);
        }

        $equipo->update($datos);

        return redirect()->route('admin.equipos.index')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipo $equipo)
    {
        $equipo->delete();

        return redirect()->route('admin.equipos.index')->with('success', 'Equipo eliminado.');
    }

    private function guardarLogo(Request $request): string
    {
        $archivo = $request->file('logo');
        $nombre = $archivo->hashName();
        $archivo->move(public_path('img/equipos'), $nombre);

        return $nombre;
    }
}
