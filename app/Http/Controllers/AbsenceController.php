<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Classe;
use App\Models\Note;
use App\Services\AcademicCacheService;
use App\Services\AcademicPerformanceProjector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
        private readonly AcademicPerformanceProjector $projector,
    ) {}

    public function index()
    {
        $classes = Classe::orderBy('nom_classe')->get();

        return view('absences.index', compact('classes'));
    }

    public function saisirClasse(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'semestre' => 'nullable|integer|in:1,2',
        ]);

        $annee = currentAcademicYear();
        $classes = Classe::orderBy('nom_classe')->get();
        $classe = Classe::findOrFail($request->classe_id);
        $semestre = in_array((int) $request->semestre, [Note::SEMESTRE_1, Note::SEMESTRE_2], true)
            ? (int) $request->semestre
            : Note::SEMESTRE_1;

        if (! $annee) {
            return view('absences.index', compact('classes', 'classe', 'annee'));
        }

        $matieres = $classe->matieresForAnnee($annee->id)->orderBy('nom_matiere')->get();
        $eleves = $classe->eleves()->wherePivot('annee_academique_id', $annee->id)->orderBy('nom')->orderBy('prenom')->get();

        $absences = Absence::query()
            ->where('annee_academique_id', $annee->id)
            ->where('semestre', $semestre)
            ->whereIn('eleve_id', $eleves->modelKeys())
            ->get()
            ->keyBy(fn (Absence $a) => $a->eleve_id.':'.$a->matiere_id);

        return view('absences.index', compact(
            'classes',
            'classe',
            'annee',
            'semestre',
            'matieres',
            'eleves',
            'absences'
        ));
    }

    public function store(Request $request)
    {
        $annee = currentAcademicYear();

        if (! $annee) {
            return back()->withErrors(['general' => 'Aucune année académique active.']);
        }

        $validated = $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'semestre' => 'required|integer|in:1,2',
            'heures' => 'array',
            'heures.*.*' => 'nullable|integer|min:0|max:500',
        ]);

        $classe = $validated['classe_id'];
        $semestre = $validated['semestre'];

        DB::transaction(function () use ($validated, $annee, $classe, $semestre) {
            foreach ($validated['heures'] ?? [] as $eleveId => $parMatiere) {
                foreach ($parMatiere as $matiereId => $heures) {
                    $heures = (int) ($heures ?? 0);

                    Absence::updateOrCreate(
                        [
                            'eleve_id' => $eleveId,
                            'matiere_id' => $matiereId,
                            'annee_academique_id' => $annee->id,
                            'semestre' => $semestre,
                        ],
                        ['heures' => $heures]
                    );
                }
            }
        });

        $this->projector->rebuildClassYear((int) $classe, $annee->id);
        $this->academicCache->bust();

        return redirect()->route('absences.saisir', ['classe_id' => $classe, 'semestre' => $semestre])
            ->with('success', 'Absences enregistrées.');
    }
}
