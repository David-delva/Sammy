<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Services\CalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClassementController extends Controller
{
    public function __construct(
        protected CalculationService $calculationService,
    ) {}

    public function index(Request $request)
    {
        $annee = currentAcademicYear();
        if (! $annee) {
            return redirect()->route('annees.index')->with('error', 'Aucune année active.');
        }

        $classes = Classe::orderBy('nom_classe')->get();
        $selectedClasseId = $request->query('classe_id');

        $classement = collect();
        $classe = null;

        if ($selectedClasseId) {
            $classe = Classe::findOrFail($selectedClasseId);
            $classement = $this->calculationService->getClassementForClass($classe, $annee);
        }

        return view('classement.index', compact('classes', 'classement', 'selectedClasseId', 'classe', 'annee'));
    }

    public function exportPdf(int $classe_id)
    {
        $annee = currentAcademicYear();
        $classe = Classe::findOrFail($classe_id);
        $classement = $this->calculationService->getClassementForClass($classe, $annee);

        $pdf = Pdf::loadView('classement.pdf', compact('classement', 'classe', 'annee'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("classement_{$classe->nom_classe}_{$annee->libelle}.pdf");
    }
}
