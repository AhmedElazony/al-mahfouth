<?php

namespace Database\Seeders;

use App\Domains\User\Enums\UserGendersEnum;
use App\Domains\User\Enums\UserRolesEnum;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Domains\User\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed a super admin user
        $superAdmin = User::factory()->create([
            'name' => 'Admin User',
            'username' => 'superAdmin',
            'email' => 'admin@al-mahfouth.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // TODO: Change this to a secure password in production.
            'role' => UserRolesEnum::SUPER_ADMIN->value,
            'gender' => UserGendersEnum::MALE->value,
        ]);
    }
}
