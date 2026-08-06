<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Actions\Billing\CreerFacture;
use App\Actions\Billing\EnregistrerPaiementFacture;
use App\Actions\Billing\GenererPdfFacture;
use App\Actions\Billing\SynchroniserStatutFacture;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\StoreFactureRequest;
use App\Http\Requests\Billing\UpdateFactureStatusRequest;
use App\Models\Eleve;
use App\Models\Facture;
use App\Models\Inscription;
use App\Support\AcademicContext\AcademicContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FactureController extends Controller
{
    public function __construct(
        private readonly AcademicContext $academicContext,
        private readonly CreerFacture $creerFacture,
        private readonly EnregistrerPaiementFacture $enregistrerPaiementFacture,
        private readonly GenererPdfFacture $genererPdfFacture,
        private readonly SynchroniserStatutFacture $synchroniserStatutFacture,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Facture::class);

        $annee = $this->academicContext->currentYear();
        $search = trim((string) $request->query('search', ''));
        $statut = $request->query('statut');
        $eleveFilter = $request->integer('eleve');
        $selectedEleve = $eleveFilter > 0 ? Eleve::query()->find($eleveFilter) : null;

        $factures = Facture::query()
            ->with([
                'inscription.eleve',
                'inscription.classe',
                'inscription.anneeAcademique',
                'creator',
            ])
            ->withSum('paiements', 'montant')
            ->when($annee, function ($query) use ($annee): void {
                $query->whereHas('inscription', function ($inscriptionQuery) use ($annee): void {
                    $inscriptionQuery->where('annee_academique_id', $annee->id);
                });
            })
            ->when($eleveFilter > 0, function ($query) use ($eleveFilter): void {
                $query->whereHas('inscription', function ($inscriptionQuery) use ($eleveFilter): void {
                    $inscriptionQuery->where('eleve_id', $eleveFilter);
                });
            })
            ->when(
                in_array($statut, array_keys(Facture::statutOptions()), true),
                fn ($query) => $query->where('statut', $statut)
            )
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery->where('numero', 'like', "%{$search}%")
                        ->orWhere('libelle', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('inscription.eleve', function ($eleveQuery) use ($search): void {
                            $eleveQuery->where('nom', 'like', "%{$search}%")
                                ->orWhere('prenom', 'like', "%{$search}%")
                                ->orWhere('matricule', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('date_emission')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('factures.index', compact('factures', 'annee', 'search', 'statut', 'selectedEleve'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Facture::class);

        $annee = $this->academicContext->currentYear();

        if (! $annee) {
            return redirect()
                ->route('factures.index', $this->academicContext->routeParameters())
                ->with('error', 'Aucune annee academique selectionnee.');
        }

        $inscriptions = Inscription::query()
            ->with(['eleve', 'classe'])
            ->where('annee_academique_id', $annee->id)
            ->get()
            ->sortBy(static function (Inscription $inscription): string {
                /** @var Eleve $eleve */
                $eleve = $inscription->eleve;

                return strtolower($eleve->nom.' '.$eleve->prenom);
            })
            ->values();

        $selectedInscription = $inscriptions->firstWhere('id', $request->integer('inscription'));

        if (! $selectedInscription && $request->integer('eleve') > 0) {
            $selectedInscription = $inscriptions->firstWhere('eleve_id', $request->integer('eleve'));
        }

        return view('factures.create', compact('annee', 'inscriptions', 'selectedInscription'));
    }

    public function store(StoreFactureRequest $request): RedirectResponse
    {
        $annee = $this->academicContext->currentYear();

        if (! $annee) {
            return back()->withInput()->with('error', 'Aucune annee academique selectionnee.');
        }

        $inscription = Inscription::query()
            ->with(['eleve', 'classe', 'anneeAcademique'])
            ->findOrFail($request->integer('inscription_id'));

        if ((int) $inscription->annee_academique_id !== (int) $annee->id) {
            return back()
                ->withInput()
                ->withErrors(['inscription_id' => "Cette inscription n'appartient pas a l'annee academique selectionnee."]);
        }

        $facture = $this->creerFacture->handle(
            $request->validated(),
            $annee,
            $inscription,
            $request->user(),
        );

        return redirect()
            ->route('factures.show', $this->academicContext->routeParameters(['facture' => $facture]))
            ->with('success', "Facture {$facture->numero} creee avec succes.");
    }

    public function show(Facture $facture)
    {
        $this->authorize('view', $facture);

        $facture->load([
            'inscription.eleve',
            'inscription.classe',
            'inscription.anneeAcademique',
            'creator',
            'paiements.creator',
        ]);

        return view('factures.show', compact('facture'));
    }

    public function updateStatus(UpdateFactureStatusRequest $request, Facture $facture): RedirectResponse
    {
        $facture->loadMissing([
            'inscription.anneeAcademique',
            'paiements',
        ]);

        if ($redirect = $this->redirectIfSelectionMismatch($facture)) {
            return $redirect;
        }

        $statut = $request->string('statut')->toString();

        try {
            if ($statut === Facture::STATUT_PAYEE) {
                if ($facture->solde_restant > 0) {
                    $this->enregistrerPaiementFacture->handle($facture, [
                        'montant' => $facture->solde_restant,
                        'mode_paiement' => 'regularisation',
                        'reference' => 'STATUT-'.$facture->numero,
                        'date_paiement' => now()->toDateString(),
                        'commentaire' => 'Paiement cree automatiquement lors du passage manuel au statut payee.',
                    ], $request->user());
                } else {
                    $this->synchroniserStatutFacture->handle($facture);
                }
            } elseif ($statut === Facture::STATUT_ANNULEE) {
                if ($facture->has_paiements) {
                    throw ValidationException::withMessages([
                        'statut' => "Supprimez d'abord les paiements enregistres avant d'annuler cette facture.",
                    ]);
                }

                $facture->forceFill([
                    'statut' => Facture::STATUT_ANNULEE,
                    'date_paiement' => null,
                ])->save();
            } else {
                if ($facture->has_paiements) {
                    throw ValidationException::withMessages([
                        'statut' => "Le statut 'emise' ne peut etre retabli que si aucun paiement n'est rattache a la facture.",
                    ]);
                }

                $facture->forceFill([
                    'statut' => Facture::STATUT_EMISE,
                    'date_paiement' => null,
                ])->save();
            }
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()
            ->route('factures.show', $this->academicContext->routeParameters(['facture' => $facture]))
            ->with('success', "Le statut de la facture {$facture->numero} a ete mis a jour.");
    }

    public function pdf(Facture $facture)
    {
        $this->authorize('viewPdf', $facture);

        return $this->genererPdfFacture->handle($facture);
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
