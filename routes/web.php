<?php

use App\Http\Controllers\Admin\DirectionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Calendrier
    Route::get('/calendrier', [DashboardController::class, 'calendrier'])->name('calendrier');
    Route::get('/api/calendrier/reservations', [DashboardController::class, 'apiReservations'])->name('api.calendrier.reservations');
    Route::get('/api/disponibilite', [ReservationController::class, 'checkDisponibilite'])->name('api.disponibilite');

    // Salles (responsable et admin)
    Route::middleware(['role:responsable|admin'])->group(function () {
        Route::resource('salles', SalleController::class);
    });

    // ── Routes statiques en premier (avant les wildcards {reservation}) ──

    // Responsable / admin — routes statiques
    Route::middleware(['role:responsable|admin'])->group(function () {
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/a-valider', [ReservationController::class, 'aValider'])->name('reservations.a-valider');
        Route::get('/reservations/directe/create', [ReservationController::class, 'createDirecte'])->name('reservations.directe.create');
        Route::post('/reservations/directe', [ReservationController::class, 'storeDirecte'])->name('reservations.directe.store');
        Route::post('/reservations/{reservation}/valider', [ReservationController::class, 'valider'])->name('reservations.valider');
        Route::post('/reservations/{reservation}/refuser', [ReservationController::class, 'refuser'])->name('reservations.refuser');
        Route::post('/reservations/{reservation}/annuler', [ReservationController::class, 'annuler'])->name('reservations.annuler');
    });

    // Utilisateur — routes statiques
    Route::get('/reservations/mes-reservations', [ReservationController::class, 'mesReservations'])->name('reservations.mes-reservations');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

    // ── Routes avec paramètre {reservation} en dernier ──
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');

    // Admin — gestion des utilisateurs et directions
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users',      UserController::class)->except(['show']);
        Route::resource('directions', DirectionController::class)->except(['show', 'create', 'edit']);
    });
});

require __DIR__.'/auth.php';
