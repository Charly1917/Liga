<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('temporadas', function (Blueprint $table) {

            $table->foreignId('campeon_id')
                ->nullable()
                ->after('fecha_fin')
                ->constrained('equipos')
                ->nullOnDelete();

        });
    }


    public function down(): void
    {
        Schema::table('temporadas', function (Blueprint $table) {

            $table->dropForeign(['campeon_id']);
            $table->dropColumn('campeon_id');

        });
    }

};