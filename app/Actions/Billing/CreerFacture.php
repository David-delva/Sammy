<?php

declare(strict_types=1);

namespace App\Actions\Billing;

use App\Models\AnneeAcademique;
use App\Models\Facture;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreerFacture
{
    public function __construct(
        private readonly GenererNumeroFacture $genererNumeroFacture,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(
        array $validated,
        AnneeAcademique $annee,
        Inscription $inscription,
        ?User $acteur,
    ): Facture {
        return DB::transaction(function () use ($validated, $annee, $inscription, $acteur): Facture {
            return Facture::create([
                'inscription_id' => $inscription->id,
                'created_by' => $acteur?->id,
                'numero' => $this->genererNumeroFacture->handle($annee),
                'libelle' => (string) $validated['libelle'],
                'description' => filled($validated['description'] ?? null) ? (string) $validated['description'] : null,
                'montant' => $validated['montant'],
                'statut' => Facture::STATUT_EMISE,
                'date_emission' => $validated['date_emission'],
                'date_echeance' => $validated['date_echeance'] ?? null,
                'date_paiement' => null,
            ]);
        });
    }
}
