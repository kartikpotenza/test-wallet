<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\WalletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Register User
 * endpoint: http://{domain}/api/auth/register
 * returns Bearer Token
 */
Route::post('/auth/register', [AuthController::class, 'register']);

/**
 * Register User
 * endpoint: http://{domain}/api/auth/login
 * returns Bearer Token
 */
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {

    /**
     * Add credit/balance to Wallet
     * endpoint: http://{domain}/api/wallet/add
     * returns Response
     */
    Route::post('/wallet/add', [WalletController::class, 'add']);

    /**
     * Pay from Wallet
     * endpoint: http://{domain}/api/wallet/pay
     * returns Response
     */
    Route::post('/wallet/buycookie', [WalletController::class, 'buycookie']);
});