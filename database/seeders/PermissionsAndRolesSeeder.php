<?php

namespace Database\Seeders;

use App\Enums\Permission\AccountEnum;
use App\Enums\Permission\CategoryEnum;
use App\Enums\Permission\OrderEnum;
use App\Enums\Permission\ProductEnum;
use App\Enums\Permission\UserEnum;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            ...AccountEnum::values(),
            ...CategoryEnum::values(),
            ...ProductEnum::values(),
            ...OrderEnum::values(),
            ...UserEnum::values(),
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        if (!Role::where('name', RolesEnum::CUSTOMER->value)->exists()) {
            Role::create(['name' => RolesEnum::CUSTOMER->value])
                ->givePermissionTo(AccountEnum::values());
        }

        if (!Role::where('name', RolesEnum::MODERATOR->value)->exists()) {
            Role::create(['name' => RolesEnum::MODERATOR->value])
                ->givePermissionTo([
                    ...CategoryEnum::values(),
                    ...ProductEnum::values(),
                ]);
        }

        if (!Role::where('name', RolesEnum::ADMIN->value)->exists()) {
            Role::create(['name' => RolesEnum::ADMIN->value])
                ->givePermissionTo(Permission::all());
        }
    }
}
