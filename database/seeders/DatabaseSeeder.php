<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Utilisateurs ──────────────────────────────────────
        User::create([
            'name'     => 'Admin Système',
            'email'    => 'admin@ecole.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Service Secrétariat',
            'email'    => 'secretariat@ecole.com',
            'password' => Hash::make('password'),
            'role'     => 'secretariat',
        ]);

        // ── Année académique ───────────────────────────────────
        $annee = AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active'  => true,
        ]);

        // ── Classes ───────────────────────────────────────────
        $classe6A = Classe::create(['nom_classe' => '6ème A']);
        $classe5B = Classe::create(['nom_classe' => '5ème B']);

        // ── Matières avec coefficients ────────────────────────
        // 6ème A
        $matieres6A = [
            ['nom_matiere' => 'Mathématiques',     'coefficient' => 4, 'classe_id' => $classe6A->id],
            ['nom_matiere' => 'Français',           'coefficient' => 3, 'classe_id' => $classe6A->id],
            ['nom_matiere' => 'Sciences Physiques', 'coefficient' => 2, 'classe_id' => $classe6A->id],
            ['nom_matiere' => 'Histoire-Géo',       'coefficient' => 2, 'classe_id' => $classe6A->id],
        ];

        // 5ème B
        $matieres5B = [
            ['nom_matiere' => 'Mathématiques',     'coefficient' => 5, 'classe_id' => $classe5B->id],
            ['nom_matiere' => 'Français',           'coefficient' => 4, 'classe_id' => $classe5B->id],
            ['nom_matiere' => 'Sciences Physiques', 'coefficient' => 3, 'classe_id' => $classe5B->id],
            ['nom_matiere' => 'Anglais',            'coefficient' => 2, 'classe_id' => $classe5B->id],
        ];

        foreach (array_merge($matieres6A, $matieres5B) as $m) {
            Matiere::create($m);
        }

        // ── Élève de test ──────────────────────────────────────
        $eleve = Eleve::create([
            'matricule'      => 'E-2025-001',
            'nom'            => 'Dupont',
            'prenom'         => 'Jean',
            'date_naissance' => '2010-05-15',
            'sexe'           => 'M',
        ]);

        // ── Inscription ───────────────────────────────────────
        Inscription::create([
            'eleve_id'            => $eleve->id,
            'classe_id'           => $classe6A->id,
            'annee_academique_id' => $annee->id,
        ]);

        // Appeler NoteSeeder pour générer des notes cohérentes
        $this->call(NoteSeeder::class);
    }
}
