<?php

namespace Database\Seeders;

use App\Models\Matiere;
use Illuminate\Database\Seeder;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        $matieres = [
            'Mathématiques',
            'Français',
            'Anglais',
            'Sciences Physiques',
            'SVT',
            'Histoire-Géographie',
            'EPS',
            'Informatique',
            'Philosophie',
            'Arts Plastiques',
            'Musique',
            'Technologie'
        ];

        foreach ($matieres as $matiere) {
            Matiere::create(['nom_matiere' => $matiere]);
        }
    }
}
