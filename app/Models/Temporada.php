<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temporada extends Model
{

    protected $fillable = [
        'nombre',
        'tipo',
        'activa',
        'fecha_inicio',
        'fecha_fin',
        'campeon_id'
    ];


    public function jornadas()
    {
        return $this->hasMany(Jornada::class);
    }

    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }

    public function campeon()
    {
        return $this->belongsTo(Equipo::class, 'campeon_id');
    }

    public function eliminatorias()
    {
        return $this->hasMany(Eliminatoria::class);
    }
}
