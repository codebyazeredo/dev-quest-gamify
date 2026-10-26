<?php

use App\Http\Controllers\Admin\AchievementPageController;
use App\Http\Controllers\Admin\AdminDashboardPageController;
use App\Http\Controllers\Admin\AdminHubPageController;
use App\Http\Controllers\Admin\AppSettingPageController;
use App\Http\Controllers\Admin\ChallengePageController;
use App\Http\Controllers\Admin\EventRulePageController;
use App\Http\Controllers\Admin\PersonPageController;
use App\Http\Controllers\Admin\RolePageController;
use App\Http\Controllers\Admin\TaskCategoryPageController;
use App\Http\Controllers\Admin\TaskPriorityPageController;
use App\Http\Controllers\Admin\TitlePageController;
use App\Http\Controllers\Admin\UserPageController;
use App\Http\Controllers\Board\BoardPageController;
use App\Http\Controllers\Gamification\CheckinPageController;
use App\Http\Controllers\Gamification\RankingPageController;
use App\Http\Controllers\Gamification\UserAchievementPageController;
use App\Http\Controllers\Gamification\UserChallengePageController;
use App\Http\Controllers\Gamification\UserTitlePageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Profile\DashboardPageController;
use App\Http\Controllers\Task\TaskPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardPageController::class, 'index'])->name('dashboard');
    Route::get('/boards', [BoardPageController::class, 'index'])->name('boards.index');
    Route::get('/boards/{board}', [BoardPageController::class, 'show'])->name('boards.show');
    Route::get('/boards/{board}/archive', [BoardPageController::class, 'archive'])->name('boards.archive');
    Route::get('/tasks/{task}', [TaskPageController::class, 'show'])->name('tasks.show');
    Route::get('/ranking', [RankingPageController::class, 'index'])->name('ranking');
    Route::get('/achievements', [UserAchievementPageController::class, 'index'])->name('achievements');
    Route::get('/titles', [UserTitlePageController::class, 'index'])->name('titles');
    Route::get('/challenges', [UserChallengePageController::class, 'index'])->name('challenges');
    Route::get('/checkin', [CheckinPageController::class, 'index'])->name('checkin');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminHubPageController::class, 'index'])->name('admin.index');
    Route::get('/dashboard', [AdminDashboardPageController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserPageController::class, 'index'])->name('admin.users');
    Route::get('/people', [PersonPageController::class, 'index'])->name('admin.people');
    Route::get('/roles', [RolePageController::class, 'index'])->name('admin.roles');
    Route::get('/categories', [TaskCategoryPageController::class, 'index'])->name('admin.categories');
    Route::get('/event-rules', [EventRulePageController::class, 'index'])->name('admin.event-rules');
    Route::get('/priority-rules', [TaskPriorityPageController::class, 'index'])->name('admin.priority-rules');
    Route::get('/achievements', [AchievementPageController::class, 'index'])->name('admin.achievements');
    Route::get('/titles', [TitlePageController::class, 'index'])->name('admin.titles');
    Route::get('/challenges', [ChallengePageController::class, 'index'])->name('admin.challenges');
    Route::get('/settings', [AppSettingPageController::class, 'index'])->name('admin.settings');
});

require __DIR__.'/auth.php';
