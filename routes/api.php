<?php

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\ChallengeController;
use App\Http\Controllers\Admin\EventRuleController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TaskCategoryController;
use App\Http\Controllers\Admin\TaskPriorityController;
use App\Http\Controllers\Admin\TitleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Board\BoardColumnController;
use App\Http\Controllers\Board\BoardController;
use App\Http\Controllers\Gamification\CheckinController;
use App\Http\Controllers\Gamification\TitleSelectionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
    ->whereUuid('notification')
    ->name('notifications.read');

Route::prefix('me')->name('me.')->group(function () {
    Route::post('checkin', [CheckinController::class, 'store'])->name('checkin');
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('titles/{title}/select', [TitleSelectionController::class, 'store'])->name('titles.select');
    Route::delete('title', [TitleSelectionController::class, 'destroy'])->name('titles.clear');
});

Route::post('boards', [BoardController::class, 'store'])->name('boards.store');
Route::put('boards/{board}', [BoardController::class, 'update'])->name('boards.update');
Route::post('boards/{board}/columns', [BoardColumnController::class, 'store'])->name('columns.store');
Route::put('columns/{column}', [BoardColumnController::class, 'rename'])->name('columns.rename');
Route::put('columns/{column}/milestone', [BoardColumnController::class, 'setMilestone'])->name('columns.milestone');
Route::post('columns/{column}/move-up', [BoardColumnController::class, 'moveUp'])->name('columns.move-up');
Route::post('columns/{column}/move-down', [BoardColumnController::class, 'moveDown'])->name('columns.move-down');
Route::delete('columns/{column}', [BoardColumnController::class, 'destroy'])->name('columns.destroy');

Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::post('tasks/{task}/move', [TaskController::class, 'move'])->name('tasks.move');
Route::post('tasks/{task}/claim', [TaskController::class, 'claim'])->name('tasks.claim');
Route::post('tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
Route::post('tasks/{task}/approve', [TaskController::class, 'approve'])->name('tasks.approve');
Route::post('tasks/{task}/reject', [TaskController::class, 'reject'])->name('tasks.reject');
Route::post('tasks/{task}/homologation', [TaskController::class, 'markHomologationCompleted'])->name('tasks.homologation');
Route::post('tasks/{task}/deploy', [TaskController::class, 'markDeployed'])->name('tasks.deploy');
Route::post('tasks/{task}/archive', [TaskController::class, 'archive'])->name('tasks.archive');
Route::post('tasks/{task}/unarchive', [TaskController::class, 'unarchive'])->name('tasks.unarchive');

Route::middleware('role:admin')->group(function () {
    Route::apiResource('categories', TaskCategoryController::class)->parameters(['categories' => 'category'])->except('show');
    Route::apiResource('priorities', TaskPriorityController::class)->parameters(['priorities' => 'priority'])->except('show');
    Route::apiResource('people', PersonController::class)->parameters(['people' => 'person'])->except('show');
    Route::apiResource('users', UserController::class)->except('show');

    Route::get('event-rules', [EventRuleController::class, 'index'])->name('event-rules.index');
    Route::post('event-rules', [EventRuleController::class, 'store'])->name('event-rules.store');
    Route::put('event-rules/{type}', [EventRuleController::class, 'update'])->whereNumber('type')->name('event-rules.update');

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::put('roles/{role}/permissions/{permission}', [RoleController::class, 'togglePermission'])
        ->whereNumber('permission')
        ->name('roles.permissions.toggle');

    Route::apiResource('titles', TitleController::class)->except('show');
    Route::post('titles/{title}/toggle-active', [TitleController::class, 'toggleActive'])->name('titles.toggle-active');

    Route::apiResource('achievements', AchievementController::class)->except('show');
    Route::post('achievements/{achievement}/toggle-active', [AchievementController::class, 'toggleActive'])->name('achievements.toggle-active');
    Route::post('achievements/{achievement}/duplicate', [AchievementController::class, 'duplicate'])->name('achievements.duplicate');

    Route::apiResource('challenges', ChallengeController::class)->except('show');
    Route::post('challenges/{challenge}/toggle-active', [ChallengeController::class, 'toggleActive'])->name('challenges.toggle-active');
    Route::post('challenges/{challenge}/duplicate', [ChallengeController::class, 'duplicate'])->name('challenges.duplicate');

    Route::put('settings', [AppSettingController::class, 'update'])->name('settings.update');
});
