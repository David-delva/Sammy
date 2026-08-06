<?php

declare(strict_types=1);

namespace App\Actions\Billing;

use App\Models\Facture;
use App\Support\Pdf\PdfRenderer;
use Illuminate\Http\Response;

final class GenererPdfFacture
{
    public function __construct(
        private readonly PdfRenderer $pdf,
    ) {}

    public function handle(Facture $facture): Response
    {
        $facture->loadMissing([
            'inscription.eleve',
            'inscription.classe',
            'inscription.anneeAcademique',
            'creator',
            'paiements.creator',
        ]);

        return $this->pdf->stream(
            'factures.pdf',
            ['facture' => $facture],
            "facture_{$facture->numero}.pdf",
        );
    }
}
