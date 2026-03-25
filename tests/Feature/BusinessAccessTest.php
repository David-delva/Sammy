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

    public function test_secretariat_cannot_access_admin_only_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $response = $this->actingAs($user)->get(route('annees.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_only_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get(route('annees.index'));

        $response->assertOk();
    }

    public function test_verified_secretariat_can_create_a_student_with_birthplace(): void
    {
        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $annee = AnneeAcademique::create([
            'libelle' => AnneeAcademique::labelForDate(),
            'active' => true,
        ]);

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
}