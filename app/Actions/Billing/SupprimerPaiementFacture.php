<?php

declare(strict_types=1);

namespace App\Actions\Billing;

use App\Models\Billing\PaiementFacture;
use App\Models\Facture;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class SupprimerPaiementFacture
{
    public function __construct(
        private readonly SynchroniserStatutFacture $synchroniserStatutFacture,
    ) {}

    public function handle(Facture $facture, PaiementFacture $paiement): void
    {
        if ((int) $paiement->facture_id !== (int) $facture->id) {
            throw ValidationException::withMessages([
                'paiement' => "Le paiement selectionne n'appartient pas a cette facture.",
            ]);
        }

        DB::transaction(function () use ($facture, $paiement): void {
            $paiement->delete();
            $this->synchroniserStatutFacture->handle($facture);
        });
    }
}
