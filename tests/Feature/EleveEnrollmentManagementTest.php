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
use Tests\TestCase;

class EleveEnrollmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_changes_student_profile_and_selected_year_class_without_creating_extra_enrollment(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = $this->createCurrentAcademicYear();
        $classeInitiale = Classe::create(['nom_classe' => '6eme A']);
        $classeFinale = Classe::create(['nom_classe' => '6eme B']);
        $eleve = $this->createStudent('UPD-ELEVE-001', 'Diallo', 'Aminata', 'F');

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classeInitiale->id,
            'annee_academique_id' => $annee->id,
        ]);

        $response = $this->actingAs($user)->put(route('eleves.update', $eleve), [
            'matricule' => 'UPD-ELEVE-001',
            'nom' => 'Diallo',
            'prenom' => 'Awa',
            'date_naissance' => '2011-02-15',
            'lieu_naissance' => 'Franceville',
            'sexe' => 'F',
            'classe_id' => $classeFinale->id,
        ]);

        $response->assertRedirect(route('eleves.index'));

        $this->assertDatabaseHas('eleves', [
            'id' => $eleve->id,
            'prenom' => 'Awa',
            'lieu_naissance' => 'Franceville',
        ]);

        $this->assertDatabaseHas('inscriptions', [
            'eleve_id' => $eleve->id,
            'classe_id' => $classeFinale->id,
            'annee_academique_id' => $annee->id,
        ]);

        $this->assertSame(1, Inscription::query()->where('eleve_id', $eleve->id)->count());
    }

    public function test_update_redirects_to_reenrollment_when_student_is_not_enrolled_in_selected_year(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->createCurrentAcademicYear();
        $pastYear = AnneeAcademique::create(['libelle' => '2024-2025', 'active' => false]);
        $classe = Classe::create(['nom_classe' => '5eme A']);
        $eleve = $this->createStudent('UPD-ELEVE-002', 'Bah', 'Moussa', 'M');

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $pastYear->id,
        ]);

        $response = $this->actingAs($user)->put(route('eleves.update', $eleve), [
            'matricule' => 'UPD-ELEVE-002',
            'nom' => 'Bah',
            'prenom' => 'Moussa',
            'date_naissance' => '2011-02-15',
            'lieu_naissance' => 'Libreville',
            'sexe' => 'M',
            'classe_id' => $classe->id,
        ]);

        $response->assertRedirect(route('eleves.reinscriptions.index', ['search' => $eleve->matricule]));
        $response->assertSessionHas('warning');

        $this->assertDatabaseMissing('inscriptions', [
            'eleve_id' => $eleve->id,
            'annee_academique_id' => AnneeAcademique::query()->where('libelle', '2025-2026')->value('id'),
        ]);
    }

    public function test_reenrollment_creates_selected_year_inscription_for_existing_student(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = $this->createCurrentAcademicYear();
        $pastYear = AnneeAcademique::create(['libelle' => '2024-2025', 'active' => false]);
        $ancienneClasse = Classe::create(['nom_classe' => '4eme A']);
        $nouvelleClasse = Classe::create(['nom_classe' => '3eme A']);
        $eleve = $this->createStudent('REINS-001', 'Barry', 'Mariam', 'F');

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $ancienneClasse->id,
            'annee_academique_id' => $pastYear->id,
        ]);

        $response = $this->actingAs($user)->post(route('eleves.reinscriptions.store', $eleve), [
            'classe_id' => $nouvelleClasse->id,
        ]);

        $response->assertRedirect(route('eleves.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inscriptions', [
            'eleve_id' => $eleve->id,
            'classe_id' => $nouvelleClasse->id,
            'annee_academique_id' => $annee->id,
        ]);

        $this->assertSame(2, Inscription::query()->where('eleve_id', $eleve->id)->count());
    }

    public function test_destroy_removes_only_selected_year_inscription_and_preserves_history(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $anneeCourante = $this->createCurrentAcademicYear();
        $anneePassee = AnneeAcademique::create(['libelle' => '2024-2025', 'active' => false]);
        $classeCourante = Classe::create(['nom_classe' => '6eme A']);
        $classePassee = Classe::create(['nom_classe' => '5eme A']);
        $matiere = Matiere::create(['nom_matiere' => 'Histoire']);
        $eleve = $this->createStudent('DEL-ELEVE-001', 'Sow', 'Aicha', 'F');

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classePassee->id,
            'annee_academique_id' => $anneePassee->id,
        ]);

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classeCourante->id,
            'annee_academique_id' => $anneeCourante->id,
        ]);

        Note::create([
            'eleve_id' => $eleve->id,
            'matiere_id' => $matiere->id,
            'annee_academique_id' => $anneePassee->id,
            'note' => 14,
            'type_devoir' => 'devoir',
            'semestre' => Note::SEMESTRE_1,
        ]);

        $response = $this->actingAs($user)->delete(route('eleves.destroy', $eleve));

        $response->assertRedirect(route('eleves.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('inscriptions', [
            'eleve_id' => $eleve->id,
            'annee_academique_id' => $anneeCourante->id,
        ]);

        $this->assertDatabaseHas('inscriptions', [
            'eleve_id' => $eleve->id,
            'annee_academique_id' => $anneePassee->id,
        ]);

        $this->assertDatabaseHas('notes', [
            'eleve_id' => $eleve->id,
            'annee_academique_id' => $anneePassee->id,
            'matiere_id' => $matiere->id,
        ]);

        $this->assertDatabaseHas('eleves', [
            'id' => $eleve->id,
        ]);
    }

    public function test_destroy_refuses_to_remove_selected_year_when_notes_exist(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = $this->createCurrentAcademicYear();
        $classe = Classe::create(['nom_classe' => '6eme C']);
        $matiere = Matiere::create(['nom_matiere' => 'Mathematiques']);
        $eleve = $this->createStudent('DEL-ELEVE-002', 'Nze', 'Clarisse', 'F');

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $annee->id,
        ]);

        Note::create([
            'eleve_id' => $eleve->id,
            'matiere_id' => $matiere->id,
            'annee_academique_id' => $annee->id,
            'note' => 16,
            'type_devoir' => 'composition',
            'semestre' => Note::SEMESTRE_2,
        ]);

        $response = $this->actingAs($user)->delete(route('eleves.destroy', $eleve));

        $response->assertRedirect(route('eleves.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('inscriptions', [
            'eleve_id' => $eleve->id,
            'annee_academique_id' => $annee->id,
        ]);

        $this->assertDatabaseHas('notes', [
            'eleve_id' => $eleve->id,
            'annee_academique_id' => $annee->id,
        ]);
    }

    private function createCurrentAcademicYear(): AnneeAcademique
    {
        return AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active' => true,
        ]);
    }

    private function createStudent(string $matricule, string $nom, string $prenom, string $sexe): Eleve
    {
        return Eleve::create([
            'matricule' => $matricule,
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => '2011-02-15',
            'lieu_naissance' => 'Libreville',
            'sexe' => $sexe,
        ]);
    }
}
