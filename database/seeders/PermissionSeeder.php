<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

//use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @todo Refactor all this to match the module permissions in agent
 * @stephen!
 */
class PermissionSeeder extends Seeder
{

    private $permissions = [];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $routelist = Route::getRoutes();

        $permissions = [];

        foreach ($routelist as $route) {

            $prefix = 'api/' . config('app.api_version');

            if ($route->getPrefix() !== $prefix) continue;

            $index       = $this->getIndex($route);
            $moduleName  = $this->getModuleName($route);

            $routeName   = $this->getRouteName($route);

            $displayName = str_replace('_', ' ',$routeName);
            $displayName = str_replace('-', ' ',$displayName);

            $parentModule = $this->getParentModuleName($route);

            $this->permissions[$parentModule][$moduleName][] = [
                'name'          => $routeName,
                'module_name'   => $moduleName,
                'display_name'  => $displayName
            ];
        }


        $this->insertPermission();

        $permissions = [
            [
                'module_name' => 'user.access.user.change-password',
                'name' => 'user.access.user.change-password',
                'display_name' => 'User Can Change Password',
            ],
            [
                'module_name' => 'user.access.user.export-activity',
                'name' => 'user.access.user.export-activity',
                'display_name' => 'User Can Export Activity in Excel Format',

            ],
            [
                'module_name' => 'user.access.user.change-announcement-status',
                'name' => 'user.access.user.change-announcement-status',
                'display_name' => 'User Can Update Announcement Status',

            ]

        ];

        Permission::factory(count($permissions))
            ->sequence(...$permissions)
            ->create();
    }



    private function insertPermission()
    {
        foreach ($this->permissions as $moduleName => $values) {

            if (Permission::where('name', $moduleName)->count()) continue;

            $module = Permission::create([
                'name'         => $moduleName,
                'module_name'  => $moduleName,
                'display_name' => $moduleName,
            ]);

            foreach ($values as $parent => $children) {

                if (Permission::where('name', $parent)->count()) continue;

                $parent = Permission::create([
                    'parent_id'    => $module->id,
                    'name'         => $parent,
                    'module_name'  => $parent,
                    'display_name' => $parent,
                ]);


                foreach ($children as $key => $child) {

                    if (Permission::where('name', $child['name'])->count()) continue;

                    Permission::create([
                        'parent_id' => $parent->id,
                        'name' => $child['name'],
                        'display_name' => $child['display_name'],
                        'module_name'  => $child['module_name'],
                    ]);
                }
            }
        }
    }



    private function getRouteName($route)
    {
        $index    = $this->getIndex($route);
        $method   = explode('@', $route->getActionName())[1];
        $routeArr = explode('.', $route->getName());
        $name     = $routeArr[$index];
        $ability  = config('abilities')[$method] ?? $method;

        if ($ability == $name) {
            return $name;
        }
        return $ability . "_" . $name;
    }

    private function getParentModuleName($route)
    {
        return explode('.', $route->getName())[0];
    }

    private function getModuleName($route)
    {
        $index = $this->getIndex($route);
        return explode('.', $route->getName())[$index];
    }



    private function getIndex($route)
    {
        $routes =  explode('.', $route->getName());
        $length = count($routes);

        return [
            '1' => 0,
            '2' => 0,
            '3' => 1,
            '4' => 2,
            '5' => 3,
            '6' => 4,
            '7' => 5
        ][$length];
    }
}
