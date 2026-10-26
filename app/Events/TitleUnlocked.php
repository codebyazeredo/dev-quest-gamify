<?php

namespace App\Events;

use App\Models\Title;
use App\Models\User;

class TitleUnlocked
{
    public function __construct(
        public readonly User $user,
        public readonly Title $title,
    ) {}
}
