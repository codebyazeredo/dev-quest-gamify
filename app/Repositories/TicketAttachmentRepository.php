<?php

namespace App\Repositories;

use App\Models\TicketAttachment;

class TicketAttachmentRepository extends Repository
{
    protected function model(): string
    {
        return TicketAttachment::class;
    }
}
