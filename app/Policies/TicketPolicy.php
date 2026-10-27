<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isSupportStaff($user);
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $this->isSupportStaff($user);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $this->isSupportStaff($user);
    }

    private function isSupportStaff(User $user): bool
    {
        return $user->isAdmin() || $user->isProductOwner() || $user->isSuporte();
    }
}
