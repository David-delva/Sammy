<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $annee = AnneeAcademique::where('active', true)->first();

        if (!$annee) {
            return;
        }

        // Récupérer tous les élèves inscrits cette année
        $inscriptions = Inscription::with('eleve')->where('annee_academique_id', $annee->id)->get();

        foreach ($inscriptions as $inscription) {
            $eleve = $inscription->eleve;
            $matieres = Matiere::where('classe_id', $inscription->classe_id)->get();

            foreach ($matieres as $matiere) {
                // 2 devoirs par matière
                for ($i = 0; $i < 2; $i++) {
                    Note::create([
                        'eleve_id'            => $eleve->id,
                        'matiere_id'          => $matiere->id,
                        'annee_academique_id' => $annee->id,
                        'note'                => rand(800, 2000) / 100, // 8.00 → 20.00
                        'type_devoir'         => 'devoir',
                    ]);
                }

                // 1 composition par matière
                Note::create([
                    'eleve_id'            => $eleve->id,
                    'matiere_id'          => $matiere->id,
                    'annee_academique_id' => $annee->id,
                    'note'                => rand(800, 2000) / 100,
                    'type_devoir'         => 'composition',
                ]);
            }
        }
    }
}
