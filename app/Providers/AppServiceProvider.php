<?php

namespace App\Providers;

use App\Models\AnneeAcademique;
use App\Models\Inscription;
use App\Models\Note;
use App\Observers\InscriptionObserver;
use App\Observers\NoteObserver;
use App\Services\AcademicWriteAccessService;
use App\Services\AcademicYearService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AcademicYearService::class, function () {
            return new AcademicYearService;
        });

        $helpers = app_path('helpers.php');
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }

    public function boot(): void
    {
        Note::observe(NoteObserver::class);
        Inscription::observe(InscriptionObserver::class);

        View::composer('*', function ($view) {
            $service = app(AcademicYearService::class);
            $writeAccess = app(AcademicWriteAccessService::class);

            $hasDateParam = request()->has('date');
            $dateParam = $service->sanitizeDate(request()->query('date'));

            if (request()->routeIs('dashboard') && ! $hasDateParam) {
                session()->forget('academic_year_date');
            } elseif ($hasDateParam && $dateParam) {
                session(['academic_year_date' => $dateParam]);
            } elseif ($hasDateParam) {
                session()->forget('academic_year_date');
            }

            $annee = null;
            $years = collect();

            if (Schema::hasTable('annee_academiques')) {
                $annee = $service->forDate();
                $years = AnneeAcademique::query()->orderBy('libelle', 'desc')->get();
            }

            $view->with([
                'currentAcademicDate' => $service->referenceDate(),
                'currentAcademicYear' => $annee,
                'isCurrentAcademicYear' => $annee ? $service->isCurrentYear() : false,
                'academicYears' => $years,
                'currentAcademicLabel' => $annee ? $annee->libelle : null,
                'canManageAcademicData' => auth()->check() && $writeAccess->canManageSelectedYear(auth()->user()),
            ]);
        });
    }
}
