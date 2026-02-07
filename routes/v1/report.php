<?php

use App\Http\Api\V1\Controllers\Tahfidh\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'groups/{group}/reports', 'as' => 'reports.'], function () {
    Route::get('/', [ReportController::class, 'index'])
        ->middleware('role:super_admin,admin')->name('index');

    Route::get('/{report}', [ReportController::class, 'show'])
        ->middleware('role:super_admin,admin,teacher')->name('show');

    Route::post('/', [ReportController::class, 'store'])
        ->middleware('role:super_admin,admin,teacher')->name('store');

    Route::put('/{report}', [ReportController::class, 'update'])
        ->middleware('role:super_admin,admin,teacher')->name('update');

    Route::delete('/{report}', [ReportController::class, 'destroy'])
        ->middleware('role:super_admin,admin,teacher')->name('destroy');
});
