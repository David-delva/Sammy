<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Seeder;

class ClasseSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ['6ème A', '6ème B', '5ème A', '4ème A', '3ème A'];

        foreach ($classes as $classe) {
            Classe::create(['nom_classe' => $classe]);
        }
    }
}
