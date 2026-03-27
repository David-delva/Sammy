<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearDeletionProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_year_cannot_be_deleted_even_when_empty(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => true]);

        $response = $this->actingAs($user)->delete(route('annees.destroy', $annee));

        $response->assertRedirect(route('annees.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('annee_academiques', ['id' => $annee->id]);
    }

    public function test_year_with_linked_school_data_cannot_be_deleted(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = AnneeAcademique::create(['libelle' => '2025-2026', 'active' => false]);
        $classe = Classe::create(['nom_classe' => '6eme A']);
        $eleve = Eleve::create([
            'matricule' => 'DEL-001',
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

        $response = $this->actingAs($user)->delete(route('annees.destroy', $annee));

        $response->assertRedirect(route('annees.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('annee_academiques', ['id' => $annee->id]);
    }

    public function test_empty_inactive_year_can_still_be_deleted(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = AnneeAcademique::create(['libelle' => '2024-2025', 'active' => false]);

        $response = $this->actingAs($user)->delete(route('annees.destroy', $annee));

        $response->assertRedirect(route('annees.index'));
        $this->assertDatabaseMissing('annee_academiques', ['id' => $annee->id]);
    }
}
