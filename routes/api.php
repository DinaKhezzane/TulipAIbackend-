<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\CategoriesController;
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

Route::get('/categories', [CategoriesController::class, 'index']);

Route::middleware(['api'])->group(function () {
    Route::post('/managers', [ManagerController::class, 'create']);
});
