<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Actions\Billing\EnregistrerPaiementFacture;
use App\Actions\Billing\SupprimerPaiementFacture;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\StorePaiementFactureRequest;
use App\Models\Billing\PaiementFacture;
use App\Models\Facture;
use App\Support\AcademicContext\AcademicContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class PaiementFactureController extends Controller
{
    public function __construct(
        private readonly AcademicContext $academicContext,
        private readonly EnregistrerPaiementFacture $enregistrerPaiementFacture,
        private readonly SupprimerPaiementFacture $supprimerPaiementFacture,
    ) {}

    public function store(StorePaiementFactureRequest $request, Facture $facture): RedirectResponse
    {
        $facture->loadMissing(['inscription.anneeAcademique', 'paiements']);

        if ($redirect = $this->redirectIfSelectionMismatch($facture)) {
            return $redirect;
        }

        try {
            $this->enregistrerPaiementFacture->handle(
                $facture,
                $request->validated(),
                $request->user(),
            );
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()
            ->route('factures.show', $this->academicContext->routeParameters(['facture' => $facture]))
            ->with('success', "Le paiement de la facture {$facture->numero} a ete enregistre.");
    }

    public function destroy(Facture $facture, PaiementFacture $paiement): RedirectResponse
    {
        $this->authorize('deletePayment', $facture);
        $facture->loadMissing(['inscription.anneeAcademique', 'paiements']);

        if ($redirect = $this->redirectIfSelectionMismatch($facture)) {
            return $redirect;
        }

        try {
            $this->supprimerPaiementFacture->handle($facture, $paiement);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()
            ->route('factures.show', $this->academicContext->routeParameters(['facture' => $facture]))
            ->with('success', 'Le paiement a ete supprime et le statut de la facture a ete resynchronise.');
    }

    protected function redirectIfSelectionMismatch(Facture $facture): ?RedirectResponse
    {
        if (! $this->academicContext->selectedYearMatchesFacture($facture)) {
            return redirect()
                ->route('factures.index', $this->academicContext->routeParameters())
                ->with('error', $this->academicContext->mismatchMessageForFacture($facture));
        }

        return null;
    }
}
