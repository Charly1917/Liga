<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('eliminatorias', function (Blueprint $table) {

            $table->integer('penales_local')->nullable()->after('goles_visitante');

            $table->integer('penales_visitante')->nullable()->after('penales_local');

            $table->enum('estado', [
                'Pendiente',
                'Finalizado'
            ])->default('Pendiente')->after('ganador_id');

        });
    }

    public function down(): void
    {
        Schema::table('eliminatorias', function (Blueprint $table) {

            $table->dropColumn([
                'penales_local',
                'penales_visitante',
                'estado'
            ]);

        });
    }

};