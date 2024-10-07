<?php

namespace Tests\Feature\Traits;

use App\Enums\RolesEnum;
use App\Models\User;
use Database\Seeders\PermissionsAndRolesSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait SetupTrait
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase()
    {
        $this->seed([
            PermissionsAndRolesSeeder::class,
            UserSeeder::class,
        ]);
    }

    protected function user(RolesEnum $role = RolesEnum::ADMIN): User
    {
        return User::role($role->value)->firstOrFail();
    }
}
