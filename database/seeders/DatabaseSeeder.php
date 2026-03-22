<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        // ── Matières (catalogue global) ───────────────────────
        $math  = Matiere::create(['nom_matiere' => 'Mathématiques']);
        $fran  = Matiere::create(['nom_matiere' => 'Français']);
        $phys  = Matiere::create(['nom_matiere' => 'Sciences Physiques']);
        $angl  = Matiere::create(['nom_matiere' => 'Anglais']);
        $hist  = Matiere::create(['nom_matiere' => 'Histoire-Géo']);

        // ── Liaisons classe × matière × année avec coefficients ──
        DB::table('classe_matiere')->insert([
            // 6ème A
            ['classe_id' => $classe6A->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $hist->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            // 5ème B (même matière Maths, coefficient différent)
            ['classe_id' => $classe5B->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $angl->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

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
