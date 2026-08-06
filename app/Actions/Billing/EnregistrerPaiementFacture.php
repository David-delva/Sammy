<?php

declare(strict_types=1);

namespace App\Actions\Billing;

use App\Models\Billing\PaiementFacture;
use App\Models\Facture;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class EnregistrerPaiementFacture
{
    public function __construct(
        private readonly SynchroniserStatutFacture $synchroniserStatutFacture,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(Facture $facture, array $validated, ?User $acteur): PaiementFacture
    {
        if ($facture->statut === Facture::STATUT_ANNULEE) {
            throw ValidationException::withMessages([
                'montant' => "Impossible d'enregistrer un paiement sur une facture annulee.",
            ]);
        }

        return DB::transaction(function () use ($facture, $validated, $acteur): PaiementFacture {
            $facture->refresh();

            $montant = round((float) $validated['montant'], 2);
            $soldeRestant = $facture->solde_restant;

            if ($montant > $soldeRestant) {
                throw ValidationException::withMessages([
                    'montant' => 'Le montant saisi depasse le solde restant de '.number_format($soldeRestant, 2, ',', ' ').' FCFA.',
                ]);
            }

            $paiement = PaiementFacture::create([
                'facture_id' => $facture->id,
                'created_by' => $acteur?->id,
                'montant' => $montant,
                'mode_paiement' => (string) $validated['mode_paiement'],
                'reference' => filled($validated['reference'] ?? null) ? (string) $validated['reference'] : null,
                'date_paiement' => $validated['date_paiement'],
                'commentaire' => filled($validated['commentaire'] ?? null) ? (string) $validated['commentaire'] : null,
            ]);

            $this->synchroniserStatutFacture->handle($facture);

            return $paiement;
        });
    }
}
