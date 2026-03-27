<?php

use App\Http\Controllers\AnneeAcademiqueController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NoteMasseController;
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
            $redirectTo .= '?'.http_build_query($query);
        }

        $fragment = parse_url($previousUrl, PHP_URL_FRAGMENT);
        if ($fragment) {
            $redirectTo .= '#'.$fragment;
        }

        return redirect($redirectTo);
    })->name('academic-year.reset');

    Route::middleware(['readonly'])->group(function () {
        Route::middleware(['academic-write-access'])->group(function () {
            Route::resource('eleves', EleveController::class)->except(['index', 'show'])->parameters([
                'eleves' => 'eleve',
            ]);

            Route::resource('classes', ClasseController::class)->except(['index', 'show'])->parameters([
                'classes' => 'classe',
            ]);

            Route::get('/matieres/assigner', [MatiereController::class, 'assignerIndex'])->name('matieres.assigner');
            Route::get('/matieres/assigner/classe', [MatiereController::class, 'assignerClasse'])->name('matieres.assigner.classe');
            Route::post('/matieres/assigner', [MatiereController::class, 'assignerSauvegarder'])->name('matieres.assigner.store');

            Route::resource('matieres', MatiereController::class)->except(['index', 'show']);
            Route::resource('notes', NoteController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
            Route::get('/notes-masse', [NoteMasseController::class, 'index'])->name('notes.masse.index');
            Route::post('/notes-masse', [NoteMasseController::class, 'store'])->name('notes.masse.store');
        });

        Route::middleware(['role:admin'])->group(function () {
            Route::post('/annees/{annee}/write-access', [AnneeAcademiqueController::class, 'grantWriteAccess'])->name('annees.write-access.store');
            Route::delete('/annees/{annee}/write-access/{user}', [AnneeAcademiqueController::class, 'revokeWriteAccess'])->name('annees.write-access.destroy');
            Route::resource('annees', AnneeAcademiqueController::class);
        });

        Route::get('/eleves/{eleve}/historique', [EleveController::class, 'historique'])->name('eleves.historique');
        Route::resource('eleves', EleveController::class)->only(['index', 'show'])->parameters([
            'eleves' => 'eleve',
        ]);

        Route::get('/bulletin/pdf/{id}', [BulletinController::class, 'generatePdf'])->name('bulletins.pdf');
        Route::get('/classement', [App\Http\Controllers\ClassementController::class, 'index'])->name('classement.index');
        Route::get('/classement/pdf/{classe_id}', [App\Http\Controllers\ClassementController::class, 'exportPdf'])->name('classement.pdf');
        Route::get('/classes/{classe}/liste-pdf', [App\Http\Controllers\ListeClasseController::class, 'generatePdf'])->name('classes.liste.pdf');

        Route::resource('classes', ClasseController::class)->only(['index', 'show'])->parameters([
            'classes' => 'classe',
        ]);
        Route::resource('matieres', MatiereController::class)->only(['index', 'show']);
        Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    });
});

require __DIR__.'/auth.php';
