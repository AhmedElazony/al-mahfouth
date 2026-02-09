<?php

use App\Http\Api\V1\Controllers\User\Admin\UserController;
use App\Http\Api\V1\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('/', [UserController::class, 'index'])
        ->middleware('role:super_admin,admin,teacher')->name('index');

    Route::get('/{user}', [UserController::class, 'show'])
        ->name('show');

    Route::post('/', [UserController::class, 'store'])
        ->middleware('role:super_admin,admin')->name('store');

    Route::put('/{user}', [UserController::class, 'update'])
        ->middleware('role:super_admin,admin')->name('update');

    Route::delete('/{user}', [UserController::class, 'destroy'])
        ->middleware('role:super_admin,admin')->name('destroy');

});

Route::put('user/profile', [ProfileController::class, 'update'])
    ->name('user.profile.update');
