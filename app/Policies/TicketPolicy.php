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

    public function viewAttachments(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket) || ($ticket->task !== null && $user->can('view', $ticket->task));
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $this->isSupportStaff($user);
    }

    private function isSupportStaff(User $user): bool
    {
        return $user->isSupportStaff();
    }
}
