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

            $index = $this->getIndex($route);
            $moduleName = $this->getModuleName($route);

            $routeName = $this->getRouteName($route);

            $displayName = str_replace('_', ' ', $routeName);
            $displayName = str_replace('-', ' ', $displayName);

            $parentModule = $this->getParentModuleName($route);

            $this->permissions[$parentModule][$moduleName][] = [
                'name' => $routeName,
                'module_name' => $moduleName,
                'display_name' => $displayName
            ];
        }


        // $this->insertPermission();

        $permissions = [

            [
                'module_name' => 'user.access.users.user_list',
                'name' => 'user.access.users.user_list',
                'display_name' => 'Display User user_list',
                'group_by' => 'user_list',
                'sort' => 1,
            ],
            [
                'module_name' => 'user.access.users.user_list.change-password',
                'name' => 'user.access.users.user_list.change-password',
                'display_name' => 'User Can Change Password',
                'group_by' => 'user_list',
                'sort' => 2,
            ],
            [
                'module_name' => 'user.access.users.user_list.create-user',
                'name' => 'user.access.users.user_list.create-user',
                'display_name' => 'Create User',
                'group_by' => 'user_list',
                'sort' => 3,
            ],
            [
                'module_name' => 'user.access.users.user_list.edit-user',
                'name' => 'user.access.users.user_list.edit-user',
                'display_name' => 'Edit User',
                'group_by' => 'user_list',
                'sort' => 4,
            ],
            [
                'module_name' => 'user.access.users.user_list.delete-user',
                'name' => 'user.access.users.user_list.delete-user',
                'display_name' => 'Delete User',
                'group_by' => 'user_list',
                'sort' => 5,
            ],
            [
                'module_name' => 'user.access.users.activity',
                'name' => 'user.access.users.activity',
                'display_name' => 'User Can See Activities',
                'group_by' => 'activity',
                'sort' => 1,
            ],
            [
                'module_name' => 'user.access.users.activity.export',
                'name' => 'user.access.users.activity.export',
                'display_name' => 'User Can Export Activity in Excel Format',
                'group_by' => 'activity',
                'sort' => 2,
            ],
            [
                'module_name' => 'user.access.users.role',
                'name' => 'user.access.users.role',
                'display_name' => 'Can View User Role List',
                'group_by' => 'role',
                'sort' => 1,
            ],
            [
                'module_name' => 'user.access.users.role.create',
                'name' => 'user.access.users.role.create',
                'display_name' => 'Can Create User Role',
                'group_by' => 'role',
                'sort' => 2,
            ],
            [
                'module_name' => 'user.access.users.role.update',
                'name' => 'user.access.users.role.update',
                'display_name' => 'Can Update User Role',
                'group_by' => 'role',
                'sort' => 3,
            ],
            [
                'module_name' => 'user.access.users.role.delete',
                'name' => 'user.access.users.role.delete',
                'display_name' => 'Can Delete User Role',
                'group_by' => 'role',
                'sort' => 4,
            ],
            [
                'module_name' => 'user.access.users.ip',
                'name' => 'user.access.users.ip',
                'display_name' => 'Can View User IP list',
                'group_by' => 'ip',
                'sort' => 1,
            ],
            [
                'module_name' => 'user.access.users.ip.perform-ip-tasks',
                'name' => 'user.access.users.perform-ip-tasks',
                'display_name' => 'Perform User Ip Related Tasks',
                'group_by' => 'ip',
                'sort' => 2,
            ],
            [
                'module_name' => 'user.access.users.ip.create',
                'name' => 'user.access.users.ip.create',
                'display_name' => 'Create User IP',
                'group_by' => 'ip',
                'sort' => 3,
            ],
            [
                'module_name' => 'user.access.users.ip.update',
                'name' => 'user.access.users.ip.update',
                'display_name' => 'Update User IP',
                'group_by' => 'ip',
                'sort' => 4,
            ],
            [
                'module_name' => 'user.access.users.ip.delete',
                'name' => 'user.access.users.ip.delete',
                'display_name' => 'Delete User IP',
                'group_by' => 'ip',
                'sort' => 5,
            ],
            [
                'module_name' => 'user.access.users.attendance',
                'name' => 'user.access.users.attendance',
                'display_name' => 'List Attendance',
                'group_by' => 'attendance',
                'sort' => 1,
            ],
            [
                'module_name' => 'user.access.users.attendance.create-attendance',
                'name' => 'user.access.users.attendance.create-attendance',
                'display_name' => 'Create Attendance',
                'group_by' => 'attendance',
                'sort' => 2,
            ],
            [
                'module_name' => 'user.access.users.attendance.delete-attendance',
                'name' => 'user.access.users.attendance.delete-attendance',
                'display_name' => 'Delete Attendance',
                'group_by' => 'attendance',
                'sort' => 3,
            ],
            [
                'module_name' => 'user.access.attendance.update-attendance',
                'name' => 'user.access.attendance.update-attendance',
                'display_name' => 'Update Attendance',
                'group_by' => 'attendance',
                'sort' => 4,
            ],
            [
                'module_name' => 'user.access.finance.cash_flow',
                'name' => 'user.access.finance.cash_flow',
                'display_name' => 'See Cash Flow List',
                'group_by' => 'cash_flow',
                'sort' => 1,
            ],
            [
                'module_name' => 'user.access.finance.cash_flow.create',
                'name' => 'user.access.finance.cash_flow.create',
                'display_name' => 'Can Create Flow',
                'group_by' => 'cash_flow',
                'sort' => 2,
            ],
            [
                'module_name' => 'user.access.finance.cash_flow.update',
                'name' => 'user.access.finance.cash_flow.update',
                'display_name' => 'Can Update Cash Flow',
                'group_by' => 'cash_flow',
                'sort' => 3,
            ],
            [
                'module_name' => 'user.access.finance.cash_flow.delete',
                'name' => 'user.access.finance.cash_flow.delete',
                'display_name' => 'Can Delete Cashflow',
                'group_by' => 'cash_flow',
                'sort' => 4,
            ],
            [

                'module_name' => 'user.access.social.chat',
                'name' => 'user.access.social.chat',
                'display_name' => 'User Chat',
                'group_by' => 'chat',
                'sort' => 1,
            ],
            [

                'module_name' => 'user.access.social.notification',
                'name' => 'user.access.social.notification',
                'display_name' => 'Notification List',
                'group_by' => 'notification',
                'sort' => 1,
            ],
            [

                'module_name' => 'user.access.social.notification.create',
                'name' => 'user.access.social.notification.create',
                'display_name' => 'User Can Create Notification',
                'group_by' => 'notification',
                'sort' => 2,
            ],
            [
                'module_name' => 'user.access.social.notification.edit',
                'name' => 'user.access.social.notification.edit',
                'display_name' => 'User Can Edit Notification',
                'group_by' => 'notification',
                'sort' => 3,
            ],
            [

                'module_name' => 'user.access.social.announcement',
                'name' => 'user.access.social.announcement',
                'display_name' => 'Announcement List',
                'group_by' => 'announcement',
                'sort' => 1,
            ],
            [

                'module_name' => 'user.access.social.announcement.update-announcement-status',
                'name' => 'user.access.social.announcement.update-announcement-status',
                'display_name' => 'User Can Update Announcement Status',
                'group_by' => 'announcement',
                'sort' => 2,
            ],
            [
                'module_name' => 'user.access.social.announcement.view-announcement-data',
                'name' => 'user.access.social.announcement.view-announcement-data',
                'display_name' => 'User Can View Announcement Data',
                'group_by' => 'announcement',
                'sort' => 3,
            ],
        ];

        $this->insertPermission();

        foreach ($permissions as $permission) {
            Permission::updateOrCreate($permission, $permission);
        }

        $role = Role::where('name', 'Administrator')->first();

        if (! $role) {
            $role = Role::create(['name' => 'Administrator']);
        }

        if ($role) {
            $role->syncPermissions(\Spatie\Permission\Models\Permission::get()->pluck('id')->toArray());
        }
    }


    private function insertPermission()
    {
        foreach ($this->permissions as $moduleName => $values) {

            if (Permission::where('name', $moduleName)->count()) continue;

            $module = Permission::updateOrCreate([
                'name' => $moduleName,
                'module_name' => $moduleName,
                'display_name' => $moduleName,
            ], [
                'name' => $moduleName,
                'module_name' => $moduleName,
                'display_name' => $moduleName,
            ]);

            foreach ($values as $parent => $children) {

                if (Permission::where('name', $parent)->count()) continue;

                $parent = Permission::updateOrCreate([
                    'parent_id' => $module->id,
                    'name' => $parent,
                    'module_name' => $parent,
                    'display_name' => $parent,
                ], [
                    'parent_id' => $module->id,
                    'name' => $parent,
                    'module_name' => $parent,
                    'display_name' => $parent,
                ]);


                foreach ($children as $key => $child) {

                    if (Permission::where('name', $child['name'])->count()) continue;

                    Permission::updateOrCreate([
                        'parent_id' => $parent->id,
                        'name' => $child['name'],
                        'display_name' => $child['display_name'],
                        'module_name' => $child['module_name'],
                    ], [
                        'parent_id' => $parent->id,
                        'name' => $child['name'],
                        'display_name' => $child['display_name'],
                        'module_name' => $child['module_name'],
                    ]);
                }
            }
        }
    }


    private function getRouteName($route)
    {
        $index = $this->getIndex($route);
        $method = explode('@', $route->getActionName())[1];
        $routeArr = explode('.', $route->getName());
        $name = $routeArr[$index];
        $ability = config('abilities')[$method] ?? $method;

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
        $routes = explode('.', $route->getName());
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
