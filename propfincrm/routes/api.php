<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Api\v1\AuthController;

Route::prefix('v1')->group(function () {
    // Public Authentication routes
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.v1.auth.login');

    // Protected routes requiring authentication & tenant context
    Route::middleware(['api.tenant'])->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/switch-company', [AuthController::class, 'switchCompany'])->name('api.v1.auth.switch-company');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
    });
});

