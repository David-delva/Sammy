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
                    // 2-3 devoirs par matière et semestre
                    $nbDevoirs = rand(2, 3);
                    for ($i = 0; $i < $nbDevoirs; $i++) {
                        Note::create([
                            'eleve_id' => $eleve->id,
                            'matiere_id' => $matiere->id,
                            'annee_academique_id' => $annee->id,
                            'note' => $this->genererNoteRealiste(),
                            'type_devoir' => 'devoir',
                            'semestre' => $semestre,
                        ]);
                    }

                    // 1 composition par matière et semestre
                    Note::create([
                        'eleve_id' => $eleve->id,
                        'matiere_id' => $matiere->id,
                        'annee_academique_id' => $annee->id,
                        'note' => $this->genererNoteRealiste(),
                        'type_devoir' => 'composition',
                        'semestre' => $semestre,
                    ]);
                }
            }
        }
    }

    private function genererNoteRealiste(): float
    {
        // Notes pondérées : plus de notes entre 10 et 16
        $notesBonnes = [10, 10.5, 11, 11.5, 12, 12.5, 13, 13.5, 14, 14.5, 15, 15.5, 16];
        $notesMoyennes = [8, 8.5, 9, 9.5, 16.5, 17, 17.5];
        $notesFaibles = [5, 5.5, 6, 6.5, 7, 7.5];
        $notesExcellent = [18, 18.5, 19, 19.5, 20];

        $rand = rand(1, 100);

        if ($rand <= 50) {
            // 50% de notes entre 10 et 16
            return $notesBonnes[array_rand($notesBonnes)];
        } elseif ($rand <= 75) {
            // 25% de notes moyennes
            return $notesMoyennes[array_rand($notesMoyennes)];
        } elseif ($rand <= 90) {
            // 15% de notes faibles
            return $notesFaibles[array_rand($notesFaibles)];
        } else {
            // 10% d'excellentes notes
            return $notesExcellent[array_rand($notesExcellent)];
        }
    }
}
