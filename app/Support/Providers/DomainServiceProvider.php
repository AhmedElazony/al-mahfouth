<?php

namespace App\Support\Providers;

use App\Domains\Tahfidh\Services\Contracts\GroupService as GroupServiceContract;
use App\Domains\Tahfidh\Services\Contracts\ReportService as ReportServiceContract;
use App\Domains\Tahfidh\Services\Database\GroupService;
use App\Domains\Tahfidh\Services\Database\ReportService;
use App\Domains\User\Services\Contracts\UserService as UserServiceContract;
use App\Domains\User\Services\Database\UserService;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $services = [
            UserServiceContract::class => UserService::class,
            GroupServiceContract::class => GroupService::class,
            ReportServiceContract::class => ReportService::class,
        ];

        foreach ($services as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }

    public function boot(): void {}
}
