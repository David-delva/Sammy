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
        // â”€â”€ Utilisateurs â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        User::create([
            'name'     => 'Admin SystÃ¨me',
            'email'    => 'admin@ecole.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Service SecrÃ©tariat',
            'email'    => 'secretariat@ecole.com',
            'password' => Hash::make('password'),
            'role'     => 'secretariat',
        ]);

        // â”€â”€ AnnÃ©e acadÃ©mique â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $annee = AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active'  => true,
        ]);

        // â”€â”€ Classes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $classe6A = Classe::create(['nom_classe' => '6Ã¨me A']);
        $classe5B = Classe::create(['nom_classe' => '5Ã¨me B']);

        // â”€â”€ MatiÃ¨res (catalogue global) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $math  = Matiere::create(['nom_matiere' => 'MathÃ©matiques']);
        $fran  = Matiere::create(['nom_matiere' => 'FranÃ§ais']);
        $phys  = Matiere::create(['nom_matiere' => 'Sciences Physiques']);
        $angl  = Matiere::create(['nom_matiere' => 'Anglais']);
        $hist  = Matiere::create(['nom_matiere' => 'Histoire-GÃ©o']);

        // â”€â”€ Liaisons classe Ã— matiÃ¨re Ã— annÃ©e avec coefficients â”€â”€
        DB::table('classe_matiere')->insert([
            // 6Ã¨me A
            ['classe_id' => $classe6A->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe6A->id, 'matiere_id' => $hist->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            // 5Ã¨me B (mÃªme matiÃ¨re Maths, coefficient diffÃ©rent)
            ['classe_id' => $classe5B->id, 'matiere_id' => $math->id,  'annee_academique_id' => $annee->id, 'coefficient' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $fran->id,  'annee_academique_id' => $annee->id, 'coefficient' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $angl->id,  'annee_academique_id' => $annee->id, 'coefficient' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['classe_id' => $classe5B->id, 'matiere_id' => $phys->id,  'annee_academique_id' => $annee->id, 'coefficient' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // â”€â”€ Ã‰lÃ¨ve de test â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $eleve = Eleve::create([
            'matricule'      => 'E-2025-001',
            'nom'            => 'Dupont',
            'prenom'         => 'Jean',
            'date_naissance' => '2010-05-15',
            'sexe'           => 'M',
        ]);

        // â”€â”€ Inscription â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Inscription::create([
            'eleve_id'            => $eleve->id,
            'classe_id'           => $classe6A->id,
            'annee_academique_id' => $annee->id,
        ]);

        // Appeler NoteSeeder pour gÃ©nÃ©rer des notes cohÃ©rentes
        $this->call(NoteSeeder::class);
    }
}
