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

class NoteIntegrityValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_search_by_subject_does_not_escape_the_selected_academic_year(): void
    {
        Carbon::setTestNow('2026-03-26 10:00:00');

        $user = User::factory()->create(['role' => 'admin']);
        $currentYear = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);
        $historicalYear = AnneeAcademique::create(['libelle' => '2024-2025', 'active' => false]);
        $classe = Classe::create(['nom_classe' => '6eme A']);
        $matiere = Matiere::create(['nom_matiere' => 'Physique']);
        $eleve = $this->createStudent('SRCH-001', 'Barry', 'Mariam', 'F');

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $historicalYear->id,
        ]);

        Note::create([
            'eleve_id' => $eleve->id,
            'matiere_id' => $matiere->id,
            'annee_academique_id' => $historicalYear->id,
            'note' => 14,
            'type_devoir' => 'devoir',
            'semestre' => Note::SEMESTRE_1,
        ]);

        $response = $this->actingAs($user)->get(route('notes.index', ['search' => 'Physique']));

        $response->assertOk();
        $response->assertViewHas('notes', fn ($notes) => $notes->total() === 0);
        $this->assertSame($currentYear->id, currentAcademicYear()?->id);
    }

    public function test_single_note_store_rejects_subject_outside_student_programme(): void
    {
        Carbon::setTestNow('2026-03-26 10:00:00');

        $user = User::factory()->create(['role' => 'admin']);
        $annee = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);
        $classeA = Classe::create(['nom_classe' => '6eme A']);
        $classeB = Classe::create(['nom_classe' => '6eme B']);
        $matiere = Matiere::create(['nom_matiere' => 'Mathematiques']);
        $eleve = $this->createStudent('NOTE-001', 'Diallo', 'Aminata', 'F');

        $classeA->matieres()->attach($matiere->id, [
            'annee_academique_id' => $annee->id,
            'coefficient' => 4,
        ]);

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classeB->id,
            'annee_academique_id' => $annee->id,
        ]);

        $response = $this->actingAs($user)->post(route('notes.store'), [
            'eleve_id' => $eleve->id,
            'matiere_id' => $matiere->id,
            'note' => 15,
            'type_devoir' => 'devoir',
            'semestre' => Note::SEMESTRE_1,
        ]);

        $response->assertSessionHasErrors('matiere_id');
        $this->assertDatabaseCount('notes', 0);
    }

    public function test_note_update_rejects_switching_to_an_incompatible_student_programme(): void
    {
        Carbon::setTestNow('2026-03-26 10:00:00');

        $user = User::factory()->create(['role' => 'admin']);
        $annee = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);
        $classeA = Classe::create(['nom_classe' => '5eme A']);
        $classeB = Classe::create(['nom_classe' => '5eme B']);
        $matiere = Matiere::create(['nom_matiere' => 'Francais']);
        $eleveA = $this->createStudent('UPD-001', 'Bah', 'Moussa', 'M');
        $eleveB = $this->createStudent('UPD-002', 'Sow', 'Aicha', 'F');

        $classeA->matieres()->attach($matiere->id, [
            'annee_academique_id' => $annee->id,
            'coefficient' => 3,
        ]);

        Inscription::create([
            'eleve_id' => $eleveA->id,
            'classe_id' => $classeA->id,
            'annee_academique_id' => $annee->id,
        ]);

        Inscription::create([
            'eleve_id' => $eleveB->id,
            'classe_id' => $classeB->id,
            'annee_academique_id' => $annee->id,
        ]);

        $note = Note::create([
            'eleve_id' => $eleveA->id,
            'matiere_id' => $matiere->id,
            'annee_academique_id' => $annee->id,
            'note' => 12,
            'type_devoir' => 'devoir',
            'semestre' => Note::SEMESTRE_1,
        ]);

        $response = $this->actingAs($user)->put(route('notes.update', $note), [
            'eleve_id' => $eleveB->id,
            'matiere_id' => $matiere->id,
            'note' => 13,
            'type_devoir' => 'devoir',
            'semestre' => Note::SEMESTRE_1,
        ]);

        $response->assertSessionHasErrors('matiere_id');
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'eleve_id' => $eleveA->id,
            'matiere_id' => $matiere->id,
        ]);
    }

    public function test_mass_note_store_rejects_students_outside_the_selected_class(): void
    {
        Carbon::setTestNow('2026-03-26 10:00:00');

        $user = User::factory()->create(['role' => 'admin']);
        $annee = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);
        $classeA = Classe::create(['nom_classe' => '4eme A']);
        $classeB = Classe::create(['nom_classe' => '4eme B']);
        $matiere = Matiere::create(['nom_matiere' => 'SVT']);
        $eleveA = $this->createStudent('MASS-001', 'Barry', 'Mariam', 'F');
        $eleveB = $this->createStudent('MASS-002', 'Diallo', 'Moussa', 'M');

        $classeA->matieres()->attach($matiere->id, [
            'annee_academique_id' => $annee->id,
            'coefficient' => 2,
        ]);

        Inscription::create([
            'eleve_id' => $eleveA->id,
            'classe_id' => $classeA->id,
            'annee_academique_id' => $annee->id,
        ]);

        Inscription::create([
            'eleve_id' => $eleveB->id,
            'classe_id' => $classeB->id,
            'annee_academique_id' => $annee->id,
        ]);

        $response = $this->actingAs($user)
            ->from(route('notes.masse.index', [
                'classe_id' => $classeA->id,
                'matiere_id' => $matiere->id,
                'type_devoir' => 'devoir',
                'semestre' => Note::SEMESTRE_1,
            ]))
            ->post(route('notes.masse.store'), [
                'classe_id' => $classeA->id,
                'matiere_id' => $matiere->id,
                'type_devoir' => 'devoir',
                'semestre' => Note::SEMESTRE_1,
                'notes' => [
                    $eleveA->id => 13,
                    $eleveB->id => 16,
                ],
            ]);

        $response->assertSessionHasErrors('notes');
        $this->assertDatabaseCount('notes', 0);
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
