<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création de l'Administrateur par défaut
        User::factory()->create([
            'name' => 'Admin Système',
            'email' => 'admin@ecole.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Création du Secrétariat par défaut
        User::factory()->create([
            'name' => 'Service Secrétariat',
            'email' => 'secretariat@ecole.com',
            'password' => Hash::make('password'),
            'role' => 'secretariat',
        ]);

        // Créer une année académique par défaut
        $annee = \App\Models\AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active' => true
        ]);

        // Créer des classes de test
        $classe1 = \App\Models\Classe::create(['nom_classe' => '6ème A']);
        $classe2 = \App\Models\Classe::create(['nom_classe' => '5ème B']);

        // Créer des matières de test
        $matMath = \App\Models\Matiere::create(['nom_matiere' => 'Mathématiques']);
        $matFran = \App\Models\Matiere::create(['nom_matiere' => 'Français']);

        // Lier les matières aux classes pour l'année active avec un coefficient
        $classe1->matieres()->attach($matMath->id, ['annee_academique_id' => $annee->id, 'coefficient' => 4]);
        $classe1->matieres()->attach($matFran->id, ['annee_academique_id' => $annee->id, 'coefficient' => 3]);
        
        $classe2->matieres()->attach($matMath->id, ['annee_academique_id' => $annee->id, 'coefficient' => 5]);
        $classe2->matieres()->attach($matFran->id, ['annee_academique_id' => $annee->id, 'coefficient' => 4]);

        // Créer un élève de test
        $eleve = \App\Models\Eleve::create([
            'matricule' => 'E-2025-001',
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'date_naissance' => '2010-05-15',
            'sexe' => 'M'
        ]);

        // L'inscrire dans la classe 1
        \App\Models\Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classe1->id,
            'annee_academique_id' => $annee->id
        ]);
    }
}
