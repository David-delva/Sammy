<?php

namespace App\Http\Controllers;

use App\Models\Semestre;
use App\Services\AcademicCacheService;
use Illuminate\Http\Request;

class SemestreController extends Controller
{
    public function __construct(private readonly AcademicCacheService $academicCache) {}

    public function update(Request $request, Semestre $semestre)
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:50'],
        ]);

        $semestre->update($validated);
        $this->academicCache->bust();

        return redirect()->route('ues.gerer', ['classe_id' => $semestre->classe_id])
            ->with('success', "Libellé du semestre mis à jour : {$semestre->libelle}.");
    }
}
