<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\CheckinService;
use Illuminate\Auth\Events\Login;

class AutoCheckinOnLogin
{
    public function __construct(private CheckinService $checkinService) {}

    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $this->checkinService->checkIn($event->user);
    }
}
