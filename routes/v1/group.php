<?php

use App\Http\Api\V1\Controllers\Tahfidh\GroupController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'groups', 'as' => 'groups.'], function () {
    Route::get('/', [GroupController::class, 'index'])
        ->middleware('role:super_admin,admin')->name('index');

    Route::get('/{group}', [GroupController::class, 'show'])
        ->name('show');

    Route::post('/', [GroupController::class, 'store'])
        ->middleware('role:super_admin,admin')->name('store');

    Route::put('/{group}', [GroupController::class, 'update'])
        ->middleware('role:super_admin,admin')->name('update');

    Route::delete('/{group}', [GroupController::class, 'destroy'])
        ->middleware('role:super_admin,admin')->name('destroy');

    Route::get('/{group}/students', [GroupController::class, 'getStudents'])
        ->middleware('role:super_admin,admin,teacher')->name('students');

    Route::post('/{group}/students', [GroupController::class, 'assignStudent'])
        ->middleware('role:super_admin,admin,teacher')->name('assign-student');

    Route::delete('/{group}/students/{student}', [GroupController::class, 'removeStudent'])
        ->middleware('role:super_admin,admin,teacher')->name('remove-student');
});
