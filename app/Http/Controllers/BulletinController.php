<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Note;
use App\Services\CalculationService;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinController extends Controller
{
    public function __construct(protected CalculationService $calculationService)
    {
    }

    public function generatePdf($id)
    {
        $eleve = Eleve::findOrFail($id);
        $semestre = (int) request()->query('semestre', Note::SEMESTRE_1);

        if (! in_array($semestre, array_keys(Note::semestreOptions()), true)) {
            $semestre = Note::SEMESTRE_1;
        }

        try {
            $data = $this->calculationService->getBulletinData($eleve, $semestre);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $pdf = Pdf::loadView('bulletins.pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download("bulletin_{$eleve->nom}_{$eleve->prenom}_S{$semestre}_{$data['annee']->libelle}.pdf");
    }

    public function show($id)
    {
        return $this->generatePdf($id);
    }
}