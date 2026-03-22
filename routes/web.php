<?php

use App\Http\Controllers\ClasseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\BulletinController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AnneeAcademiqueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes SecrÃ©tariat + Admin (Base)
    Route::middleware(['readonly'])->group(function () {
        Route::get('/eleves/{eleve}/historique', [EleveController::class, 'historique'])->name('eleves.historique');
        Route::resource('eleves', EleveController::class)->parameters([
            'eleves' => 'eleve'
        ]);
        Route::get('/bulletin/pdf/{id}', [BulletinController::class, 'generatePdf'])->name('bulletins.pdf');

        // Classement
        Route::get('/classement', [App\Http\Controllers\ClassementController::class, 'index'])->name('classement.index');
        Route::get('/classement/pdf/{classe_id}', [App\Http\Controllers\ClassementController::class, 'exportPdf'])->name('classement.pdf');
        Route::get('/classes/{classe}/liste-pdf', [App\Http\Controllers\ListeClasseController::class, 'generatePdf'])->name('classes.liste.pdf');

        // Routes STRICTEMENT Admin
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('classes', ClasseController::class)->parameters([
                'classes' => 'classe'
            ]);
            Route::resource('matieres', MatiereController::class);
            
            // Gestion des liaisons classe Ã— matiÃ¨re
            Route::get('/matieres/assigner', [MatiereController::class, 'assignerIndex'])->name('matieres.assigner');
            Route::get('/matieres/assigner/classe', [MatiereController::class, 'assignerClasse'])->name('matieres.assigner.classe');
            Route::post('/matieres/assigner', [MatiereController::class, 'assignerSauvegarder'])->name('matieres.assigner.store');
            
            Route::resource('notes', NoteController::class);
            Route::get('/notes-masse', [App\Http\Controllers\NoteMasseController::class, 'index'])->name('notes.masse.index');
            Route::post('/notes-masse', [App\Http\Controllers\NoteMasseController::class, 'store'])->name('notes.masse.store');
            Route::resource('annees', AnneeAcademiqueController::class);
        });
    });
});

require __DIR__.'/auth.php';
