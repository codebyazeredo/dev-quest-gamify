<?php

use App\Livewire\Admin\Achievements\Index as AdminAchievements;
use App\Livewire\Admin\Categories\Index as AdminCategories;
use App\Livewire\Admin\Challenges\Index as AdminChallenges;
use App\Livewire\Admin\EventRules\Index as AdminEventRules;
use App\Livewire\Admin\People\Index as AdminPeople;
use App\Livewire\Admin\PriorityRules;
use App\Livewire\Admin\Roles\Index as AdminRoles;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Admin\Titles\Index as AdminTitles;
use App\Livewire\Admin\Users\Index as AdminUsers;
use App\Livewire\Board\Index as BoardIndex;
use App\Livewire\Board\Show as BoardShow;
use App\Livewire\Checkin\History as CheckinHistory;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Gamification\Achievements;
use App\Livewire\Gamification\Challenges;
use App\Livewire\Gamification\Ranking;
use App\Livewire\Gamification\Titles;
use App\Livewire\Task\Show as TaskShow;
use App\Models\Board;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect(Board::landingUrl()) : redirect()->route('login'))->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::get('/boards', BoardIndex::class)->name('boards.index');
    Route::get('/boards/{board}', BoardShow::class)->name('boards.show');
    Route::get('/tasks/{task}', TaskShow::class)->name('tasks.show');
    Route::get('/ranking', Ranking::class)->name('ranking');
    Route::get('/achievements', Achievements::class)->name('achievements');
    Route::get('/titles', Titles::class)->name('titles');
    Route::get('/challenges', Challenges::class)->name('challenges');
    Route::get('/checkin', CheckinHistory::class)->name('checkin');
});

Route::get('/admin/users', AdminUsers::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.users');

Route::get('/admin/people', AdminPeople::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.people');

Route::get('/admin/categories', AdminCategories::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.categories');

Route::get('/admin/event-rules', AdminEventRules::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.event-rules');

Route::get('/admin/priority-rules', PriorityRules::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.priority-rules');

Route::get('/admin/achievements', AdminAchievements::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.achievements');

Route::get('/admin/titles', AdminTitles::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.titles');

Route::get('/admin/challenges', AdminChallenges::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.challenges');

Route::get('/admin/settings', AdminSettings::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.settings');

Route::get('/admin/roles', AdminRoles::class)
    ->middleware(['auth', 'role:admin'])
    ->name('admin.roles');

require __DIR__.'/auth.php';
