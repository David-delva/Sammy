<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Services\CalculationService;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinController extends Controller
{
    protected $calculationService;

    public function __construct(CalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    public function generatePdf($id)
    {
        $eleve = Eleve::findOrFail($id);

        try {
            $data = $this->calculationService->getBulletinData($eleve);
            } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
             }

    // ... génération PDF
    }
}
