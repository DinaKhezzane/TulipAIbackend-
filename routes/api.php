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

use App\Http\Controllers\OutflowController;
use App\Http\Controllers\DashboardMetricsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InflowController;
use App\Http\Controllers\CashFlowController;
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




Route::post('/reports/generate', [ReportController::class, 'generateReport']);



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



Route::get('/company_info/{token}', [InvitationController::class, 'getCompanyInfo']);


Route::post('/employee_signup', [InvitationController::class, 'storeEmployee']);
    


Route::post('/outflow', [OutflowController::class, 'store'])
    ->middleware(VerifyToken::class);

// Add the route in your routes/api.php
Route::get('/outflow-categories', [OutflowController::class, 'getOutflowCategories']);
Route::get('/getOutflows', [OutflowController::class, 'getAllOutflows'])->middleware(VerifyToken::class);;



Route::post('/inflow', [InflowController::class, 'store'])->middleware(VerifyToken::class);; // Store inflow
Route::get('/inflow-categories', [InflowController::class, 'getInflowCategories']); // Get inflow categories
Route::get('/inflows', [InflowController::class, 'getAllInflows'])->middleware(VerifyToken::class);; // Get all inflows for a company
Route::get('/dashboard/metrics', [DashboardMetricsController::class, 'index'])->middleware(VerifyToken::class);; // Get all inflows for a company



Route::get('/cash-flow', [CashFlowController::class, 'getCashFlowData'])->middleware(VerifyToken::class);; 
Route::get('/inflow-category', [CashFlowController::class, 'getInflowsByCategories'])->middleware(VerifyToken::class);; 

Route::get('/outflow-category', [CashFlowController::class, 'getOutflowsByCategories'])->middleware(VerifyToken::class);; 
