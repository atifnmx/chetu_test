<?php

use App\Http\Controllers\ErrorLogController;
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => 'error'], function () {
    Route::group(['prefix' => '/logs'], function () {
        Route::get('/', [ErrorLogController::class, 'getAllErrorLogs']);
        Route::get('/{id}', [ErrorLogController::class, 'getSingleErrorLog']);
        Route::post('/', [ErrorLogController::class, 'storeErrorLog']);
        Route::put('/{id}', [ErrorLogController::class, 'updateErrorLog']);
        Route::patch('/{id}', [ErrorLogController::class, 'updateErrorLogPartial']);
        Route::delete('/{id}', [ErrorLogController::class, 'destroyErrorLog']);

        Route::middleware(['webhhooktoken'])->group(function () {
            Route::post('/webhook', [ErrorLogController::class, 'storeWebhookCall']);    
        });
    });
});
