<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReceivingInspectionApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/user-sync', [ApiController::class, 'syncUser']);
Route::post('/user-desync', [ApiController::class, 'desyncUser']);
Route::get('/heartbeat', [AuthController::class, 'check']);
Route::post('/password-change', [ApiController::class, 'passwordChange']); 
Route::post('/activation', [ApiController::class, 'activation']);
Route::post('/plant-sync', [ApiController::class, 'syncPlant']);
Route::post('/sso/logout', [\App\Http\Controllers\SsoLoginController::class, 'ssoLogout']);

Route::middleware('auth:sanctum')->prefix('inspections')->name('api.inspections.')->group(function () {
    Route::get('/', [ReceivingInspectionApiController::class, 'index'])->name('index');
    Route::post('/', [ReceivingInspectionApiController::class, 'store'])->name('store');
    Route::get('/export', [ReceivingInspectionApiController::class, 'export'])->name('export');
    Route::get('/{inspection}/audit', [ReceivingInspectionApiController::class, 'audit'])->name('audit');
    Route::patch('/{inspection}/production', [ReceivingInspectionApiController::class, 'updateProduction'])->name('production.update');
    Route::patch('/{inspection}/qc', [ReceivingInspectionApiController::class, 'updateQc'])->name('qc.update');
    Route::post('/{inspection}/production/submit', [ReceivingInspectionApiController::class, 'submitProduction'])->name('production.submit');
    Route::post('/{inspection}/qc/submit', [ReceivingInspectionApiController::class, 'submitQc'])->name('qc.submit');
    Route::post('/{inspection}/reopen', [ReceivingInspectionApiController::class, 'reopen'])->name('reopen');
    Route::post('/{inspection}/sections/{section}/reopen', [ReceivingInspectionApiController::class, 'reopen'])->name('sections.reopen');
    Route::delete('/{inspection}', [ReceivingInspectionApiController::class, 'destroy'])->name('destroy');
    Route::get('/{inspection}', [ReceivingInspectionApiController::class, 'show'])->name('show');
});
