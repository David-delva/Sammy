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

        $noms = [
            'DIALLO', 'TRAORE', 'KONE', 'CAMARA', 'TOURE', 'SYLLA',
            'BAH', 'SOW', 'BARRY', 'DIALLO', 'KONATE', 'COULIBALY',
            'OUEDRAOGO', 'SANOGO', 'COULIBALY', 'DEMBELE', 'FOFANA',
            'KOUAME', 'ADU', 'MENSAH', 'OKORO', 'NKAMAH', 'MBEMBA',
        ];

        $prenoms = [
            'Mamadou', 'Fatoumata', 'Ibrahima', 'Aissatou', 'Ousmane',
            'Mariama', 'Abdoulaye', 'Kadiatou', 'Seydou', 'Aminata',
            'Boubacar', 'Ramatou', 'Moussa', 'Fatou', 'Idrissa',
            'Awa', 'Cheikh', 'Mariam', 'Amadou', 'Salimata',
        ];

        $lieux = [
            'Libreville', 'Franceville', 'Mouila', 'Lambarene',
            'Port-Gentil', 'Oyem', 'Koulamoutou', 'Makokou',
            'Bitam', 'Moanda', 'Tchibanga', 'Lastoursville',
        ];

        $matricule = 2025001;

        foreach ($classes as $classe) {
            for ($i = 0; $i < 8; $i++) {
                $nom = $noms[array_rand($noms)];
                $prenom = $prenoms[array_rand($prenoms)];
                $sexe = ['M', 'F'][rand(0, 1)];

                $eleve = Eleve::create([
                    'matricule' => 'E-'.$matricule++,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'date_naissance' => now()->subYears(rand(10, 18))->format('Y-m-d'),
                    'lieu_naissance' => $lieux[array_rand($lieux)],
                    'sexe' => $sexe,
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
