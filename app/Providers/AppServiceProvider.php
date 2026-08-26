<?php

namespace App\Providers;

use App\Models\User;
use App\Support\ToastCollector;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ToastCollector::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        // "move" is excluded: TaskPolicy::move() blocks Testing as a structural
        // checkpoint for every role, admin included — see §2 of the RBAC plan.
        // The policy already grants admin/PO full access for every other move.
        Gate::before(function (User $user, string $ability) {
            if ($ability === 'move') {
                return null;
            }

            return $user->hasRole('admin') ? true : null;
        });

        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
