<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Trait\RoutePermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * @todo Refactor all this to match the module permissions in agent
 * @stephen!
 */
class PermissionSeeder extends Seeder
{
    use RoutePermission;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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


        $permissions = $this->permissions();

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

}
