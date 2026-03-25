<?php

namespace App\Providers;

use App\Models\AnneeAcademique;
use App\Services\AcademicYearService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
            
            // If on dashboard or no date param, clear the session
            if (request()->routeIs('dashboard') && ! $dateParam) {
                session()->forget('academic_year_date');
            } elseif ($dateParam) {
                session(['academic_year_date' => $dateParam]);
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
            ]);
        });
    }
}