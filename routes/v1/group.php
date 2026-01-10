<?php

use App\Http\Api\V1\Controllers\Tahfidh\GroupController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'groups', 'as' => 'groups.'], function () {
    Route::get('/', [GroupController::class, 'index'])
        ->middleware('role:super_admin,admin')->name('index');

    Route::get('/{group}', [GroupController::class, 'show'])
        ->name('show');
});
