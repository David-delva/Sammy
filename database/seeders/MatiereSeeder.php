<?php

namespace Database\Seeders;

use App\Models\Matiere;
use App\Models\Classe;
use Illuminate\Database\Seeder;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classe::all();
        $matieres = [
            ['nom' => 'Mathématiques', 'coef' => 4],
            ['nom' => 'Français', 'coef' => 3],
            ['nom' => 'Anglais', 'coef' => 2],
            ['nom' => 'Sciences Physiques', 'coef' => 3],
            ['nom' => 'SVT', 'coef' => 2],
            ['nom' => 'Histoire-Géographie', 'coef' => 2],
            ['nom' => 'EPS', 'coef' => 1],
        ];

        foreach ($classes as $classe) {
            foreach ($matieres as $matiere) {
                Matiere::create([
                    'nom_matiere' => $matiere['nom'],
                    'coefficient' => $matiere['coef'],
                    'classe_id' => $classe->id,
                ]);
            }
        }
    }
}
