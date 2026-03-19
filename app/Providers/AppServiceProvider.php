<?php

namespace App\Providers;

use App\Services\AcademicYearService;
use App\Models\AnneeAcademique;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AcademicYearService::class, function ($app) {
            return new AcademicYearService();
        });

        $helpers = app_path('helpers.php');
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $service = app(AcademicYearService::class);
            
            $dateParam = request()->query('date');
            if ($dateParam) {
                session(['academic_year_date' => $dateParam]);
            }

            $annee = $service->forDate();
            $years = Cache::remember('academic_years_list', 300, function () {
                return AnneeAcademique::orderBy('libelle', 'desc')->get();
            });

            $view->with([
                'currentAcademicDate'  => $service->referenceDate(),
                'currentAcademicYear'  => $annee,
                'isCurrentAcademicYear' => $service->isCurrentYear(),
                'academicYears'        => $years,
            ]);
        });
    }
}
