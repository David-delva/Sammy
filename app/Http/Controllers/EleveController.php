<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEleveRequest;
use App\Http\Requests\StoreReinscriptionRequest;
use App\Http\Requests\UpdateEleveRequest;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Facture;
use App\Models\Inscription;
use App\Models\Note;
use App\Services\CalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EleveController extends Controller
{
    public function index()
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();
        $classeFilter = request()->query('classe');

        if ($annee) {
            $eleveIds = Inscription::where('annee_academique_id', $annee->id)
                ->when($classeFilter, function ($query) use ($classeFilter) {
                    return $query->where('classe_id', $classeFilter);
                })
                ->distinct('eleve_id')
                ->pluck('eleve_id');

            $eleves = Eleve::whereIn('id', $eleveIds)
                ->orderBy('nom')
                ->paginate(20);
        } else {
            $eleves = Eleve::orderBy('nom')->paginate(20);
        }

        $eleves->getCollection()->transform(function (Eleve $eleve) use ($date) {
            $eleve->resolved_classe = $eleve->classeForDate($date);

            return $eleve;
        });

        $classes = Classe::orderBy('nom_classe')->get();

        return view('eleves.index', compact('eleves', 'annee', 'classes', 'classeFilter'));
    }

    public function create()
    {
        $annee = currentAcademicYear();
        $classes = Classe::orderBy('nom_classe')->get();

        return view('eleves.create', compact('classes', 'annee'));
    }

    public function reenrollmentIndex(Request $request)
    {
        $annee = currentAcademicYear();
        $search = trim((string) $request->query('search', ''));
        $classes = Classe::orderBy('nom_classe')->get();
        $candidates = null;

        if ($annee) {
            $candidates = Eleve::query()
                ->with('latestInscription.classe')
                ->whereDoesntHave('inscriptions', function ($query) use ($annee) {
                    $query->where('annee_academique_id', $annee->id);
                })
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($searchQuery) use ($search) {
                        $searchQuery->where('matricule', 'like', "%{$search}%")
                            ->orWhere('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%");
                    });
                })
                ->orderBy('nom')
                ->orderBy('prenom')
                ->paginate(20)
                ->withQueryString();
        }

        return view('eleves.reinscriptions', compact('annee', 'classes', 'search', 'candidates'));
    }

    public function store(StoreEleveRequest $request)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return back()
                ->withInput()
                ->withErrors(['general' => "Aucune annee academique selectionnee. Veuillez en creer ou en choisir une d'abord."]);
        }

        DB::transaction(function () use ($request, $annee) {
            $eleve = Eleve::create($request->only([
                'matricule', 'nom', 'prenom', 'date_naissance', 'lieu_naissance', 'sexe', 'bac', 'provenance',
            ]));

            Inscription::create([
                'eleve_id' => $eleve->id,
                'classe_id' => $request->classe_id,
                'annee_academique_id' => $annee->id,
            ]);
        });

        return redirect()
            ->route('eleves.index', $this->indexRouteParameters())
            ->with('success', "Eleve inscrit avec succes pour l'annee {$annee->libelle}.");
    }

    public function show(Eleve $eleve)
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();
        $currentInscription = $annee ? $eleve->inscriptionForAcademicYear($annee) : null;

        $eleve->load([
            'notes' => function ($query) use ($annee) {
                if ($annee) {
                    $query->where('annee_academique_id', $annee->id);
                }

                $query->with('matiere')
                    ->orderBy('semestre')
                    ->orderBy('created_at', 'desc');
            },
        ]);

        $eleve->resolved_classe = $eleve->classeForDate($date);

        $calculationService = app(CalculationService::class);
        $notesCollection = $eleve->notes;

        $notesOverview = [
            'total_notes' => $notesCollection->count(),
            'total_matieres' => $notesCollection->pluck('matiere_id')->unique()->count(),
            'moyenne_annuelle' => $calculationService->calculateMoyenneGenerale($eleve, $annee),
            'moyenne_semestre_1' => $calculationService->calculateMoyenneGenerale($eleve, $annee, Note::SEMESTRE_1),
            'moyenne_semestre_2' => $calculationService->calculateMoyenneGenerale($eleve, $annee, Note::SEMESTRE_2),
        ];

        $notesBySemestre = collect(Note::semestreOptions())
            ->map(function (string $label, int $semestre) use ($notesCollection, $eleve, $annee, $calculationService) {
                $semestreNotes = $notesCollection
                    ->filter(fn (Note $note) => (int) $note->semestre === (int) $semestre)
                    ->values();

                if ($semestreNotes->isEmpty()) {
                    return null;
                }

                $matieres = $semestreNotes
                    ->groupBy('matiere_id')
                    ->map(function ($matiereNotes) use ($eleve, $annee, $calculationService, $semestre) {
                        $notes = $matiereNotes
                            ->sortByDesc(fn (Note $note) => $note->created_at?->timestamp ?? 0)
                            ->values();

                        $matiere = $notes->first()->matiere;
                        $moyenneDevoirs = $notes->where('type_devoir', 'devoir')->avg('note');
                        $noteComposition = $notes->where('type_devoir', 'composition')->max('note');
                        $noteRattrapage = $notes->where('type_devoir', 'rattrapage')->max('note');

                        return [
                            'matiere' => $matiere,
                            'notes' => $notes,
                            'total_notes' => $notes->count(),
                            'moyenne_devoirs' => $moyenneDevoirs !== null ? round($moyenneDevoirs, 2) : null,
                            'note_composition' => $noteComposition !== null ? round($noteComposition, 2) : null,
                            'note_rattrapage' => $noteRattrapage !== null ? round($noteRattrapage, 2) : null,
                            'moyenne_matiere' => $calculationService->calculateMoyenneMatiere($eleve, $matiere, $annee, $semestre),
                            'derniere_saisie' => $notes->first()->created_at,
                        ];
                    })
                    ->sortBy(fn (array $matiereGroup) => strtolower($matiereGroup['matiere']->nom_matiere))
                    ->values();

                return [
                    'semestre' => (int) $semestre,
                    'label' => $label,
                    'total_notes' => $semestreNotes->count(),
                    'total_matieres' => $matieres->count(),
                    'moyenne_generale' => $calculationService->calculateMoyenneGenerale($eleve, $annee, $semestre),
                    'matieres' => $matieres,
                ];
            })
            ->filter()
            ->values();

        return view('eleves.show', compact('eleve', 'annee', 'currentInscription', 'notesOverview', 'notesBySemestre'));
    }

    public function edit(Eleve $eleve)
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();
        $classes = Classe::orderBy('nom_classe')->get();
        $inscription = $annee
            ? $eleve->inscriptionForAcademicYear($annee)
            : $eleve->inscriptionForDate($date);

        if ($annee && ! $inscription) {
            return redirect()
                ->route('eleves.reinscriptions.index', $this->indexRouteParameters(['search' => $eleve->matricule]))
                ->with('warning', "{$eleve->nom} {$eleve->prenom} n'est pas inscrit pour l'annee {$annee->libelle}. Utilisez la reinscription annuelle.");
        }

        return view('eleves.edit', compact('eleve', 'classes', 'inscription', 'annee'));
    }

    public function update(UpdateEleveRequest $request, Eleve $eleve)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return back()
                ->withInput()
                ->withErrors(['general' => 'Aucune annee academique selectionnee.']);
        }

        $inscription = $eleve->inscriptionForAcademicYear($annee);

        if (! $inscription) {
            return redirect()
                ->route('eleves.reinscriptions.index', $this->indexRouteParameters(['search' => $eleve->matricule]))
                ->with('warning', "{$eleve->nom} {$eleve->prenom} n'est pas inscrit pour l'annee {$annee->libelle}. Reinscrivez-le d'abord.");
        }

        DB::transaction(function () use ($request, $eleve, $inscription) {
            $eleve->update($request->only([
                'matricule', 'nom', 'prenom', 'date_naissance', 'lieu_naissance', 'sexe', 'bac', 'provenance',
            ]));

            $inscription->update([
                'classe_id' => $request->classe_id,
            ]);
        });

        return redirect()
            ->route('eleves.index', $this->indexRouteParameters())
            ->with('success', 'Eleve modifie avec succes.');
    }

    public function reenroll(StoreReinscriptionRequest $request, Eleve $eleve)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return back()->with('error', 'Aucune annee academique selectionnee.');
        }

        if ($eleve->inscriptionForAcademicYear($annee)) {
            return redirect()
                ->route('eleves.index', $this->indexRouteParameters())
                ->with('warning', "{$eleve->nom} {$eleve->prenom} est deja inscrit pour l'annee {$annee->libelle}.");
        }

        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $request->integer('classe_id'),
            'annee_academique_id' => $annee->id,
        ]);

        return redirect()
            ->route('eleves.index', $this->indexRouteParameters())
            ->with('success', "{$eleve->nom} {$eleve->prenom} a ete reinscrit pour l'annee {$annee->libelle}.");
    }

    public function destroy(Eleve $eleve)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return redirect()
                ->route('eleves.index', $this->indexRouteParameters())
                ->with('error', 'Aucune annee academique selectionnee.');
        }

        $inscription = $eleve->inscriptionForAcademicYear($annee);

        if (! $inscription) {
            return redirect()
                ->route('eleves.index', $this->indexRouteParameters())
                ->with('warning', "{$eleve->nom} {$eleve->prenom} n'est pas inscrit pour l'annee {$annee->libelle}.");
        }

        $hasNotesForYear = Note::query()
            ->where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $annee->id)
            ->exists();

        if ($hasNotesForYear) {
            return redirect()
                ->route('eleves.index', $this->indexRouteParameters())
                ->with('error', "Impossible de retirer {$eleve->nom} {$eleve->prenom} de l'annee {$annee->libelle} : des notes existent deja pour cette annee.");
        }

        $hasInvoicesForYear = Facture::query()
            ->where('inscription_id', $inscription->id)
            ->exists();

        if ($hasInvoicesForYear) {
            return redirect()
                ->route('eleves.index', $this->indexRouteParameters())
                ->with('error', "Impossible de retirer {$eleve->nom} {$eleve->prenom} de l'annee {$annee->libelle} : des factures d'inscription existent deja pour cette annee.");
        }

        DB::transaction(function () use ($inscription) {
            $inscription->delete();
        });

        return redirect()
            ->route('eleves.index', $this->indexRouteParameters())
            ->with('success', "Inscription retiree avec succes pour l'annee {$annee->libelle}.");
    }

    public function historique(Eleve $eleve)
    {
        $historique = Inscription::with(['classe', 'anneeAcademique'])
            ->where('eleve_id', $eleve->id)
            ->get()
            ->map(function ($inscription) use ($eleve) {
                $calculationService = app(CalculationService::class);

                return [
                    'annee' => $inscription->anneeAcademique,
                    'classe' => $inscription->classe,
                    'moyenne_generale' => $calculationService->calculateMoyenneGenerale($eleve, $inscription->anneeAcademique),
                ];
            })
            ->sortByDesc(fn ($item) => $item['annee']->libelle);

        return view('eleves.historique', compact('eleve', 'historique'));
    }

    protected function indexRouteParameters(array $extra = []): array
    {
        return array_filter(array_merge([
            'date' => request()->query('date'),
        ], $extra), fn ($value) => filled($value));
    }
}
