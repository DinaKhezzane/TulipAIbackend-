<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ExpenseController;

use App\Http\Controllers\UserProfileController;

use App\Http\Middleware\VerifyToken;
use App\Http\Controllers\InvitationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are typically stateless and are prefixed with the "api" prefix.
|
*/
// Route::post('/managers', [ManagerController::class, 'create']);

Route::get('/expenses', [ExpenseController::class, 'index']);
Route::post('/api/expenses', [ExpenseController::class, 'store']);
Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);


Route::get('/categories', [CategoriesController::class, 'index']);

Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['api'])->group(function () {
    Route::post('/managers', [ManagerController::class, 'create']);
});

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);



Route::get('/roles', [RoleController::class, 'index']);

Route::get('/user-profile', [UserProfileController::class, 'getUserProfile'])
    ->middleware(VerifyToken::class);

    

Route::post('/invite-employee', [InvitationController::class, 'inviteEmployee'])
    ->middleware(VerifyToken::class);

