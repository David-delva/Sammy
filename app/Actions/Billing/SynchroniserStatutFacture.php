<?php

declare(strict_types=1);

namespace App\Actions\Billing;

use App\Models\Billing\PaiementFacture;
use App\Models\Facture;

final class SynchroniserStatutFacture
{
    public function handle(Facture $facture): Facture
    {
        $facture = $facture->fresh(['paiements']) ?? $facture->load('paiements');

        $montantPaye = $facture->montant_paye;
        /** @var PaiementFacture|null $dernierPaiement */
        $dernierPaiement = $facture->paiements
            ->sortByDesc('date_paiement')
            ->first();
        $datePaiement = $dernierPaiement?->date_paiement;

        if ($facture->statut === Facture::STATUT_ANNULEE) {
            $facture->forceFill([
                'date_paiement' => $datePaiement,
            ])->save();

            return $facture->fresh(['paiements']) ?? $facture;
        }

        $nouveauStatut = Facture::STATUT_EMISE;

        if ($montantPaye > 0 && $facture->solde_restant > 0) {
            $nouveauStatut = Facture::STATUT_PARTIELLEMENT_PAYEE;
        } elseif ($facture->solde_restant <= 0 && $montantPaye > 0) {
            $nouveauStatut = Facture::STATUT_PAYEE;
        }

        $facture->forceFill([
            'statut' => $nouveauStatut,
            'date_paiement' => $datePaiement,
        ])->save();

        return $facture->fresh(['paiements']) ?? $facture;
    }
}
