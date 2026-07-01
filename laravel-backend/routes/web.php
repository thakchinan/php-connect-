<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::get('/get_market_data', [ApiController::class, 'getMarketData']);
    Route::get('/get_user_state', [ApiController::class, 'getUserState']);
    Route::post('/login', [ApiController::class, 'login']);
    Route::post('/register', [ApiController::class, 'register']);
    Route::get('/logout', [ApiController::class, 'logout']);
    Route::post('/save_profile', [ApiController::class, 'saveProfile']);
    Route::post('/watchlist_toggle', [ApiController::class, 'watchlistToggle']);
    Route::post('/trade', [ApiController::class, 'trade']);
});
