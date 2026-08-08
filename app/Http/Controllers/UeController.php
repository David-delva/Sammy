<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Semestre;
use App\Models\Ue;
use App\Services\AcademicCacheService;
use App\Services\AcademicPerformanceProjector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UeController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
        private readonly AcademicPerformanceProjector $projector,
    ) {}

    public function index()
    {
        $classes = Classe::orderBy('nom_classe')->get();

        return view('ues.index', compact('classes'));
    }

    public function gererClasse(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
        ]);

        $annee = currentAcademicYear();
        $classes = Classe::orderBy('nom_classe')->get();
        $classe = Classe::findOrFail($request->classe_id);

        if (! $annee) {
            return view('ues.index', compact('classes', 'classe', 'annee'));
        }

        $matieres = $classe->matieresForAnnee($annee->id)->orderBy('nom_matiere')->get();

        $semestreS1 = Semestre::pour($classe->id, $annee->id, Note::SEMESTRE_1);
        $semestreS2 = Semestre::pour($classe->id, $annee->id, Note::SEMESTRE_2);

        $ues = Ue::query()
            ->whereIn('semestre_id', [$semestreS1->id, $semestreS2->id])
            ->with(['matieres', 'semestre'])
            ->orderBy('code')
            ->get();

        $uesParSemestre = [
            Note::SEMESTRE_1 => $ues->where('semestre_id', $semestreS1->id)->values(),
            Note::SEMESTRE_2 => $ues->where('semestre_id', $semestreS2->id)->values(),
        ];

        $matiereAssigneeA = [];
        foreach ($ues as $ue) {
            foreach ($ue->matieres as $matiere) {
                $matiereAssigneeA[$matiere->id] = $ue;
            }
        }

        $semestresParNumero = [
            Note::SEMESTRE_1 => $semestreS1,
            Note::SEMESTRE_2 => $semestreS2,
        ];

        return view('ues.index', compact(
            'classes',
            'classe',
            'annee',
            'matieres',
            'uesParSemestre',
            'matiereAssigneeA',
            'semestresParNumero'
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
            'code' => ['required', 'string', 'max:30', Rule::unique('ues', 'code')],
            'libelle' => ['required', 'string', 'max:150'],
            'semestre' => ['required', 'integer', 'in:1,2'],
            'matiere_ids' => ['array'],
            'matiere_ids.*' => ['integer', 'exists:matieres,id'],
        ]);

        $classe = Classe::findOrFail($validated['classe_id']);
        $matiereIds = $validated['matiere_ids'] ?? [];

        $conflit = $this->matieresDejaAffectees($matiereIds, null);

        if ($conflit !== '') {
            return back()->withErrors(['matiere_ids' => "Déjà affectée(s) à une autre UE : {$conflit}"])->withInput();
        }

        $semestreEntity = Semestre::pour($classe->id, $annee->id, (int) $validated['semestre']);
        $credits = $this->sommeCredits($classe->id, $annee->id, $matiereIds);

        $ue = Ue::create([
            'code' => $validated['code'],
            'libelle' => $validated['libelle'],
            'credits' => $credits,
            'semestre_id' => $semestreEntity->id,
        ]);

        $this->syncMatieres($ue, $classe->id, $annee->id, $matiereIds);

        $this->projector->rebuildClassYear($classe->id, $annee->id);
        $this->academicCache->bust();

        return redirect()->route('ues.gerer', ['classe_id' => $classe->id])
            ->with('success', "UE {$ue->code} créée.");
    }

    public function update(Request $request, Ue $ue)
    {
        $ue->loadMissing('semestre');
        $classe = $ue->semestre->classe;
        $anneeId = (int) $ue->semestre->annee_academique_id;

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('ues', 'code')->ignore($ue->id)],
            'libelle' => ['required', 'string', 'max:150'],
            'semestre' => ['required', 'integer', 'in:1,2'],
            'matiere_ids' => ['array'],
            'matiere_ids.*' => ['integer', 'exists:matieres,id'],
        ]);

        $matiereIds = $validated['matiere_ids'] ?? [];

        $conflit = $this->matieresDejaAffectees($matiereIds, $ue->id);

        if ($conflit !== '') {
            return back()->withErrors(['matiere_ids' => "Déjà affectée(s) à une autre UE : {$conflit}"])->withInput();
        }

        $semestreEntity = Semestre::pour($classe->id, $anneeId, (int) $validated['semestre']);
        $credits = $this->sommeCredits($classe->id, $anneeId, $matiereIds);

        $ue->update([
            'code' => $validated['code'],
            'libelle' => $validated['libelle'],
            'credits' => $credits,
            'semestre_id' => $semestreEntity->id,
        ]);

        $this->syncMatieres($ue, $classe->id, $anneeId, $matiereIds);

        $this->projector->rebuildClassYear($classe->id, $anneeId);
        $this->academicCache->bust();

        return redirect()->route('ues.gerer', ['classe_id' => $classe->id])
            ->with('success', "UE {$ue->code} mise à jour.");
    }

    public function destroy(Ue $ue)
    {
        $ue->loadMissing('semestre');
        $classeId = (int) $ue->semestre->classe_id;
        $anneeId = (int) $ue->semestre->annee_academique_id;
        $code = $ue->code;

        Matiere::where('ue_id', $ue->id)->update(['ue_id' => null, 'coefficient' => null, 'credits' => null]);
        $ue->delete();

        $this->projector->rebuildClassYear($classeId, $anneeId);
        $this->academicCache->bust();

        return redirect()->route('ues.gerer', ['classe_id' => $classeId])
            ->with('success', "UE {$code} supprimée.");
    }

    /**
     * Rattache les matières sélectionnées à l'UE (matiere.ue_id direct) et miroite
     * leur coefficient/crédits (déjà définis via l'assignation classe_matiere) sur la matière,
     * pour rester conforme au modèle de données (Matiere: ue_id, coefficient, credits).
     */
    private function syncMatieres(Ue $ue, int $classeId, int $anneeId, array $matiereIds): void
    {
        Matiere::where('ue_id', $ue->id)
            ->whereNotIn('id', $matiereIds)
            ->update(['ue_id' => null, 'coefficient' => null, 'credits' => null]);

        if ($matiereIds === []) {
            return;
        }

        $pivots = DB::table('classe_matiere')
            ->where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeId)
            ->whereIn('matiere_id', $matiereIds)
            ->get(['matiere_id', 'coefficient', 'credits'])
            ->keyBy('matiere_id');

        foreach ($matiereIds as $matiereId) {
            $pivot = $pivots->get($matiereId);

            Matiere::where('id', $matiereId)->update([
                'ue_id' => $ue->id,
                'coefficient' => $pivot->coefficient ?? 1,
                'credits' => $pivot->credits ?? 0,
            ]);
        }
    }

    private function sommeCredits(int $classeId, int $anneeId, array $matiereIds): int
    {
        if ($matiereIds === []) {
            return 0;
        }

        return (int) DB::table('classe_matiere')
            ->where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeId)
            ->whereIn('matiere_id', $matiereIds)
            ->sum('credits');
    }

    private function matieresDejaAffectees(array $matiereIds, ?int $ignoreUeId): string
    {
        if ($matiereIds === []) {
            return '';
        }

        $query = Matiere::query()
            ->whereIn('id', $matiereIds)
            ->whereNotNull('ue_id');

        if ($ignoreUeId !== null) {
            $query->where('ue_id', '!=', $ignoreUeId);
        }

        return $query->pluck('nom_matiere')->implode(', ');
    }
}
