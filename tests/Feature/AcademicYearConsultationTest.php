<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AcademicYearConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_current_calendar_year_remains_writable_without_consultation_selection(): void
    {
        Carbon::setTestNow('2026-03-26 10:00:00');

        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $currentYear = AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active' => false,
        ]);

        AnneeAcademique::create([
            'libelle' => '2026-2027',
            'active' => true,
        ]);

        $classe = Classe::create([
            'nom_classe' => '6eme A',
        ]);

        $response = $this->actingAs($user)->post(route('eleves.store'), [
            'matricule' => 'E-CURRENT-001',
            'nom' => 'Diallo',
            'prenom' => 'Aminata',
            'date_naissance' => '2011-02-15',
            'lieu_naissance' => 'Koulamoutou',
            'sexe' => 'F',
            'classe_id' => $classe->id,
        ]);

        $response->assertRedirect(route('eleves.index'));

        $this->assertDatabaseHas('eleves', [
            'matricule' => 'E-CURRENT-001',
        ]);

        $eleve = Eleve::query()->where('matricule', 'E-CURRENT-001')->firstOrFail();

        $this->assertDatabaseHas('inscriptions', [
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $currentYear->id,
        ]);
    }

    public function test_selected_year_outside_the_current_calendar_year_is_read_only(): void
    {
        Carbon::setTestNow('2026-03-26 10:00:00');

        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        AnneeAcademique::create([
            'libelle' => '2025-2026',
            'active' => false,
        ]);

        $futureYear = AnneeAcademique::create([
            'libelle' => '2026-2027',
            'active' => true,
        ]);

        $classe = Classe::create([
            'nom_classe' => '6eme A',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['academic_year_date' => '2026-09-01'])
            ->from(route('eleves.create'))
            ->post(route('eleves.store'), [
                'matricule' => 'E-READONLY-001',
                'nom' => 'Barry',
                'prenom' => 'Mariam',
                'date_naissance' => '2011-06-10',
                'lieu_naissance' => 'Libreville',
                'sexe' => 'F',
                'classe_id' => $classe->id,
            ]);

        $response->assertRedirect(route('eleves.create'));
        $response->assertSessionHas('error', "Action impossible : vous etes en mode consultation sur une annee differente de l'annee en cours.");

        $this->assertDatabaseMissing('eleves', [
            'matricule' => 'E-READONLY-001',
        ]);
        $this->assertSame('2026-2027', $futureYear->libelle);
    }

    public function test_reset_academic_year_clears_session_and_removes_date_from_redirect(): void
    {
        $user = User::factory()->create([
            'role' => 'secretariat',
        ]);

        $classe = Classe::create([
            'nom_classe' => '6eme A',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['academic_year_date' => '2025-09-01'])
            ->from(route('eleves.index', ['date' => '2025-09-01', 'classe' => $classe->id]))
            ->get(route('academic-year.reset'));

        $response->assertRedirect(route('eleves.index', ['classe' => $classe->id]));
        $response->assertSessionMissing('academic_year_date');
    }
}