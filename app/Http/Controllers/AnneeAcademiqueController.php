<?php

namespace App\Http\Controllers;

use App\Models\AcademicResult;
use App\Models\AcademicSubjectResult;
use App\Models\AnneeAcademique;
use App\Models\User;
use App\Services\AcademicCacheService;
use App\Services\AcademicWriteAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AnneeAcademiqueController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
        private readonly AcademicWriteAccessService $academicWriteAccess,
    ) {}

    public function index()
    {
        $annees = AnneeAcademique::orderBy('libelle', 'desc')->get();
        $secretariats = User::query()
            ->where('role', 'secretariat')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $authorizedUserIdsByYear = Schema::hasTable('academic_year_user_permissions')
            ? DB::table('academic_year_user_permissions')
                ->select('annee_academique_id', 'user_id')
                ->get()
                ->groupBy('annee_academique_id')
                ->map(fn ($permissions) => $permissions->pluck('user_id')->map(fn ($id) => (int) $id)->all())
                ->all()
            : [];

        $currentCalendarLabel = AnneeAcademique::labelForDate(now()->toDateString());

        return view('annees.index', compact('annees', 'secretariats', 'authorizedUserIdsByYear', 'currentCalendarLabel'));
    }

    public function create()
    {
        return view('annees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:9', 'unique:annee_academiques,libelle'],
            'active' => ['sometimes', 'boolean'],
        ]);

        if ($request->boolean('active')) {
            AnneeAcademique::where('active', true)->update(['active' => false]);
            $validated['active'] = true;
        } else {
            $validated['active'] = false;
        }

        AnneeAcademique::create($validated);
        $this->invalidateAcademicCaches();

        return redirect()->route('annees.index')->with('success', 'Annee academique creee.');
    }

    public function edit(AnneeAcademique $annee)
    {
        return view('annees.edit', compact('annee'));
    }

    public function update(Request $request, AnneeAcademique $annee)
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:9', 'unique:annee_academiques,libelle,'.$annee->id],
            'active' => ['sometimes', 'boolean'],
        ]);

        if ($request->boolean('active')) {
            AnneeAcademique::where('active', true)->where('id', '!=', $annee->id)->update(['active' => false]);
            $validated['active'] = true;
        } else {
            $validated['active'] = false;
        }

        $annee->update($validated);
        $this->invalidateAcademicCaches();

        return redirect()->route('annees.index')->with('success', 'Annee academique mise a jour.');
    }

    public function destroy(AnneeAcademique $annee)
    {
        if ($annee->active) {
            return redirect()
                ->route('annees.index')
                ->with('error', "Suppression bloquee : desactivez d'abord l'annee academique {$annee->libelle}.");
        }

        $linkedData = collect([
            'inscriptions' => $annee->inscriptions()->exists(),
            'notes' => DB::table('notes')->where('annee_academique_id', $annee->id)->exists(),
            'assignations de matieres' => DB::table('classe_matiere')->where('annee_academique_id', $annee->id)->exists(),
            'resultats projetes' => AcademicResult::query()->where('annee_academique_id', $annee->id)->exists()
                || AcademicSubjectResult::query()->where('annee_academique_id', $annee->id)->exists(),
        ])->filter()->keys()->values();

        if ($linkedData->isNotEmpty()) {
            return redirect()
                ->route('annees.index')
                ->with('error', "Suppression bloquee : l'annee {$annee->libelle} contient deja des ".implode(', ', $linkedData->all()).'.');
        }

        $annee->delete();
        $this->invalidateAcademicCaches();

        return redirect()->route('annees.index')->with('success', 'Annee academique supprimee.');
    }

    public function grantWriteAccess(Request $request, AnneeAcademique $annee)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'secretariat')),
            ],
        ]);

        if ($this->academicWriteAccess->isCurrentCalendarYear($annee)) {
            return redirect()
                ->route('annees.index')
                ->with('warning', "Le secretariat peut deja modifier l'annee {$annee->libelle} car elle correspond a l'annee academique en cours.");
        }

        $userId = (int) $validated['user_id'];

        DB::table('academic_year_user_permissions')->updateOrInsert(
            [
                'annee_academique_id' => $annee->id,
                'user_id' => $userId,
            ],
            [
                'granted_by' => $request->user()?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $secretariat = User::query()->findOrFail($userId);

        return redirect()
            ->route('annees.index')
            ->with('success', "Autorisation accordee a {$secretariat->name} pour modifier l'annee {$annee->libelle}.");
    }

    public function revokeWriteAccess(AnneeAcademique $annee, User $user)
    {
        abort_unless($user->role === 'secretariat', 404);

        if ($this->academicWriteAccess->isCurrentCalendarYear($annee)) {
            return redirect()
                ->route('annees.index')
                ->with('warning', "L'acces du secretariat a l'annee {$annee->libelle} est deja automatique tant qu'elle reste l'annee en cours.");
        }

        DB::table('academic_year_user_permissions')
            ->where('annee_academique_id', $annee->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()
            ->route('annees.index')
            ->with('success', "Autorisation retiree a {$user->name} pour l'annee {$annee->libelle}.");
    }

    private function invalidateAcademicCaches(): void
    {
        $this->academicCache->bust();
    }
}
