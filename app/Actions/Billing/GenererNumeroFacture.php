<?php

declare(strict_types=1);

namespace App\Actions\Billing;

use App\Models\AnneeAcademique;
use App\Models\Facture;

final class GenererNumeroFacture
{
    public function handle(AnneeAcademique $annee): string
    {
        $prefix = 'FAC-'.str_replace('-', '', $annee->libelle).'-';
        $lastNumber = Facture::query()
            ->where('numero', 'like', $prefix.'%')
            ->latest('id')
            ->value('numero');

        $sequence = 1;

        if (is_string($lastNumber) && preg_match('/(\d+)$/', $lastNumber, $matches) === 1) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
