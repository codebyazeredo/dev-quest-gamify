<?php

namespace App\Events;

use App\Models\TaskEvent;

class TaskEventCreated
{
    public function __construct(public readonly TaskEvent $taskEvent) {}
}
