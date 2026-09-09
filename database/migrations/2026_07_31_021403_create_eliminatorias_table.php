<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('eliminatorias', function (Blueprint $table) {

            $table->id();


            $table->foreignId('temporada_id')
                ->constrained('temporadas')
                ->cascadeOnDelete();


            $table->string('fase');


            $table->foreignId('equipo_local_id')
                ->nullable()
                ->constrained('equipos')
                ->nullOnDelete();


            $table->foreignId('equipo_visitante_id')
                ->nullable()
                ->constrained('equipos')
                ->nullOnDelete();


            $table->foreignId('ganador_id')
                ->nullable()
                ->constrained('equipos')
                ->nullOnDelete();


            $table->integer('goles_local')
                ->nullable();


            $table->integer('goles_visitante')
                ->nullable();


            $table->timestamps();

        });
    }



    public function down(): void
    {
        Schema::dropIfExists('eliminatorias');
    }

};