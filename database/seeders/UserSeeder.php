<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('12345678'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'carlos@gmail.com'],
            [
                'name' => 'Carlos',
                'password' => bcrypt('carlos12345'),
            ]
        );
    }
}