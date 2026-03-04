<?php

use App\Http\Api\V1\Controllers\Tahfidh\Groups\GroupController;
use App\Http\Api\V1\Controllers\Tahfidh\Groups\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'groups', 'as' => 'groups.'], function () {
    Route::get('/', [GroupController::class, 'index'])
        ->middleware('role:super_admin,admin,teacher')->name('index');

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

    Route::get('/{group}/students/{studentId}', [GroupController::class, 'showStudent'])
        ->middleware('role:super_admin,admin,teacher')->name('students.show');

    Route::post('/{group}/students', [GroupController::class, 'assignStudent'])
        ->middleware('role:super_admin,admin,teacher')->name('assign-student');

    Route::put('/{group}/students/profile', [GroupController::class, 'updateStudentProfile'])
        ->middleware('role:super_admin,admin,teacher')->name('update-student-profile');

    Route::put('/{group}/students/{student}', [GroupController::class, 'updateAssignedStudent'])
        ->middleware('role:super_admin,admin,teacher')->name('update-student');

    Route::delete('/{group}/students/{student}', [GroupController::class, 'removeStudent'])
        ->middleware('role:super_admin,admin,teacher')->name('remove-student');

    // Reports
    Route::group(['prefix' => '/{group}/reports', 'as' => 'reports.'], static function () {
        Route::get('/', [ReportController::class, 'index'])
            ->middleware('role:super_admin,admin,teacher')->name('index');

        Route::get('/{report}', [ReportController::class, 'show'])
            ->middleware('role:super_admin,admin,teacher')->name('show');

        Route::post('/', [ReportController::class, 'store'])
            ->middleware('role:super_admin,admin,teacher')->name('store');

        Route::put('/{report}', [ReportController::class, 'update'])
            ->middleware('role:super_admin,admin,teacher')->name('update');

        Route::delete('/{report}', [ReportController::class, 'destroy'])
            ->middleware('role:super_admin,admin,teacher')->name('destroy');
    });
});
