<?php

namespace App\Support\Providers;

use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $services = [
            \App\Domains\User\Services\Contracts\UserServiceInterface::class => \App\Domains\User\Services\Database\UserService::class,
        ];

        foreach ($services as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }

    public function boot(): void {}
}
