<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{

    protected $fillable = [
        'numero',
        'temporada_id'
    ];


    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }


    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }
}
