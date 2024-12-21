<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminOnlyMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group([
        'middleware' => AdminOnlyMiddleware::class,
        'prefix' => '/manage',
        'as' => 'manage.'
    ], function () {
        Route::apiResource('/company', CompanyController::class)->names('company');
        Route::apiResource('/employee', EmployeeController::class)->names('employee');
    });
});


require __DIR__ . '/auth.php';
