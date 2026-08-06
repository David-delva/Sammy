<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Billing\PaiementFacture;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Facture;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FactureManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_can_create_an_invoice_for_a_current_year_enrollment(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $annee = $this->createCurrentAcademicYear();
        $classe = Classe::create(['nom_classe' => '6eme A']);
        $eleve = $this->createStudent('FAC-001', 'Diallo', 'Aminata', 'F');
        $inscription = Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $annee->id,
        ]);

        $response = $this->actingAs($user)->post(route('factures.store'), [
            'inscription_id' => $inscription->id,
            'libelle' => "Frais d'inscription annuelle",
            'description' => 'Inscription et dossier administratif',
            'montant' => 35000,
            'date_emission' => '2026-04-02',
            'date_echeance' => '2026-04-10',
        ]);

        $facture = Facture::query()->first();

        $response->assertRedirect(route('factures.show', $facture));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('factures', [
            'id' => $facture?->id,
            'inscription_id' => $inscription->id,
            'created_by' => $user->id,
            'libelle' => "Frais d'inscription annuelle",
            'statut' => Facture::STATUT_EMISE,
        ]);
    }

    public function test_invoice_status_can_be_marked_as_paid(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $facture = $this->createInvoice();

        $response = $this->actingAs($user)->patch(route('factures.status.update', $facture), [
            'statut' => Facture::STATUT_PAYEE,
        ]);

        $response->assertRedirect(route('factures.show', $facture));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('factures', [
            'id' => $facture->id,
            'statut' => Facture::STATUT_PAYEE,
        ]);
        $this->assertNotNull($facture->fresh()->date_paiement);
        $this->assertDatabaseHas('paiement_factures', [
            'facture_id' => $facture->id,
            'montant' => 25000,
            'mode_paiement' => PaiementFacture::MODE_REGULARISATION,
        ]);
    }

    public function test_payment_can_be_recorded_on_issued_invoice_and_status_becomes_partially_paid(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $facture = $this->createInvoice();

        $response = $this->actingAs($user)->post(route('factures.paiements.store', $facture), [
            'montant' => 10000,
            'mode_paiement' => PaiementFacture::MODE_ESPECES,
            'reference' => 'REC-100',
            'date_paiement' => '2026-04-03',
            'commentaire' => 'Versement initial',
        ]);

        $response->assertRedirect(route('factures.show', $facture));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('paiement_factures', [
            'facture_id' => $facture->id,
            'montant' => 10000,
            'mode_paiement' => PaiementFacture::MODE_ESPECES,
        ]);
        $this->assertSame(Facture::STATUT_PARTIELLEMENT_PAYEE, $facture->fresh()->statut);
        $this->assertSame(15000.0, $facture->fresh()->solde_restant);
    }

    public function test_invoice_becomes_paid_when_payments_cover_full_amount(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $facture = $this->createInvoice();

        $this->actingAs($user)->post(route('factures.paiements.store', $facture), [
            'montant' => 10000,
            'mode_paiement' => PaiementFacture::MODE_ESPECES,
            'reference' => 'REC-100',
            'date_paiement' => '2026-04-03',
        ])->assertRedirect(route('factures.show', $facture));

        $response = $this->actingAs($user)->post(route('factures.paiements.store', $facture), [
            'montant' => 15000,
            'mode_paiement' => PaiementFacture::MODE_VIREMENT,
            'reference' => 'VIR-250',
            'date_paiement' => '2026-04-04',
        ]);

        $response->assertRedirect(route('factures.show', $facture));
        $this->assertSame(Facture::STATUT_PAYEE, $facture->fresh()->statut);
        $this->assertSame(0.0, $facture->fresh()->solde_restant);
    }

    public function test_payment_cannot_be_recorded_on_cancelled_invoice(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $facture = $this->createInvoice();
        $facture->update(['statut' => Facture::STATUT_ANNULEE]);

        $response = $this->actingAs($user)
            ->from(route('factures.show', $facture))
            ->post(route('factures.paiements.store', $facture), [
                'montant' => 5000,
                'mode_paiement' => PaiementFacture::MODE_ESPECES,
                'reference' => 'REC-ERR',
                'date_paiement' => '2026-04-05',
            ]);

        $response->assertRedirect(route('factures.show', $facture));
        $response->assertSessionHasErrors('montant');
        $this->assertDatabaseMissing('paiement_factures', [
            'facture_id' => $facture->id,
            'reference' => 'REC-ERR',
        ]);
    }

    public function test_payment_can_be_deleted_and_invoice_status_is_resynchronized(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $facture = $this->createInvoice();

        $this->actingAs($user)->post(route('factures.paiements.store', $facture), [
            'montant' => 10000,
            'mode_paiement' => PaiementFacture::MODE_ESPECES,
            'reference' => 'REC-DELETE',
            'date_paiement' => '2026-04-03',
        ])->assertRedirect(route('factures.show', $facture));

        $paiement = PaiementFacture::query()->where('facture_id', $facture->id)->firstOrFail();

        $response = $this->actingAs($user)->delete(route('factures.paiements.destroy', [$facture, $paiement]));

        $response->assertRedirect(route('factures.show', $facture));
        $this->assertDatabaseMissing('paiement_factures', [
            'id' => $paiement->id,
        ]);
        $this->assertSame(Facture::STATUT_EMISE, $facture->fresh()->statut);
        $this->assertSame(25000.0, $facture->fresh()->solde_restant);
    }

    public function test_secretariat_can_record_payment_for_current_year_invoice(): void
    {
        Carbon::setTestNow('2026-03-27 10:00:00');

        $secretariat = User::factory()->create(['role' => 'secretariat']);
        $facture = $this->createInvoice();

        $response = $this->actingAs($secretariat)->post(route('factures.paiements.store', $facture), [
            'montant' => 12000,
            'mode_paiement' => PaiementFacture::MODE_MOBILE_MONEY,
            'reference' => 'MOMO-120',
            'date_paiement' => '2026-03-27',
        ]);

        $response->assertRedirect(route('factures.show', $facture));
        $this->assertDatabaseHas('paiement_factures', [
            'facture_id' => $facture->id,
            'reference' => 'MOMO-120',
        ]);
    }

    public function test_secretariat_cannot_record_payment_for_selected_non_current_year_without_authorization(): void
    {
        Carbon::setTestNow('2026-03-27 10:00:00');

        $secretariat = User::factory()->create(['role' => 'secretariat']);
        $this->createCurrentAcademicYear();
        $pastYear = AnneeAcademique::create([
            'libelle' => '2024-2025',
            'active' => false,
        ]);
        $facture = $this->createInvoice(null, $pastYear, 'FAC-20242025-0001');

        $this->actingAs($secretariat)
            ->withSession(['academic_year_date' => '2024-09-01'])
            ->post(route('factures.paiements.store', $facture), [
                'montant' => 5000,
                'mode_paiement' => PaiementFacture::MODE_ESPECES,
                'reference' => 'DENY-500',
                'date_paiement' => '2026-03-27',
            ])
            ->assertForbidden();
    }

    public function test_payment_write_is_rejected_when_selected_year_does_not_match_invoice_year(): void
    {
        Carbon::setTestNow('2026-03-27 10:00:00');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->createCurrentAcademicYear();
        $pastYear = AnneeAcademique::create([
            'libelle' => '2024-2025',
            'active' => false,
        ]);
        $facture = $this->createInvoice($admin->id, $pastYear, 'FAC-20242025-0002');

        $response = $this->actingAs($admin)
            ->withSession(['academic_year_date' => '2025-09-01'])
            ->post(route('factures.paiements.store', $facture), [
                'montant' => 5000,
                'mode_paiement' => PaiementFacture::MODE_ESPECES,
                'reference' => 'MISMATCH-500',
                'date_paiement' => '2026-03-27',
            ]);

        $response->assertRedirect(route('factures.index', ['date' => '2025-09-01']));
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('paiement_factures', [
            'facture_id' => $facture->id,
            'reference' => 'MISMATCH-500',
        ]);
    }

    public function test_invoice_pdf_can_be_generated(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $facture = $this->createInvoice($user->id);

        $response = $this->actingAs($user)->get(route('factures.pdf', $facture));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_enrollment_removal_is_blocked_when_invoice_exists_for_that_year(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $facture = $this->createInvoice($user->id);
        $eleve = $facture->inscription->eleve;

        $response = $this->actingAs($user)->delete(route('eleves.destroy', $eleve));

        $response->assertRedirect(route('eleves.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('inscriptions', [
            'id' => $facture->inscription_id,
        ]);
        $this->assertDatabaseHas('factures', [
            'id' => $facture->id,
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

    private function createInvoice(?int $createdBy = null, ?AnneeAcademique $annee = null, string $numero = 'FAC-20252026-0001'): Facture
    {
        $annee ??= $this->createCurrentAcademicYear();
        $classe = Classe::create(['nom_classe' => '5eme A']);
        $eleve = $this->createStudent('FAC-LOCK-'.$numero, 'Sow', 'Aicha', 'F');
        $inscription = Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $classe->id,
            'annee_academique_id' => $annee->id,
        ]);

        return Facture::create([
            'inscription_id' => $inscription->id,
            'created_by' => $createdBy,
            'numero' => $numero,
            'libelle' => "Frais d'inscription annuelle",
            'description' => 'Paiement attendu avant validation du dossier.',
            'montant' => 25000,
            'statut' => Facture::STATUT_EMISE,
            'date_emission' => '2026-04-02',
            'date_echeance' => '2026-04-15',
            'date_paiement' => null,
        ]);
    }
}
