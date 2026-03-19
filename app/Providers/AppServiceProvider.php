<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\AnneeAcademique;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ensure application helpers are loaded early so helper functions are available
        $helpers = app_path('helpers.php');
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share a date-aware academic year with all views.
        View::composer('*', function ($view) {
            // Prefer explicit query parameter `date` (Y-m-d), otherwise session or now
            $dateParam = request()->query('date');

            // Persist explicit selection in session so navigation keeps context
            if ($dateParam && $dateParam !== session('academic_year_date')) {
                session(['academic_year_date' => $dateParam]);
            }

            $dateSource = $dateParam ?? session('academic_year_date');
            $date = $dateSource ? Carbon::parse($dateSource) : Carbon::now();

            $label = AnneeAcademique::labelForDate($date);
            // Try to find the academic year by label, fallback to active
            $annee = Cache::remember("academicYear:{$label}", 300, function () use ($label) {
                return AnneeAcademique::where('libelle', $label)->first() ?? AnneeAcademique::where('active', true)->first();
            });

            // Provide list of academic years for selection in layout (cache short)
            $years = Cache::remember('academicYears', 300, function () {
                return AnneeAcademique::orderBy('libelle', 'desc')->get();
            });

            // Share with all views
            $view->with('currentAcademicDate', $date->toDateString());
            $view->with('currentAcademicLabel', $label);
            $view->with('currentAcademicYear', $annee);
            $view->with('academicYears', $years);

            // Also bind to the container for controllers/services
            if ($annee) {
                app()->instance('currentAcademicYear', $annee);
            }
        });
    }
}
