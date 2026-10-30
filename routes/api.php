<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;
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



