<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AcademicContextViewTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_stats_follow_the_selected_academic_year(): void
    {
        Carbon::setTestNow('2026-03-26 10:00:00');

        $user = User::factory()->create(['role' => 'secretariat']);
        $historicalYear = AnneeAcademique::create(['libelle' => '2024-2025', 'active' => false]);
        $currentYear = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);

        $historicalClass = Classe::create(['nom_classe' => '3eme A']);
        $historicalSubject = Matiere::create(['nom_matiere' => 'Histoire']);
        $historicalStudent = $this->createStudent('CTX-H-001', 'Barry', 'Mariam', 'F');
        $historicalClass->matieres()->attach($historicalSubject->id, [
            'annee_academique_id' => $historicalYear->id,
            'coefficient' => 2,
        ]);
        Inscription::create([
            'eleve_id' => $historicalStudent->id,
            'classe_id' => $historicalClass->id,
            'annee_academique_id' => $historicalYear->id,
        ]);
        Note::create([
            'eleve_id' => $historicalStudent->id,
            'matiere_id' => $historicalSubject->id,
            'annee_academique_id' => $historicalYear->id,
            'note' => 12,
            'type_devoir' => 'devoir',
            'semestre' => Note::SEMESTRE_1,
        ]);
        Note::create([
            'eleve_id' => $historicalStudent->id,
            'matiere_id' => $historicalSubject->id,
            'annee_academique_id' => $historicalYear->id,
            'note' => 16,
            'type_devoir' => 'composition',
            'semestre' => Note::SEMESTRE_1,
        ]);

        $currentClass = Classe::create(['nom_classe' => '2nde A']);
        $currentSubject = Matiere::create(['nom_matiere' => 'Maths']);
        $currentStudent = $this->createStudent('CTX-C-001', 'Diallo', 'Aminata', 'F');
        $currentClass->matieres()->attach($currentSubject->id, [
            'annee_academique_id' => $currentYear->id,
            'coefficient' => 4,
        ]);
        Inscription::create([
            'eleve_id' => $currentStudent->id,
            'classe_id' => $currentClass->id,
            'annee_academique_id' => $currentYear->id,
        ]);
        Note::create([
            'eleve_id' => $currentStudent->id,
            'matiere_id' => $currentSubject->id,
            'annee_academique_id' => $currentYear->id,
            'note' => 18,
            'type_devoir' => 'devoir',
            'semestre' => Note::SEMESTRE_1,
        ]);
        Note::create([
            'eleve_id' => $currentStudent->id,
            'matiere_id' => $currentSubject->id,
            'annee_academique_id' => $currentYear->id,
            'note' => 18,
            'type_devoir' => 'composition',
            'semestre' => Note::SEMESTRE_1,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['date' => '2024-10-01']));

        $response->assertOk();
        $response->assertViewHas('stats', function (array $stats) {
            return $stats['total_eleves'] === 1
                && $stats['total_classes'] === 1
                && $stats['total_matieres'] === 1
                && $stats['total_notes'] === 2
                && $stats['moyenne_generale'] === 14.0;
        });
    }

    public function test_class_show_only_exposes_students_and_subjects_for_the_selected_year(): void
    {
        Carbon::setTestNow('2026-03-26 10:00:00');

        $user = User::factory()->create(['role' => 'secretariat']);
        $historicalYear = AnneeAcademique::create(['libelle' => '2024-2025', 'active' => false]);
        $currentYear = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);

        $classe = Classe::create(['nom_classe' => '6eme A']);
        $historicalSubject = Matiere::create(['nom_matiere' => 'Geographie']);
        $currentSubject = Matiere::create(['nom_matiere' => 'Sciences']);
        $historicalStudent = $this->createStudent('CLS-H-001', 'Bah', 'Moussa', 'M');
        $currentStudent = $this->createStudent('CLS-C-001', 'Sow', 'Aicha', 'F');

        $classe->matieres()->attach($historicalSubject->id, [
            'annee_academique_id' => $historicalYear->id,
            'coefficient' => 2,
        ]);
        $classe->matieres()->attach($currentSubject->id, [
            'annee_academique_id' => $currentYear->id,
            'coefficient' => 3,
        ]);

        Inscription::create([
            'eleve_id' => $historicalStudent->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $historicalYear->id,
        ]);
        Inscription::create([
            'eleve_id' => $currentStudent->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $currentYear->id,
        ]);

        $response = $this->actingAs($user)->get(route('classes.show', ['classe' => $classe, 'date' => '2024-10-01']));

        $response->assertOk();
        $response->assertViewHas('classe', function (Classe $viewClasse) use ($historicalStudent, $historicalSubject) {
            return $viewClasse->eleves->modelKeys() === [$historicalStudent->id]
                && $viewClasse->matieres->modelKeys() === [$historicalSubject->id];
        });
    }

    private function createStudent(string $matricule, string $nom, string $prenom, string $sexe): Eleve
    {
        return Eleve::create([
            'matricule' => $matricule,
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => '2011-02-15',
            'sexe' => $sexe,
        ]);
    }
}
