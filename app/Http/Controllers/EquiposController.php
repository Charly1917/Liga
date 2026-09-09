<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;

class EquiposController extends Controller
{
    public function index (){
        $equipos = Equipo::all();
        return view('equipos.index', compact('equipos'));
    }
}
