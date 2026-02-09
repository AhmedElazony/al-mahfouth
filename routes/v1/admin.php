<?php

use App\Http\Api\V1\Controllers\User\Admin\StatsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'role:super_admin,admin'], function () {
	Route::get('stats', [StatsController::class, 'stats'])->name('stats');

	Route::get('groups/{group}/stats', [StatsController::class, 'showGroupStats'])
		->name('groups.stats');
});
