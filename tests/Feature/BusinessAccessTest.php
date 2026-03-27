<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_is_redirected_to_email_verification_for_business_routes(): void
    {
        $user = User::factory()->unverified()->create([
            'role' => 'secretariat',
        ]);

        $response = $this->actingAs($user)->get(route('eleves.index'));

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_verified_secretariat_can_access_student_module(): void
    {
        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $response = $this->actingAs($user)->get(route('eleves.index'));

        $response->assertOk();
    }

    public function test_verified_secretariat_can_access_class_management_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $response = $this->actingAs($user)->get(route('classes.index'));

        $response->assertOk();
    }

    public function test_secretariat_can_access_write_screens_in_current_academic_year(): void
    {
        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $this->createCurrentAcademicYear();
        $this->actingAs($user);

        foreach (['eleves.create', 'classes.create', 'matieres.create', 'matieres.assigner', 'notes.create', 'notes.masse.index'] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }

        $this->get(route('annees.index'))->assertForbidden();
    }

    public function test_admin_can_access_admin_only_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->createCurrentAcademicYear();
        $this->actingAs($user);

        foreach (['eleves.create', 'classes.create', 'matieres.create', 'matieres.assigner', 'notes.create', 'notes.masse.index', 'annees.index'] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_secretariat_can_create_a_student_in_current_academic_year(): void
    {
        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $annee = $this->createCurrentAcademicYear();
        $classe = Classe::create([
            'nom_classe' => '6eme A',
        ]);

        $response = $this->actingAs($user)->post(route('eleves.store'), [
            'matricule' => 'E-SECRET-001',
            'nom' => 'Diallo',
            'prenom' => 'Aminata',
            'date_naissance' => '2011-02-15',
            'lieu_naissance' => 'Koulamoutou',
            'sexe' => 'F',
            'classe_id' => $classe->id,
        ]);

        $response->assertRedirect(route('eleves.index'));

        $this->assertDatabaseHas('eleves', [
            'matricule' => 'E-SECRET-001',
            'lieu_naissance' => 'Koulamoutou',
        ]);

        $eleve = Eleve::query()->where('matricule', 'E-SECRET-001')->firstOrFail();

        $this->assertDatabaseHas('inscriptions', [
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $annee->id,
        ]);
    }

    public function test_admin_can_create_a_student_with_birthplace(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $annee = $this->createCurrentAcademicYear();

        $classe = Classe::create([
            'nom_classe' => '6eme A',
        ]);

        $response = $this->actingAs($user)->post(route('eleves.store'), [
            'matricule' => 'E-TEST-001',
            'nom' => 'Diallo',
            'prenom' => 'Aminata',
            'date_naissance' => '2011-02-15',
            'lieu_naissance' => 'Koulamoutou',
            'sexe' => 'F',
            'classe_id' => $classe->id,
        ]);

        $response->assertRedirect(route('eleves.index'));

        $this->assertDatabaseHas('eleves', [
            'matricule' => 'E-TEST-001',
            'lieu_naissance' => 'Koulamoutou',
        ]);

        $eleve = Eleve::query()->where('matricule', 'E-TEST-001')->firstOrFail();

        $this->assertDatabaseHas('inscriptions', [
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $annee->id,
        ]);
    }

    private function createCurrentAcademicYear(): AnneeAcademique
    {
        return AnneeAcademique::create([
            'libelle' => AnneeAcademique::labelForDate(),
            'active' => true,
        ]);
    }
}
