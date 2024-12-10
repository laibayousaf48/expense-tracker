<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [AdminController::class, 'loginView'])->name('login');
Route::get('/register', [AdminController::class, 'registerView'])->name('register');
Route::post('/admin/register', [AdminController::class, 'register'])->name('admin.register');
// Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
// Route::middleware(['auth:sanctum'])->post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
//         ->middleware(['guest:admin'])
//         ->name('admin.login.store');
Route::post('/admin/login', [AdminController::class, 'login'])
    ->middleware(['guest:admin'])
    ->name('admin.login');
    
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth:admin')
        ->name('admin.logout');

        Route::middleware(['auth'])->group(function () {
            Route::get('/home', [AdminController::class, 'home'])
           ->name('home');
           Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/expenses', [AdminController::class, 'expenses'])->name('admin.expenses'); 
        Route::get('/admin/budgets', [AdminController::class, 'budgets'])->name('admin.budgets');
        Route::get('/admin/users/{id}', [AdminController::class, 'usersShow'])->name('admin.users.show');
        Route::get('/admin/users/{id}/edit', [AdminController::class, 'usersEdit'])->name('admin.users.edit');
        Route::delete('/admin/users/{id}', [AdminController::class, 'usersDestroy'])->name('admin.users.destroy');
        });
// Route::middleware(['auth:web'])->group(function () {
        // Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        // Route::get('/home', [AdminController::class, 'home'])->name('home');
        
// });
