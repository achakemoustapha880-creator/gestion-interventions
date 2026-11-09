<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\TechnicienController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    // ----- Pages accessibles à tous les utilisateurs connectés -----
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::redirect('/dashboard', '/');

    Route::get('mon-profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('mon-profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('mon-profil/mot-de-passe', [ProfilController::class, 'motDePasse'])->name('profil.password');

    Route::resource('clients', ClientController::class)->except('destroy')->withTrashed(['show']);
    Route::resource('techniciens', TechnicienController::class)->except('destroy')
        ->parameters(['techniciens' => 'technicien']);
    Route::resource('equipements', EquipementController::class)->except('destroy');

    Route::get('historique', [InterventionController::class, 'historique'])->name('interventions.historique');
    Route::get('historique/export', [InterventionController::class, 'exporter'])->name('interventions.export');
    Route::resource('interventions', InterventionController::class)->except('destroy');
    Route::patch('interventions/{intervention}/statut', [InterventionController::class, 'changerStatut'])
        ->name('interventions.statut');
        Route::get('interventions/{intervention}/fiche', [InterventionController::class, 'fiche'])->name('interventions.fiche');

    // ----- Pages réservées aux administrateurs -----
    Route::middleware('can:admin')->group(function () {
        Route::get('rapports', RapportController::class)->name('rapports');
        Route::resource('utilisateurs', UtilisateurController::class)->except('show');

        Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
        Route::delete('techniciens/{technicien}', [TechnicienController::class, 'destroy'])->name('techniciens.destroy');
        Route::delete('equipements/{equipement}', [EquipementController::class, 'destroy'])->name('equipements.destroy');
        Route::delete('interventions/{intervention}', [InterventionController::class, 'destroy'])->name('interventions.destroy');
        Route::delete('historique/supprimer', [InterventionController::class, 'supprimerSelection'])->name('interventions.supprimer-selection');

        // Corbeille des interventions
        Route::get('corbeille', [InterventionController::class, 'corbeille'])->name('interventions.corbeille');
        Route::post('corbeille/restaurer', [InterventionController::class, 'restaurer'])->name('interventions.restaurer');
        Route::delete('corbeille/supprimer', [InterventionController::class, 'supprimerDefinitivement'])->name('interventions.supprimer-definitivement');
        Route::delete('corbeille/vider', [InterventionController::class, 'viderCorbeille'])->name('interventions.vider-corbeille');

        // Corbeille des clients
        Route::get('corbeille-clients', [ClientController::class, 'corbeille'])->name('clients.corbeille');
        Route::post('corbeille-clients/restaurer', [ClientController::class, 'restaurer'])->name('clients.restaurer');
        Route::delete('corbeille-clients/supprimer', [ClientController::class, 'supprimerDefinitivement'])->name('clients.supprimer-definitivement');
        Route::delete('corbeille-clients/vider', [ClientController::class, 'viderCorbeille'])->name('clients.vider-corbeille');
    });

    // Routes de Breeze (conservées)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';