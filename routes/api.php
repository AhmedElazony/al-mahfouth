<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function () {
    Route::middleware('auth:sanctum')->group(function () {
        require __DIR__.'/v1/user.php';
        require __DIR__.'/v1/group.php';
    });

    require __DIR__.'/v1/auth.php';
});
