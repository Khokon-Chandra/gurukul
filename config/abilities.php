<?php


/**
 * List of default method names of the Controllers and the related permission.
 */

return [
    'unprotected_route_names' => [

    ],

    'unprotected_route_url' => [

    ],

    'route_permissions' => [


        // Epic Name: Dashboard
        'dashboard.index' =>[
            'module_name' => 'user.access.dashboards.dashboard_list',
            'name' => 'user.access.dashboards.dashboard_list',
            'display_name' => 'Can Access Dashboard',
            'group_by' => 'dashboard',
            'sort' => 1,
        ],

        // Epic Name: Users
        'users.user.index' => [
            'module_name' => 'user.access.users.user_list',
            'name' => 'user.access.users.user_list',
            'display_name' => 'Can Access User List',
            'group_by' => 'user_list',
            'sort' => 1,
        ],

        'user.change.password' => [
            'module_name' => 'user.access.users.user_list.change-password',
            'name' => 'user.access.users.user_list.change-password',
            'display_name' => 'Can Change Password',
            'group_by' => 'user_list',
            'sort' => 2,
        ],
        'users.user.store' => [
            'module_name' => 'user.access.users.user_list.create-user',
            'name' => 'user.access.users.user_list.create-user',
            'display_name' => 'Can Create User',
            'group_by' => 'user_list',
            'sort' => 3,
        ],
        'users.user.update' => [
            'module_name' => 'user.access.users.user_list.edit-user',
            'name' => 'user.access.users.user_list.edit-user',
            'display_name' => 'Can Update User',
            'group_by' => 'user_list',
            'sort' => 4,
        ],
        'users.user.destroy' => [
            'module_name' => 'user.access.users.user_list.delete-user',
            'name' => 'user.access.users.user_list.delete-user',
            'display_name' => 'Can Delete User',
            'group_by' => 'user_list',
            'sort' => 5,
        ],

        'users.roles.index' => [
            'module_name' => 'user.access.users.role',
            'name' => 'user.access.users.role',
            'display_name' => 'Can View User Role List',
            'group_by' => 'role',
            'sort' => 1,
        ],

        'users.roles.store' => [
            'module_name' => 'user.access.users.role.create',
            'name' => 'user.access.users.role.create',
            'display_name' => 'Can Create User Role',
            'group_by' => 'role',
            'sort' => 2,
        ],
        'users.roles.update' => [
            'module_name' => 'user.access.users.role.update',
            'name' => 'user.access.users.role.update',
            'display_name' => 'Can Update User Role',
            'group_by' => 'role',
            'sort' => 3,
        ],
        'users.roles.destroy' =>[
            'module_name' => 'user.access.users.role.delete',
            'name' => 'user.access.users.role.delete',
            'display_name' => 'Can Delete User Role',
            'group_by' => 'role',
            'sort' => 4,
        ],
        'users.ip.index' => [
            'module_name' => 'user.access.users.ip',
            'name' => 'user.access.users.ip',
            'display_name' => 'Can Access User IP list',
            'group_by' => 'ip',
            'sort' => 1,
        ],

        'users.ip.delete-multiple' => [
            'module_name' => 'user.access.users.ip.delete',
            'name' => 'user.access.users.ip.delete',
            'display_name' => 'Can Delete User IP',
            'group_by' => 'ip',
            'sort' => 5,
        ],
        'users.ip.multi_update' => [
            'module_name' => 'user.access.users.ip.update',
            'name' => 'user.access.users.ip.update',
            'display_name' => 'Can Update User IP',
            'group_by' => 'ip',
            'sort' => 4,
        ],
        'users.ip.store' => [
            'module_name' => 'user.access.users.ip.create',
            'name' => 'user.access.users.ip.create',
            'display_name' => 'Can Create User IP',
            'group_by' => 'ip',
            'sort' => 3,
        ],
        'users.ip.update' => [
            'module_name' => 'user.access.users.ip.update',
            'name' => 'user.access.users.ip.update',
            'display_name' => 'Can Update User IP',
            'group_by' => 'ip',
            'sort' => 4,
        ],
        'users.ip.destroy' =>[
            'module_name' => 'user.access.users.ip.delete',
            'name' => 'user.access.users.ip.delete',
            'display_name' => 'Can Delete User IP',
            'group_by' => 'ip',
            'sort' => 5,
        ],


        'users.attendances.index' => [
            'module_name' => 'user.access.users.attendance',
            'name' => 'user.access.users.attendance',
            'display_name' => 'Can Access Attendance List',
            'group_by' => 'attendance',
            'sort' => 1,
        ],

        'users.attendances.store' => [
            'module_name' => 'user.access.users.attendance.create-attendance',
            'name' => 'user.access.users.attendance.create-attendance',
            'display_name' => 'Can Create Attendance',
            'group_by' => 'attendance',
            'sort' => 2,
        ],
        'users.attendances.destroy' => [
            'module_name' => 'user.access.users.attendance.delete-attendance',
            'name' => 'user.access.users.attendance.delete-attendance',
            'display_name' => 'Can Delete Attendance',
            'group_by' => 'attendance',
            'sort' => 3,
        ],
        'users.attendances.delete_multiple' => [
            'module_name' => 'user.access.users.attendance.delete-attendance',
            'name' => 'user.access.users.attendance.delete-attendance',
            'display_name' => 'Can Delete Attendance',
            'group_by' => 'attendance',
            'sort' => 3,
        ],
        'users.attendances.update' => [
            'module_name' => 'user.access.users.attendance.update-attendance',
            'name' => 'user.access.users.attendance.update-attendance',
            'display_name' => 'Can Update Attendance',
            'group_by' => 'attendance',
            'sort' => 5,
        ],
        'users.attendances.update_multiple' => [
            'module_name' => 'user.access.users.attendance.update-attendance',
            'name' => 'user.access.users.attendance.update-attendance',
            'display_name' => 'Can Update Attendance',
            'group_by' => 'attendance',
            'sort' => 5,
        ],
        'users.activities.index' => [
            'module_name' => 'user.access.users.activity',
            'name' => 'user.access.users.activity',
            'display_name' => 'Can Access Activities List',
            'group_by' => 'activity',
            'sort' => 1,
        ],
        'users.activities.download' => [
            'module_name' => 'user.access.users.activity.export',
            'name' => 'user.access.users.activity.export',
            'display_name' => 'Can Export Activity in Excel Format',
            'group_by' => 'activity',
            'sort' => 2,
        ],

        // Epic Name: Finance
        'finance.cashflows.index' => [
            'module_name' => 'user.access.finance.cash_flow',
            'name' => 'user.access.finance.cash_flow',
            'display_name' => 'Can Access Cash Flow List',
            'group_by' => 'cash_flow',
            'sort' => 1,
        ],

        'finance.cashflows.store' =>[
            'module_name' => 'user.access.finance.cash_flow.create',
            'name' => 'user.access.finance.cash_flow.create',
            'display_name' => 'Can Create Flow',
            'group_by' => 'cash_flow',
            'sort' => 2,
        ],
        'finance.cashflows.update' => [
            'module_name' => 'user.access.finance.cash_flow.update',
            'name' => 'user.access.finance.cash_flow.update',
            'display_name' => 'Can Update Cash Flow',
            'group_by' => 'cash_flow',
            'sort' => 3,
        ],
        'finance.cashflows.update_multiple' => [
            'module_name' => 'user.access.finance.cash_flow.update',
            'name' => 'user.access.finance.cash_flow.update',
            'display_name' => 'Can Update Cash Flow',
            'group_by' => 'cash_flow',
            'sort' => 3,
        ],
        'finance.cashflows.destroy' => [
            'module_name' => 'user.access.finance.cash_flow.delete',
            'name' => 'user.access.finance.cash_flow.delete',
            'display_name' => 'Can Delete Cash Flow',
            'group_by' => 'cash_flow',
            'sort' => 4,
        ],
        'finance.cashflows.delete_multiple' => [
            'module_name' => 'user.access.finance.cash_flow.delete',
            'name' => 'user.access.finance.cash_flow.delete',
            'display_name' => 'Can Delete Cash Flow',
            'group_by' => 'cash_flow',
            'sort' => 4,
        ],

        // Epic Name: Social
        'social.users.all' => [
            'module_name' => 'user.access.social.chat',
            'name' => 'user.access.social.chat',
            'display_name' => 'Can Access Chat List',
            'group_by' => 'group',
            'sort' => 1,
        ],
        'social.groups.index' => [
            'module_name' => 'user.access.social.chat',
            'name' => 'user.access.social.chat',
            'display_name' => 'Can Access Chat List',
            'group_by' => 'group',
            'sort' => 1,
        ],
        'social.groups.members' => [
            'module_name' => 'user.access.social.chat.members',
            'name' => 'user.access.social.chat.members',
            'display_name' => 'Can Access Users Group',
            'group_by' => 'group',
            'sort' => 1,
        ],
        'social.groups.show' => [
            'module_name' => 'user.access.social.chat.show',
            'name' => 'user.access.social.chat.show',
            'display_name' => 'Can Access Users Chat Groups',
            'group_by' => 'group',
            'sort' => 2,
        ],
        'social.groups.storeChat' => [
            'module_name' => 'user.access.social.chat.store',
            'name' => 'user.access.social.chat.store',
            'display_name' => 'Can Store Chat',
            'group_by' => 'group',
            'sort' => 3,
        ],

        'social.notifications.index' => [
            'module_name' => 'user.access.social.notification',
            'name' => 'user.access.social.notification',
            'display_name' => 'Can Access Notification List',
            'group_by' => 'notification',
            'sort' => 1,
        ],

        'social.notifications.store' => [
            'module_name' => 'user.access.social.notification.create',
            'name' => 'user.access.social.notification.create',
            'display_name' => 'Can Create Notification',
            'group_by' => 'notification',
            'sort' => 2,
        ],
        'social.notifications.update' => [
            'module_name' => 'user.access.social.notification.edit',
            'name' => 'user.access.social.notification.edit',
            'display_name' => 'Can Update Notification',
            'group_by' => 'notification',
            'sort' => 3,
        ],
        'social.notifications.updateMultiple' => [
            'module_name' => 'user.access.social.notification.edit',
            'name' => 'user.access.social.notification.edit',
            'display_name' => 'Can Update Notification',
            'group_by' => 'notification',
            'sort' => 3,
        ],
        'social.notifications.destroy' => [
            'module_name' => 'user.access.social.notification.delete-notification',
            'name' => 'user.access.social.notification.delete-notification',
            'display_name' => 'Can Delete Notification',
            'group_by' => 'notification',
            'sort' => 3,
        ],
        'social.notifications.delete_multiple' => [
            'module_name' => 'user.access.social.notification.delete-notification',
            'name' => 'user.access.social.notification.delete-notification',
            'display_name' => 'Can Delete Notification',
            'group_by' => 'notification',
            'sort' => 3,
        ],
        'social.announcements.index' => [
            'module_name' => 'user.access.social.announcement',
            'name' => 'user.access.social.announcement',
            'display_name' => 'Can Access Announcement List',
            'group_by' => 'announcement',
            'sort' => 1,
        ],
        'social.announcements.update_multiple' =>[
            'module_name' => 'user.access.social.announcement.update-announcement',
            'name' => 'user.access.social.announcement.update-announcement',
            'display_name' => 'Can Update Announcement',
            'group_by' => 'announcement',
            'sort' => 3,
        ],
        'social.announcements.update' =>[
            'module_name' => 'user.access.social.announcement.update-announcement',
            'name' => 'user.access.social.announcement.update-announcement',
            'display_name' => 'Can Update Announcement',
            'group_by' => 'announcement',
            'sort' => 3,
        ],
        'social.announcements.update_status' =>[
            'module_name' => 'user.access.social.announcement.update-announcement',
            'name' => 'user.access.social.announcement.update-announcement',
            'display_name' => 'Can Update Announcement',
            'group_by' => 'announcement',
            'sort' => 3,
        ],
        'social.announcements.activated' =>[
            'module_name' => 'user.access.social.announcement',
            'name' => 'user.access.social.announcement',
            'display_name' => 'Can Access Announcement List',
            'group_by' => 'announcement',
            'sort' => 1,
        ],
        'social.announcements.store' =>[
            'module_name' => 'user.access.social.announcement.create-announcement',
            'name' => 'user.access.social.announcement.create-announcement',
            'display_name' => 'Can Create Announcement',
            'group_by' => 'announcement',
            'sort' => 2,
        ],
        'social.announcements.destroy' =>[
            'module_name' => 'user.access.social.announcement.delete',
            'name' => 'user.access.social.announcement.delete',
            'display_name' => 'Can Delete Announcement',
            'group_by' => 'announcement',
            'sort' => 4,
        ],

        'social.announcements.delete_multiple' =>[
            'module_name' => 'user.access.social.announcement.delete',
            'name' => 'user.access.social.announcement.delete',
            'display_name' => 'Can Delete Announcement',
            'group_by' => 'announcement',
            'sort' => 4,
        ],

        'users.permissions.index' =>[
            'module_name' => 'user.access.users.permissions',
            'name' => 'user.access.users.permissions',
            'display_name' => 'Can View Permissions',
            'group_by' => 'permissions',
            'sort' => 1,
        ],

        'users.permissions.store' => [
            'module_name' => 'user.access.users.permissions.store',
            'name' => 'user.access.users.permissions.store',
            'display_name' => 'Can Store Permissions',
            'group_by' => 'permissions',
            'sort' => 2,
        ],
        'users.permissions.update' => [
            'module_name' => 'user.access.users.permissions.update',
            'name' => 'user.access.users.permissions.update',
            'display_name' => 'Can Update Permissions',
            'group_by' => 'permissions',
            'sort' => 3,
        ],
    ],
];
