<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    protected $fillable = [
        'jornada_id',
        'temporada_id',
        'equipo_local_id',
        'equipo_visitante_id',
        'fecha',
        'lugar',
        'goles_local',
        'goles_visitante',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function jornada()
    {
        return $this->belongsTo(Jornada::class);
    }

    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }
    
    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }
}
