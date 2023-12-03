<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\CashflowController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserIpController;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => ['auth:api']], function () {
    /**
     * Admin module routes
     */
    Route::name('admin.')->group(function () {
        Route::apiResource('user-ip', UserIpController::class)
            ->middleware('permission:user.access.user.perform-ip-tasks');
        Route::put('/user-ips', [UserIpController::class, 'multiUpdate'])
            ->name('user-ip.multi_update')->middleware('permission:user.access.user.perform-ip-tasks');
        Route::apiResource('roles', RoleController::class);
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/download', [ActivityLogController::class, 'download'])->name('logs.download');
        Route::apiResource('user', UserController::class);
        Route::get('export-and-download-activity', [ActivityLogController::class, 'exportActivity'])->name('download.activity')->middleware('permission:user.access.user.export-activity');
        Route::apiResource('permissions', PermissionController::class)->only('index', 'update');
        Route::apiResource('attendances',AttendanceController::class);
    });



    /**
     * Service module routes
     */
    Route::name('service.')->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('announcements', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::patch('update-announcement-status', [AnnouncementController::class, 'updateAnAnnouncementStatus'])->name('announcement.status.update')->middleware('permission:user.access.user.update-announcement-status');
        Route::delete('announcements', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::get('get-announcement-data', [AnnouncementController::class, 'getData'])->name('get.announcement.data')->middleware('permission:user.access.user.view-announcement-data');
        Route::apiResource('cashflows', CashflowController::class);
        Route::delete('cashflows-delete-many', [CashflowController::class,'deleteMany'])->name('cashflows.delete_many');
        Route::apiResource('notifications',NotificationController::class);
    });


    /**
     * User Routes
     */

    Route::name('user.')->group(function () {

        Route::apiResource('chats', ChatController::class)
            ->middleware('permission:user.access.user.chat-agent');

        Route::put('change-password', [UserController::class, 'changePassword'])
            ->name('change.password')
            ->middleware('permission:user.access.user.change-password');
    });
});
