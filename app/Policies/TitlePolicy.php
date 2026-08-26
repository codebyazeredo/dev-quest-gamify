<?php

namespace App\Policies;

use App\Models\Title;
use App\Models\User;
use App\Models\UserTitle;

class TitlePolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Title $title): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Title $title): bool
    {
        return $user->isAdmin();
    }

    public function select(User $user, Title $title): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return UserTitle::where('user_id', $user->id)->where('title_id', $title->id)->exists();
    }
}
