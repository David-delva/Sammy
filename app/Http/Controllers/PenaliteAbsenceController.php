<?php

namespace App\Http\Controllers;

use App\Models\PenaliteAbsence;
use App\Services\AcademicCacheService;
use App\Services\AcademicPerformanceProjector;
use Illuminate\Http\Request;

class PenaliteAbsenceController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
        private readonly AcademicPerformanceProjector $projector,
    ) {}

    public function edit()
    {
        $global = PenaliteAbsence::query()
            ->whereNull('classe_id')
            ->whereNull('annee_academique_id')
            ->first();

        $tauxActuel = $global?->penalite_par_heure ?? PenaliteAbsence::DEFAULT_PENALITE_PAR_HEURE;

        return view('parametres.penalite-absence', compact('tauxActuel'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'penalite_par_heure' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        PenaliteAbsence::updateOrCreate(
            ['classe_id' => null, 'annee_academique_id' => null],
            ['penalite_par_heure' => $validated['penalite_par_heure']]
        );

        $this->projector->rebuildAll();
        $this->academicCache->bust();

        return redirect()->route('penalite-absence.edit')
            ->with('success', 'Pénalité d\'absence mise à jour pour toutes les classes.');
    }
}
