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

    // Routes Secrétariat + Admin (Base)
    // Explicit parameter name to avoid incorrect singularization (e.g. "elefe")
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
        Route::resource('classes', ClasseController::class);

        // Matières
        Route::resource('matieres', MatiereController::class);

        // Notes
        Route::resource('notes', NoteController::class);
        Route::get('/notes-masse', [App\Http\Controllers\NoteMasseController::class, 'index'])->name('notes.masse.index');
        Route::post('/notes-masse', [App\Http\Controllers\NoteMasseController::class, 'store'])->name('notes.masse.store');

        // Années académiques
        Route::resource('annees', AnneeAcademiqueController::class);
    });
});

require __DIR__.'/auth.php';
