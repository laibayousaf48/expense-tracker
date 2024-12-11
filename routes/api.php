<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout']);

// Password reset routes
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
Route::post('/password/reset/confirm', [AuthController::class, 'confirmReset']);

// Profile update route
Route::middleware(['auth:sanctum'])->post('/profile/update', [AuthController::class, 'updateProfile']);



Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
    Route::post('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
    // Route::get('/analytics', [ExpenseController::class, 'analytics']);
    Route::post('/incomes', [IncomeController::class, 'store']);
    Route::get('/expenses/date/{date}', [ExpenseController::class, 'getExpensesByDate']);
});

Route::middleware('auth:sanctum')->post('/budgets', [BudgetController::class, 'store']);
// Route::middleware('auth:sanctum')->get('/budgets', [BudgetController::class, 'index']);


Route::middleware(['auth:sanctum', 'month.year'])->group(function () {
    Route::get('/incomes', [IncomeController::class, 'index']);  //done
    Route::get('/expenses', [ExpenseController::class, 'index']);  //done
    Route::get('/analytics', [ExpenseController::class, 'analytics']);  //done
    Route::get('/summary', [IncomeController::class, 'summary']);   //done
    Route::get('/budgets', [BudgetController::class, 'index']);  //done
});