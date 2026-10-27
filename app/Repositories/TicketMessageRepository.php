<?php

namespace App\Repositories;

use App\Models\TicketMessage;

class TicketMessageRepository extends Repository
{
    protected function model(): string
    {
        return TicketMessage::class;
    }
}
