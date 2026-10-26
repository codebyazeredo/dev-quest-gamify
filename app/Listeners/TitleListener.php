<?php

namespace App\Listeners;

use App\Events\ChallengeCompleted;
use App\Services\TitleService;

class TitleListener
{
    public function __construct(private TitleService $titleService) {}

    public function handleChallengeCompleted(ChallengeCompleted $event): void
    {
        $this->titleService->grantForCompletedChallenge($event->user, $event->challenge);
    }
}
