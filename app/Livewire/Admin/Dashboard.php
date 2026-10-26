<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\RequiresAdminAccess;
use App\Services\Admin\DashboardService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    use RequiresAdminAccess;

    public function mount(): void
    {
        $this->ensureAdminAccess();
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'overview' => app(DashboardService::class)->overview(),
        ]);
    }
}
