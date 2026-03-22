<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
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

        try {
            $data = $this->calculationService->getBulletinData($eleve);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $pdf = Pdf::loadView('bulletins.pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download("bulletin_{$eleve->nom}_{$eleve->prenom}_{$data['annee']->libelle}.pdf");
    }

    public function show($id)
    {
        return $this->generatePdf($id);
    }
}