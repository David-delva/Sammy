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
        // ─── Utilisateurs ──────────────────────────────────────
        User::create([
            'name'     => 'Admin Système',
            'email'    => 'admin@ecole.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'     => 'Service Secrétariat',
            'email'    => 'secretariat@ecole.com',
            'password' => Hash::make('password'),
            'role'     => 'secretariat',
            'email_verified_at' => now(),
        ]);

        // ─── Année académique ──────────────────────────────────
        $annee = AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active'  => true,
        ]);

        // ─── Classes ───────────────────────────────────────────
        $classe6A = Classe::create(['nom_classe' => '6ème A']);
        $classe6B = Classe::create(['nom_classe' => '6ème B']);
        $classe5A = Classe::create(['nom_classe' => '5ème A']);
        $classe5B = Classe::create(['nom_classe' => '5ème B']);
        $classe4A = Classe::create(['nom_classe' => '4ème A']);
        $classe3A = Classe::create(['nom_classe' => '3ème A']);

        // ─── Matières (catalogue global) ───────────────────────
        $math  = Matiere::create(['nom_matiere' => 'Mathématiques']);
        $fran  = Matiere::create(['nom_matiere' => 'Français']);
        $phys  = Matiere::create(['nom_matiere' => 'Sciences Physiques']);
        $svt   = Matiere::create(['nom_matiere' => 'SVT']);
        $angl  = Matiere::create(['nom_matiere' => 'Anglais']);
        $hist  = Matiere::create(['nom_matiere' => 'Histoire-Géo']);
        $eps   = Matiere::create(['nom_matiere' => 'EPS']);
        $info  = Matiere::create(['nom_matiere' => 'Informatique']);
        $philo = Matiere::create(['nom_matiere' => 'Philosophie']);

        // ─── Liaisons classe × matière × année avec coefficients ─
        DB::table('classe_matiere')->insert([
            // 6ème A
            ['classe_id' => $classe6A->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $svt->id,   'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $angl->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $hist->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $eps->id,   'annee_academique_id' => $annee->id, 'coefficient' => 1, 'created_at' => now(), 'updated_at' => now()],

            // 6ème B
            ['classe_id' => $classe6B->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6B->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6B->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6B->id, 'matiere_id' => $svt->id,   'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6B->id, 'matiere_id' => $angl->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6B->id, 'matiere_id' => $hist->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6B->id, 'matiere_id' => $eps->id,   'annee_academique_id' => $annee->id, 'coefficient' => 1, 'created_at' => now(), 'updated_at' => now()],

            // 5ème A
            ['classe_id' => $classe5A->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5A->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5A->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5A->id, 'matiere_id' => $svt->id,   'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5A->id, 'matiere_id' => $angl->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5A->id, 'matiere_id' => $hist->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5A->id, 'matiere_id' => $eps->id,   'annee_academique_id' => $annee->id, 'coefficient' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5A->id, 'matiere_id' => $info->id,  'annee_academique_id' => $annee->id, 'coefficient' => 1, 'created_at' => now(), 'updated_at' => now()],

            // 5ème B
            ['classe_id' => $classe5B->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $svt->id,   'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $angl->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $hist->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $eps->id,   'annee_academique_id' => $annee->id, 'coefficient' => 1, 'created_at' => now(), 'updated_at' => now()],

            // 4ème A
            ['classe_id' => $classe4A->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe4A->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe4A->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe4A->id, 'matiere_id' => $svt->id,   'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe4A->id, 'matiere_id' => $angl->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe4A->id, 'matiere_id' => $hist->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe4A->id, 'matiere_id' => $eps->id,   'annee_academique_id' => $annee->id, 'coefficient' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe4A->id, 'matiere_id' => $info->id,  'annee_academique_id' => $annee->id, 'coefficient' => 1, 'created_at' => now(), 'updated_at' => now()],

            // 3ème A
            ['classe_id' => $classe3A->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe3A->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe3A->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe3A->id, 'matiere_id' => $svt->id,   'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe3A->id, 'matiere_id' => $angl->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe3A->id, 'matiere_id' => $hist->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe3A->id, 'matiere_id' => $eps->id,   'annee_academique_id' => $annee->id, 'coefficient' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe3A->id, 'matiere_id' => $philo->id, 'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ─── Élèves ────────────────────────────────────────────
        $this->call(EleveSeeder::class);

        // ─── Notes ─────────────────────────────────────────────
        $this->call(NoteSeeder::class);
    }
}
