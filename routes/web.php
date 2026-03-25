<?php

use App\Http\Controllers\AnneeAcademiqueController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin,secretariat'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reset academic year session (outside readonly middleware)
    Route::get('/reset-academic-year', function (Request $request) {
        session()->forget('academic_year_date');
        session()->save();

        $fallbackUrl = route('dashboard');
        $previousUrl = url()->previous();

        if (! $previousUrl) {
            return redirect($fallbackUrl);
        }

        $previousHost = parse_url($previousUrl, PHP_URL_HOST);
        if ($previousHost && $previousHost !== $request->getHost()) {
            return redirect($fallbackUrl);
        }

        $redirectPath = parse_url($previousUrl, PHP_URL_PATH) ?: parse_url($fallbackUrl, PHP_URL_PATH);
        $resetPath = parse_url(route('academic-year.reset'), PHP_URL_PATH);

        if ($redirectPath === $resetPath) {
            return redirect($fallbackUrl);
        }

        parse_str(parse_url($previousUrl, PHP_URL_QUERY) ?? '', $query);
        unset($query['date']);

        $redirectTo = $redirectPath;
        if ($query !== []) {
            $redirectTo .= '?' . http_build_query($query);
        }

        $fragment = parse_url($previousUrl, PHP_URL_FRAGMENT);
        if ($fragment) {
            $redirectTo .= '#' . $fragment;
        }

        return redirect($redirectTo);
    })->name('academic-year.reset');

    Route::middleware(['readonly'])->group(function () {
        Route::get('/eleves/{eleve}/historique', [EleveController::class, 'historique'])->name('eleves.historique');
        Route::resource('eleves', EleveController::class)->parameters([
            'eleves' => 'eleve',
        ]);
        Route::get('/bulletin/pdf/{id}', [BulletinController::class, 'generatePdf'])->name('bulletins.pdf');

        Route::get('/classement', [App\Http\Controllers\ClassementController::class, 'index'])->name('classement.index');
        Route::get('/classement/pdf/{classe_id}', [App\Http\Controllers\ClassementController::class, 'exportPdf'])->name('classement.pdf');
        Route::get('/classes/{classe}/liste-pdf', [App\Http\Controllers\ListeClasseController::class, 'generatePdf'])->name('classes.liste.pdf');

        // Admin et Secretariat peuvent gerer : Classes, Matieres, Assignation, Notes, Saisie en masse
        Route::middleware(['role:admin,secretariat'])->group(function () {
            Route::resource('classes', ClasseController::class)->parameters([
                'classes' => 'classe',
            ]);

            // Routes personnalisees pour l'assignation (doivent etre avant le resource)
            Route::get('/matieres/assigner', [MatiereController::class, 'assignerIndex'])->name('matieres.assigner');
            Route::get('/matieres/assigner/classe', [MatiereController::class, 'assignerClasse'])->name('matieres.assigner.classe');
            Route::post('/matieres/assigner', [MatiereController::class, 'assignerSauvegarder'])->name('matieres.assigner.store');

            Route::resource('matieres', MatiereController::class);

            Route::resource('notes', NoteController::class);
            Route::get('/notes-masse', [App\Http\Controllers\NoteMasseController::class, 'index'])->name('notes.masse.index');
            Route::post('/notes-masse', [App\Http\Controllers\NoteMasseController::class, 'store'])->name('notes.masse.store');
        });

        // Admin uniquement
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('annees', AnneeAcademiqueController::class);
        });
    });
});

require __DIR__.'/auth.php';
