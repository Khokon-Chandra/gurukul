<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::updateOrCreate(['name' => 'Administrator'], ['name' => 'Administrator']);

        $role->departments()->sync(Department::pluck('id')->all());

        $role->syncPermissions(Permission::get()->pluck('id')->toArray());
    }
}
