<?php

use App\Http\Api\V1\Controllers\User\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('login', [AuthController::class, 'login'])
        ->name('login');

    Route::delete('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')->name('logout');
});
