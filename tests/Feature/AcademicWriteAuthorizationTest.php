<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AcademicWriteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_secretariat_cannot_write_in_selected_non_current_year_without_admin_authorization(): void
    {
        Carbon::setTestNow('2026-03-27 10:00:00');

        $secretariat = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $this->createAcademicYears();
        $classe = Classe::create([
            'nom_classe' => '6eme A',
        ]);

        $this->actingAs($secretariat)
            ->withSession(['academic_year_date' => '2024-09-01'])
            ->get(route('eleves.create'))
            ->assertForbidden();

        $this->actingAs($secretariat)
            ->withSession(['academic_year_date' => '2024-09-01'])
            ->get(route('notes.masse.index'))
            ->assertForbidden();

        $this->actingAs($secretariat)
            ->withSession(['academic_year_date' => '2024-09-01'])
            ->post(route('eleves.store'), [
                'matricule' => 'E-ARCHIVE-001',
                'nom' => 'Barry',
                'prenom' => 'Mariam',
                'date_naissance' => '2011-06-10',
                'lieu_naissance' => 'Libreville',
                'sexe' => 'F',
                'classe_id' => $classe->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('eleves', [
            'matricule' => 'E-ARCHIVE-001',
        ]);
    }

    public function test_admin_can_grant_and_revoke_secretariat_write_access_for_non_current_year(): void
    {
        Carbon::setTestNow('2026-03-27 10:00:00');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $secretariat = User::factory()->create([
            'role' => 'secretariat',
        ]);

        [, $pastYear] = $this->createAcademicYears();

        $this->actingAs($admin)
            ->post(route('annees.write-access.store', $pastYear), [
                'user_id' => $secretariat->id,
            ])
            ->assertRedirect(route('annees.index'));

        $this->assertDatabaseHas('academic_year_user_permissions', [
            'annee_academique_id' => $pastYear->id,
            'user_id' => $secretariat->id,
            'granted_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('annees.write-access.destroy', [$pastYear, $secretariat]))
            ->assertRedirect(route('annees.index'));

        $this->assertDatabaseMissing('academic_year_user_permissions', [
            'annee_academique_id' => $pastYear->id,
            'user_id' => $secretariat->id,
        ]);
    }

    public function test_secretariat_can_write_in_selected_non_current_year_after_admin_authorization(): void
    {
        Carbon::setTestNow('2026-03-27 10:00:00');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $secretariat = User::factory()->create([
            'role' => 'secretariat',
        ]);

        [, $pastYear] = $this->createAcademicYears();
        $classe = Classe::create([
            'nom_classe' => '5eme B',
        ]);

        $this->actingAs($admin)
            ->post(route('annees.write-access.store', $pastYear), [
                'user_id' => $secretariat->id,
            ])
            ->assertRedirect(route('annees.index'));

        $this->actingAs($secretariat)
            ->withSession(['academic_year_date' => '2024-09-01'])
            ->get(route('notes.masse.index'))
            ->assertOk();

        $response = $this->actingAs($secretariat)
            ->withSession(['academic_year_date' => '2024-09-01'])
            ->post(route('eleves.store'), [
                'matricule' => 'E-GRANTED-001',
                'nom' => 'Nze',
                'prenom' => 'Clarisse',
                'date_naissance' => '2011-08-11',
                'lieu_naissance' => 'Franceville',
                'sexe' => 'F',
                'classe_id' => $classe->id,
            ]);

        $response->assertRedirect(route('eleves.index'));

        $this->assertDatabaseHas('eleves', [
            'matricule' => 'E-GRANTED-001',
        ]);

        $eleve = Eleve::query()->where('matricule', 'E-GRANTED-001')->firstOrFail();

        $this->assertDatabaseHas('inscriptions', [
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $pastYear->id,
        ]);
    }

    /**
     * @return array{0: AnneeAcademique, 1: AnneeAcademique}
     */
    private function createAcademicYears(): array
    {
        $currentYear = AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active' => true,
        ]);

        $pastYear = AnneeAcademique::create([
            'libelle' => '2024-2025',
            'active' => false,
        ]);

        return [$currentYear, $pastYear];
    }
}
