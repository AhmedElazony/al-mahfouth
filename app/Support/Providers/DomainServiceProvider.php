<?php

namespace App\Support\Providers;

use App\Domains\Tahfidh\Services\Contracts\GroupServiceInterface;
use App\Domains\Tahfidh\Services\Database\GroupService;
use App\Domains\User\Services\Contracts\UserServiceInterface;
use App\Domains\User\Services\Database\UserService;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $services = [
            UserServiceInterface::class => UserService::class,
            GroupServiceInterface::class => GroupService::class,
        ];

        foreach ($services as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }

    public function boot(): void {}
}
