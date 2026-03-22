<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use Illuminate\Database\Seeder;

class EleveSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classe::all();
        $annee = AnneeAcademique::query()->where('active', true)->latest('id')->first()
            ?? AnneeAcademique::query()->latest('id')->first();

        if ($classes->isEmpty() || ! $annee) {
            return;
        }

        $noms = ['DIALLO', 'TRAORE', 'KONE', 'CAMARA', 'TOURE', 'SYLLA', 'BAH', 'SOW'];
        $prenoms = ['Mamadou', 'Fatoumata', 'Ibrahima', 'Aissatou', 'Ousmane', 'Mariama', 'Abdoulaye', 'Kadiatou'];
        $lieux = ['Libreville', 'Franceville', 'Mouila', 'Lambarene', 'Port-Gentil', 'Oyem', 'Koulamoutou', 'Makokou'];

        $matricule = 1000;

        foreach ($classes as $classe) {
            for ($i = 0; $i < 5; $i++) {
                $eleve = Eleve::create([
                    'matricule' => 'EL' . $matricule++,
                    'nom' => $noms[array_rand($noms)],
                    'prenom' => $prenoms[array_rand($prenoms)],
                    'date_naissance' => now()->subYears(rand(12, 16))->format('Y-m-d'),
                    'lieu_naissance' => $lieux[array_rand($lieux)],
                    'sexe' => ['M', 'F'][rand(0, 1)],
                ]);

                Inscription::create([
                    'eleve_id' => $eleve->id,
                    'classe_id' => $classe->id,
                    'annee_academique_id' => $annee->id,
                ]);
            }
        }
    }
}