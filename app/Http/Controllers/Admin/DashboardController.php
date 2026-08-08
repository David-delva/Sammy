<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\ResultatAnnuel;
use App\Services\AcademicCacheService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AcademicCacheService $academicCache,
    ) {}

    public function index()
    {
        $annee = currentAcademicYear();
        $dateParam = request()->query('date');

        $stats = $this->academicCache->remember(
            $this->academicCache->dashboardStatsKey($dateParam, $annee?->id),
            300,
            function () use ($annee): array {
                if ($annee) {
                    $moyenneGenerale = ResultatAnnuel::query()
                        ->where('annee_academique_id', $annee->id)
                        ->whereNotNull('moyenne_annuelle')
                        ->avg('moyenne_annuelle');

                    return [
                        'total_eleves' => Inscription::query()
                            ->where('annee_academique_id', $annee->id)
                            ->distinct()
                            ->count('eleve_id'),
                        'total_classes' => Inscription::query()
                            ->where('annee_academique_id', $annee->id)
                            ->distinct()
                            ->count('classe_id'),
                        'total_matieres' => DB::table('classe_matiere')
                            ->where('annee_academique_id', $annee->id)
                            ->distinct()
                            ->count('matiere_id'),
                        'total_notes' => Note::query()
                            ->where('annee_academique_id', $annee->id)
                            ->count(),
                        'moyenne_generale' => $moyenneGenerale !== null ? round((float) $moyenneGenerale, 2) : null,
                    ];
                }

                $moyenneGenerale = ResultatAnnuel::query()
                    ->whereNotNull('moyenne_annuelle')
                    ->avg('moyenne_annuelle');

                return [
                    'total_eleves' => Inscription::query()->distinct()->count('eleve_id'),
                    'total_classes' => Classe::query()->count(),
                    'total_matieres' => Matiere::query()->count(),
                    'total_notes' => Note::query()->count(),
                    'moyenne_generale' => $moyenneGenerale !== null ? round((float) $moyenneGenerale, 2) : null,
                ];
            },
        );

        return view('dashboard', compact('stats', 'annee'));
    }
}
