<?php

namespace Tests\Feature;

use App\Models\AcademicResult;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicPerformanceProjectionSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_note_write_refreshes_materialized_results(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);
        $classe = Classe::create(['nom_classe' => '6eme A']);
        $matiere = Matiere::create(['nom_matiere' => 'Mathematiques']);
        $classe->matieres()->attach($matiere->id, [
            'annee_academique_id' => $annee->id,
            'coefficient' => 4,
        ]);

        $eleve = Eleve::create([
            'matricule' => 'SYNC-001',
            'nom' => 'Diallo',
            'prenom' => 'Aminata',
            'date_naissance' => '2011-02-15',
            'sexe' => 'F',
        ]);

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $annee->id,
        ]);

        $response = $this->actingAs($user)->post(route('notes.store'), [
            'eleve_id' => $eleve->id,
            'matiere_id' => $matiere->id,
            'note' => 14,
            'type_devoir' => 'devoir',
            'semestre' => 1,
        ]);

        $response->assertRedirect(route('notes.index'));

        $this->assertDatabaseHas('academic_subject_results', [
            'eleve_id' => $eleve->id,
            'annee_academique_id' => $annee->id,
            'matiere_id' => $matiere->id,
            'period' => AcademicResult::PERIOD_SEMESTRE_1,
            'moyenne_matiere' => 14.0,
        ]);

        $this->assertDatabaseHas('academic_results', [
            'eleve_id' => $eleve->id,
            'annee_academique_id' => $annee->id,
            'period' => AcademicResult::PERIOD_SEMESTRE_1,
            'moyenne_generale' => 14.0,
        ]);
    }

    public function test_mass_note_write_refreshes_materialized_results_for_impacted_students(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);
        $classe = Classe::create(['nom_classe' => '6eme A']);
        $matiere = Matiere::create(['nom_matiere' => 'Mathematiques']);
        $classe->matieres()->attach($matiere->id, [
            'annee_academique_id' => $annee->id,
            'coefficient' => 4,
        ]);

        $eleveA = Eleve::create([
            'matricule' => 'SYNC-A',
            'nom' => 'Barry',
            'prenom' => 'Mariam',
            'date_naissance' => '2011-03-10',
            'sexe' => 'F',
        ]);

        $eleveB = Eleve::create([
            'matricule' => 'SYNC-B',
            'nom' => 'Bah',
            'prenom' => 'Moussa',
            'date_naissance' => '2011-05-22',
            'sexe' => 'M',
        ]);

        foreach ([$eleveA, $eleveB] as $eleve) {
            Inscription::create([
                'eleve_id' => $eleve->id,
                'classe_id' => $classe->id,
                'annee_academique_id' => $annee->id,
            ]);
        }

        $response = $this->actingAs($user)
            ->from(route('notes.masse.index', [
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'type_devoir' => 'devoir',
                'semestre' => 1,
            ]))
            ->post(route('notes.masse.store'), [
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'type_devoir' => 'devoir',
                'semestre' => 1,
                'notes' => [
                    $eleveA->id => 13,
                    $eleveB->id => 16,
                ],
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('academic_results', [
            'eleve_id' => $eleveA->id,
            'annee_academique_id' => $annee->id,
            'period' => AcademicResult::PERIOD_SEMESTRE_1,
            'moyenne_generale' => 13.0,
        ]);

        $this->assertDatabaseHas('academic_results', [
            'eleve_id' => $eleveB->id,
            'annee_academique_id' => $annee->id,
            'period' => AcademicResult::PERIOD_SEMESTRE_1,
            'moyenne_generale' => 16.0,
        ]);
    }
}
