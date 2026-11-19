<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->moveDates(Carbon::SUNDAY);
    }

    public function down(): void
    {
        $this->moveDates(Carbon::SATURDAY);
    }

    private function moveDates(int $dayOfWeek): void
    {
        foreach ([
            'partidos' => 'fecha',
            'eliminatorias' => 'fecha',
        ] as $table => $column) {
            $records = DB::table($table)
                ->whereNotNull($column)
                ->get(['id', $column]);

            foreach ($records as $record) {
                $fecha = Carbon::parse($record->{$column});
                if ($fecha->dayOfWeek !== $dayOfWeek) {
                    $hora = $fecha->format('H:i:s');
                    $fecha->next($dayOfWeek)->setTimeFromTimeString($hora);
                    DB::table($table)
                        ->where('id', $record->id)
                        ->update([$column => $fecha]);
                }
            }
        }
    }
};
