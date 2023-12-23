<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\CashflowController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserIpController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => ['auth:api']], function () {
    /**
     * Admin module routes
     */
    Route::name('admin.')->group(function () {
        Route::apiResource('user-ip', UserIpController::class);
        Route::delete('user-ip-delete-multiple',[UserIpController::class,'deleteMultiple'])
            ->name('user-ip.delete-multiple');
        Route::put('/user-ips', [UserIpController::class, 'multiUpdate'])
            ->name('user-ip.multi_update');
        Route::apiResource('roles', RoleController::class);
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/download', [ActivityLogController::class, 'download'])
            ->name('logs.download');
        Route::apiResource('user', UserController::class);


        Route::post('create-user', [UserController::class, 'storeUser'])->name('user.store')
            ->middleware('permission:user.access.users.user_list.create-user');

        Route::delete('delete-user', [UserController::class, 'deleteUser'])->name('delete.user')
        ->middleware('permission:user.access.users.user_list.delete-user');


        Route::apiResource('permissions', PermissionController::class)
            ->only('index', 'update');
        Route::apiResource('attendances', AttendanceController::class);
        Route::patch('attendances',[AttendanceController::class,'updateMultiple'])
            ->name('attendances.update_multiple');
        Route::delete('attendances-delete-many', [AttendanceController::class,'deleteMultiple'])
            ->name('attendances.delete_multiple');
    });

    /**
     * Service module routes
     */
    Route::name('service.')->group(function () {
        Route::apiResource('announcements',AnnouncementController::class);
        Route::put('announcements-update-multiple', [AnnouncementController::class, 'updateMultiple'])
            ->name('announcements.update_multiple');
        Route::patch('update-announcement-status', [AnnouncementController::class, 'updateStatus'])
            ->name('announcements.update_status');
        Route::get('get-announcement-data', [AnnouncementController::class, 'getData'])
            ->name('get.announcement.data');
        Route::delete('/announcements-delete-multiple',[AnnouncementController::class,'deleteMultiple'])
            ->name('announcements.delete_multiple');
        Route::get('activated-announcement',[AnnouncementController::class,'activated'])
            ->name('announcements.activate');

        Route::apiResource('cashflows', CashflowController::class);
        Route::patch('cashflows',[CashflowController::class,'updateMultiple'])
            ->name('cashflows.update_multiple');
        Route::delete('cashflows-delete-many', [CashflowController::class,'deleteMultiple'])
            ->name('cashflows.delete_multiple');
        Route::apiResource('notifications',NotificationController::class);
        Route::patch('notifications',[NotificationController::class,'updateMultiple'])
            ->name('notifications.updateMultiple');
        Route::delete('notifications-delete-many',[NotificationController::class,'deleteMultiple'])
            ->name('notifications.delete_multiple');
        Route::get('departments',[DepartmentController::class,'index'])
            ->name('departments.index');

        Route::get('groups',[GroupController::class,'index'])->name('groups.index');
        Route::get('groups/{group}',[GroupController::class,'show'])->name('groups.show');
        Route::get('groups/{group}/members',[GroupController::class,'members'])->name('groups.members');
        Route::post('groups/{group}',[GroupController::class,'storeChat'])->name('groups.storeChat');
        Route::get('all-users',[UserController::class,'allUser'])->name('users.all');
    });

    /**
     * User Routes
     */
    Route::name('user.')->group(function () {
        Route::put('change-password', [UserController::class, 'changePassword'])
            ->name('change.password');
    });
});
