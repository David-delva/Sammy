<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Note;
use App\Services\AcademicPerformanceProjector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfoDemoSeeder extends Seeder
{
    private array $noms = [
        'DIALLO', 'TRAORE', 'KONE', 'CAMARA', 'TOURE', 'SYLLA', 'BAH', 'SOW',
        'BARRY', 'KONATE', 'COULIBALY', 'OUEDRAOGO', 'SANOGO', 'DEMBELE',
        'FOFANA', 'KOUAME', 'ADU', 'MENSAH', 'OKORO', 'NKAMAH', 'MBEMBA',
        'NDONG', 'OBAME', 'MOUSSAVOU', 'NZAMBA', 'ONDO', 'MASSALA',
    ];

    private array $prenoms = [
        'Mamadou', 'Fatoumata', 'Ibrahima', 'Aissatou', 'Ousmane', 'Mariama',
        'Abdoulaye', 'Kadiatou', 'Seydou', 'Aminata', 'Boubacar', 'Ramatou',
        'Moussa', 'Fatou', 'Idrissa', 'Awa', 'Cheikh', 'Mariam', 'Amadou',
        'Salimata', 'Steevy', 'Grace', 'Excellent', 'Prisca', 'Judicaël', 'Ornella',
    ];

    private array $lieux = [
        'Libreville', 'Franceville', 'Mouila', 'Lambarene', 'Port-Gentil',
        'Oyem', 'Koulamoutou', 'Makokou', 'Bitam', 'Moanda', 'Tchibanga', 'Lastoursville',
    ];

    private array $bacs = ['Bac C', 'Bac D', 'Bac A', 'Bac E', 'Bac Technique'];

    private array $lycees = [
        'Lycée National Léon Mba', 'Lycée Technique National Omar Bongo',
        'Lycée d\'État de Libreville', 'Lycée Blaise Pascal', 'Institution Immaculée Conception',
    ];

    public function run(): void
    {
        $annee = AnneeAcademique::where('active', true)->latest('id')->first()
            ?? AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);

        // ─── 3 classes ─────────────────────────────────────────
        $classes = collect(['L1 Informatique', 'L2 Informatique', 'L3 Informatique'])
            ->map(fn (string $nom) => Classe::create(['nom_classe' => $nom]));

        // ─── 5 matières informatique ───────────────────────────
        $matieresDef = [
            ['nom' => 'Algorithmique et Programmation', 'coef' => 4, 'credits' => 6],
            ['nom' => 'Bases de Données', 'coef' => 3, 'credits' => 5],
            ['nom' => 'Réseaux Informatiques', 'coef' => 3, 'credits' => 4],
            ['nom' => 'Systèmes d\'Exploitation', 'coef' => 2, 'credits' => 4],
            ['nom' => 'Génie Logiciel', 'coef' => 2, 'credits' => 3],
        ];

        $matieres = collect($matieresDef)->map(
            fn (array $def) => ['matiere' => Matiere::create(['nom_matiere' => $def['nom']]), 'coef' => $def['coef'], 'credits' => $def['credits']]
        );

        // ─── Affectation matières × classes (mêmes coefficients pour les 3 classes) ─
        $pivotRows = [];
        foreach ($classes as $classe) {
            foreach ($matieres as $m) {
                $pivotRows[] = [
                    'classe_id' => $classe->id,
                    'matiere_id' => $m['matiere']->id,
                    'annee_academique_id' => $annee->id,
                    'coefficient' => $m['coef'],
                    'credits' => $m['credits'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('classe_matiere')->insert($pivotRows);

        // ─── 100 élèves répartis sur les 3 classes ─────────────
        $repartition = [34, 33, 33];
        $matricule = 2026001;
        $eleveIds = [];
        $inscriptionRows = [];

        foreach ($classes->values() as $index => $classe) {
            for ($i = 0; $i < $repartition[$index]; $i++) {
                $eleve = Eleve::create([
                    'matricule' => 'INF-'.$matricule++,
                    'nom' => $this->noms[array_rand($this->noms)],
                    'prenom' => $this->prenoms[array_rand($this->prenoms)],
                    'date_naissance' => now()->subYears(rand(18, 24))->subDays(rand(0, 365))->format('Y-m-d'),
                    'lieu_naissance' => $this->lieux[array_rand($this->lieux)],
                    'sexe' => ['M', 'F'][rand(0, 1)],
                    'bac' => $this->bacs[array_rand($this->bacs)],
                    'provenance' => $this->lycees[array_rand($this->lycees)],
                ]);

                $eleveIds[] = ['id' => $eleve->id, 'classe_id' => $classe->id];

                $inscriptionRows[] = [
                    'eleve_id' => $eleve->id,
                    'classe_id' => $classe->id,
                    'annee_academique_id' => $annee->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('inscriptions')->insert($inscriptionRows);

        // ─── Notes (insert brut, sans observer, pour la performance) ─
        $noteRows = [];
        $now = now();

        foreach ($eleveIds as $entry) {
            foreach ($matieres as $m) {
                foreach ([Note::SEMESTRE_1, Note::SEMESTRE_2] as $semestre) {
                    for ($d = 0; $d < rand(2, 3); $d++) {
                        $noteRows[] = $this->noteRow($entry['id'], $m['matiere']->id, $annee->id, $semestre, 'devoir', $this->genererNoteRealiste(), $now);
                    }

                    $composition = $this->genererNoteRealiste();
                    $noteRows[] = $this->noteRow($entry['id'], $m['matiere']->id, $annee->id, $semestre, 'composition', $composition, $now);

                    // Rattrapage pour environ un tiers des compositions en dessous de 10
                    if ($composition < 10 && rand(1, 3) === 1) {
                        $noteRows[] = $this->noteRow($entry['id'], $m['matiere']->id, $annee->id, $semestre, 'rattrapage', $this->genererNoteRealiste(), $now);
                    }
                }
            }
        }

        foreach (array_chunk($noteRows, 500) as $chunk) {
            DB::table('notes')->insert($chunk);
        }

        // ─── Quelques absences ponctuelles ──────────────────────
        $absenceRows = [];
        foreach (array_slice($eleveIds, 0, 25) as $entry) {
            $matiere = $matieres->random();
            $absenceRows[] = [
                'eleve_id' => $entry['id'],
                'matiere_id' => $matiere['matiere']->id,
                'annee_academique_id' => $annee->id,
                'semestre' => Note::SEMESTRE_1,
                'heures' => rand(2, 12),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('absences')->insert($absenceRows);

        // ─── Un seul passage de recalcul pour toute l'année ────
        app(AcademicPerformanceProjector::class)->rebuildAll($annee->id);

        $this->command?->info('InfoDemoSeeder : 3 classes, 5 matières, '.count($eleveIds).' élèves, '.count($noteRows).' notes, '.count($absenceRows).' absences.');
    }

    private function noteRow(int $eleveId, int $matiereId, int $anneeId, int $semestre, string $type, float $note, $now): array
    {
        return [
            'eleve_id' => $eleveId,
            'matiere_id' => $matiereId,
            'annee_academique_id' => $anneeId,
            'semestre' => $semestre,
            'type_devoir' => $type,
            'note' => $note,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function genererNoteRealiste(): float
    {
        $notesBonnes = [10, 10.5, 11, 11.5, 12, 12.5, 13, 13.5, 14, 14.5, 15, 15.5, 16];
        $notesMoyennes = [8, 8.5, 9, 9.5, 16.5, 17, 17.5];
        $notesFaibles = [5, 5.5, 6, 6.5, 7, 7.5];
        $notesExcellentes = [18, 18.5, 19, 19.5, 20];

        $rand = rand(1, 100);

        return match (true) {
            $rand <= 50 => $notesBonnes[array_rand($notesBonnes)],
            $rand <= 75 => $notesMoyennes[array_rand($notesMoyennes)],
            $rand <= 90 => $notesFaibles[array_rand($notesFaibles)],
            default => $notesExcellentes[array_rand($notesExcellentes)],
        };
    }
}
