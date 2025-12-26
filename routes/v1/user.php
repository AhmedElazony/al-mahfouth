<?php

use App\Http\Api\V1\Controllers\User\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::post('/', [UserController::class, 'createUser'])
        ->middleware('role:super_admin,admin')->name('store');
});
