<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
        ]);
        
        User::create([
            'name' => 'Carlos',
            'email' => 'carlos@gmail.com',
            'password' => bcrypt('carlos12345'),
        ]);
    }
}
