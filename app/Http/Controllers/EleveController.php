<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEleveRequest;
use App\Http\Requests\UpdateEleveRequest;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Note;
use App\Services\CalculationService;
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

    public function store(StoreEleveRequest $request)
    {
        $annee = currentAcademicYear()
            ?? AnneeAcademique::forDate(now()->toDateString(), true);

        if (! $annee) {
            return back()
                ->withInput()
                ->withErrors(['general' => "Aucune annee academique active. Veuillez en creer une d'abord."]);
        }

        DB::transaction(function () use ($request, $annee) {
            $eleve = Eleve::create($request->only([
                'matricule', 'nom', 'prenom', 'date_naissance', 'lieu_naissance', 'sexe',
            ]));

            Inscription::create([
                'eleve_id' => $eleve->id,
                'classe_id' => $request->classe_id,
                'annee_academique_id' => $annee->id,
            ]);
        });

        return redirect()
            ->route('eleves.index')
            ->with('success', "Eleve inscrit avec succes pour l'annee {$annee->libelle}.");
    }

    public function show(Eleve $eleve)
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();

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

                        return [
                            'matiere' => $matiere,
                            'notes' => $notes,
                            'total_notes' => $notes->count(),
                            'moyenne_devoirs' => $moyenneDevoirs !== null ? round($moyenneDevoirs, 2) : null,
                            'note_composition' => $noteComposition !== null ? round($noteComposition, 2) : null,
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

        return view('eleves.show', compact('eleve', 'annee', 'notesOverview', 'notesBySemestre'));
    }

    public function edit(Eleve $eleve)
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();
        $classes = Classe::orderBy('nom_classe')->get();
        $inscription = $eleve->inscriptionForDate($date);

        return view('eleves.edit', compact('eleve', 'classes', 'inscription', 'annee'));
    }

    public function update(UpdateEleveRequest $request, Eleve $eleve)
    {
        $date = request()->query('date') ?? currentAcademicDate();
        $annee = currentAcademicYear();

        DB::transaction(function () use ($request, $eleve, $annee, $date) {
            $eleve->update($request->only([
                'matricule', 'nom', 'prenom', 'date_naissance', 'lieu_naissance', 'sexe',
            ]));

            if ($annee) {
                $inscription = $eleve->inscriptionForDate($date);

                if ($inscription) {
                    $inscription->update(['classe_id' => $request->classe_id]);
                } else {
                    Inscription::create([
                        'eleve_id' => $eleve->id,
                        'classe_id' => $request->classe_id,
                        'annee_academique_id' => $annee->id,
                    ]);
                }
            }
        });

        return redirect()
            ->route('eleves.index')
            ->with('success', 'Eleve modifie avec succes.');
    }

    public function destroy(Eleve $eleve)
    {
        $eleve->delete();

        return redirect()
            ->route('eleves.index')
            ->with('success', 'Eleve supprime avec succes.');
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
}
