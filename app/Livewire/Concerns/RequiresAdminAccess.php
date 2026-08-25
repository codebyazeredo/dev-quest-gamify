<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

trait RequiresAdminAccess
{
    protected function ensureAdminAccess(): void
    {
        Gate::authorize('accessAdminPanel', User::class);
    }
}
