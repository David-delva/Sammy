<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Inscription;
use App\Models\Note;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $annee = AnneeAcademique::where('active', true)->first();

        if (! $annee) {
            return;
        }

        $inscriptions = Inscription::with('eleve')
            ->where('annee_academique_id', $annee->id)
            ->get();

        foreach ($inscriptions as $inscription) {
            $eleve = $inscription->eleve;
            $classeId = $inscription->classe_id;

            $matieres = DB::table('classe_matiere')
                ->where('classe_id', $classeId)
                ->where('annee_academique_id', $annee->id)
                ->join('matieres', 'classe_matiere.matiere_id', '=', 'matieres.id')
                ->select('matieres.id', 'matieres.nom_matiere')
                ->get();

            foreach ($matieres as $matiere) {
                foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
                    for ($i = 0; $i < 2; $i++) {
                        Note::create([
                            'eleve_id' => $eleve->id,
                            'matiere_id' => $matiere->id,
                            'annee_academique_id' => $annee->id,
                            'note' => rand(800, 2000) / 100,
                            'type_devoir' => 'devoir',
                            'semestre' => $semestre,
                        ]);
                    }

                    Note::create([
                        'eleve_id' => $eleve->id,
                        'matiere_id' => $matiere->id,
                        'annee_academique_id' => $annee->id,
                        'note' => rand(800, 2000) / 100,
                        'type_devoir' => 'composition',
                        'semestre' => $semestre,
                    ]);
                }
            }
        }
    }
}