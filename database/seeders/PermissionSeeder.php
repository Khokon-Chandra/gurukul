<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Permission;
use Illuminate\Database\Seeder;

//use Spatie\Permission\Models\Permission;
use App\Models\Role;

/**
 * @todo Refactor all this to match the module permissions in agent
 * @stephen!
 */
class PermissionSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions =  config('abilities')['route_permissions'];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate($permission, $permission);
        }

        $role = Role::where('name', 'Administrator')->first();

        if (! $role) {
            $role = Role::create(['name' => 'Administrator']);
            $role->departments()->sync(Department::pluck('id')->all());
        }

        if ($role) {
            $role->syncPermissions(\Spatie\Permission\Models\Permission::get()->pluck('id')->toArray());
        }
        
    }
}
