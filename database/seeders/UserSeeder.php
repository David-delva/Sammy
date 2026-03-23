<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@ecole.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Secrétariat',
            'email' => 'secretariat@ecole.com',
            'password' => Hash::make('password'),
            'role' => 'secretariat',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Professeur Principal',
            'email' => 'gomambadelvadavid@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'administrateur',
            // email_verified_at non défini → devra vérifier l'e-mail
        ]);
    }
}
