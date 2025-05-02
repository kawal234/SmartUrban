<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\UtilityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Auth::routes();

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard Route
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Logistics Routes
    Route::prefix('logistics')->group(function () {
        Route::get('/', [LogisticsController::class, 'index'])->name('logistics.index');
        Route::get('/create', [LogisticsController::class, 'create'])->name('logistics.create');
        Route::post('/', [LogisticsController::class, 'store'])->name('logistics.store');
        Route::get('/{id}', [LogisticsController::class, 'show'])->name('logistics.show');
        Route::get('/{id}/edit', [LogisticsController::class, 'edit'])->name('logistics.edit');
        Route::put('/{id}', [LogisticsController::class, 'update'])->name('logistics.update');
        Route::delete('/{id}', [LogisticsController::class, 'destroy'])->name('logistics.destroy');
        Route::post('/{id}/optimize', [LogisticsController::class, 'optimize'])->name('logistics.optimize');
    });

    // Utility Routes
    Route::prefix('utilities')->group(function () {
        Route::get('/', [UtilityController::class, 'index'])->name('utilities.index');
        Route::get('/create', [UtilityController::class, 'create'])->name('utilities.create');
        Route::post('/', [UtilityController::class, 'store'])->name('utilities.store');
        Route::get('/{id}', [UtilityController::class, 'show'])->name('utilities.show');
        Route::get('/{id}/edit', [UtilityController::class, 'edit'])->name('utilities.edit');
        Route::put('/{id}', [UtilityController::class, 'update'])->name('utilities.update');
        Route::delete('/{id}', [UtilityController::class, 'destroy'])->name('utilities.destroy');
    });
});
