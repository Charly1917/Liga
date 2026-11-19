<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eliminatoria extends Model
{

    protected $fillable = [

        'temporada_id',
        'fase',
        'fecha',
        'lugar',

        'equipo_local_id',
        'equipo_visitante_id',

        'ganador_id',

        'goles_local',
        'goles_visitante',

        'penales_local',
        'penales_visitante',

        'estado'

    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];


    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }



    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }



    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }



    public function ganador()
    {
        return $this->belongsTo(Equipo::class, 'ganador_id');
    }
}
