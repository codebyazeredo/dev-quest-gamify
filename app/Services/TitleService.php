<?php

namespace App\Services;

use App\Models\Title;
use App\Models\User;

class TitleService
{
    public function select(User $user, Title $title): void
    {
        $user->update(['selected_title_id' => $title->id]);
    }

    public function clear(User $user): void
    {
        $user->update(['selected_title_id' => null]);
    }
}
