<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\CashflowController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserIpController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => ['auth:api']], function () {

    /**
     * Service module routes
     */
    Route::name('service.')->group(function () {
        Route::get('departments', [DepartmentController::class, 'index'])
            ->name('departments.index');
    });


    /**
     * User Routes
     */
    Route::name('user.')->group(function () {
        Route::put('change-password/{user}', [UserController::class, 'changePassword'])
            ->name('change.password');
    });


    /**
     * user module routes
     */
    Route::name('users.')->group(function () {
        Route::apiResource('ip', UserIpController::class)->except('show');
        Route::delete('user-ip-delete-multiple', [UserIpController::class, 'deleteMultiple'])->name('ip.delete-multiple');
        Route::put('/user-ips', [UserIpController::class, 'multiUpdate'])
            ->name('ip.multi_update');
        Route::apiResource('roles', RoleController::class);
        Route::get('activities', [ActivityLogController::class, 'index'])->name('activities.index');
        Route::get('activities/download', [ActivityLogController::class, 'download'])->name('activities.download');
        Route::apiResource('permissions', PermissionController::class)
            ->only('index', 'update');
        Route::apiResource('attendances', AttendanceController::class);
        Route::patch('attendances', [AttendanceController::class, 'updateMultiple'])
            ->name('attendances.update_multiple');
        Route::delete('attendances-delete-many', [AttendanceController::class, 'deleteMultiple'])
            ->name('attendances.delete_multiple');

        Route::apiResource('user', UserController::class);

        Route::put('user-update/{user}', [UserController::class, 'updateUser'])->name('update.user');
        Route::post('create-user', [UserController::class, 'storeUser'])->name('user.store');
        Route::delete('delete-user', [UserController::class, 'deleteUser'])->name('delete.user');
    });

    Route::name('user.')->group(function(){
        Route::put('change-password/{user}', [UserController::class, 'changePassword'])
            ->name('change.password');
    });

    /**
     * Finance routes
     */
    Route::name('finance.')->group(function () {
        Route::apiResource('cashflows', CashflowController::class);
        Route::patch('cashflows', [CashflowController::class, 'updateMultiple'])->name('cashflows.update_multiple');
        Route::delete('cashflows-delete-many', [CashflowController::class, 'deleteMultiple'])->name('cashflows.delete_multiple');
    });


    /**
     * Social module routes
     */
    Route::name('social.')->group(function () {
        // group chat route
        Route::get('groups', [GroupController::class, 'index'])->name('groups.index');
        Route::get('groups/{group}', [GroupController::class, 'show'])->name('groups.show');
        Route::get('groups/{group}/members', [GroupController::class, 'members'])->name('groups.members');
        Route::post('groups/{group}', [GroupController::class, 'storeChat'])->name('groups.storeChat');
        Route::get('all-users', [UserController::class, 'allUser'])->name('users.all');

        // notification routes
        Route::apiResource('notifications', NotificationController::class);
        Route::patch('notifications', [NotificationController::class, 'updateMultiple'])->name('notifications.updateMultiple');
        Route::delete('notifications-delete-many', [NotificationController::class, 'deleteMultiple'])->name('notifications.delete_multiple');

        // announcements
        Route::apiResource('announcements', AnnouncementController::class);
        Route::put('announcements-update-multiple', [AnnouncementController::class, 'updateMultiple'])->name('announcements.update_multiple');
        Route::patch('update-announcement-status', [AnnouncementController::class, 'updateStatus'])->name('announcements.update_status');
        Route::get('get-announcement-data', [AnnouncementController::class, 'getData'])->name('announcements.data');
        Route::delete('/announcements-delete-multiple', [AnnouncementController::class, 'deleteMultiple'])->name('announcements.delete_multiple');
        Route::get('activated-announcement', [AnnouncementController::class, 'activated'])->name('announcements.activated');
    });

});
