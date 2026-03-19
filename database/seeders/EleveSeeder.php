<?php

namespace Database\Seeders;

use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Database\Seeder;

class EleveSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classe::all();
        $noms = ['DIALLO', 'TRAORE', 'KONE', 'CAMARA', 'TOURE', 'SYLLA', 'BAH', 'SOW'];
        $prenoms = ['Mamadou', 'Fatoumata', 'Ibrahima', 'Aissatou', 'Ousmane', 'Mariama', 'Abdoulaye', 'Kadiatou'];

        $matricule = 1000;

        foreach ($classes as $classe) {
            for ($i = 0; $i < 5; $i++) {
                Eleve::create([
                    'matricule' => 'EL' . $matricule++,
                    'nom' => $noms[array_rand($noms)],
                    'prenom' => $prenoms[array_rand($prenoms)],
                    'date_naissance' => now()->subYears(rand(12, 16))->format('Y-m-d'),
                    'sexe' => ['M', 'F'][rand(0, 1)],
                    'classe_id' => $classe->id,
                ]);
            }
        }
    }
}
