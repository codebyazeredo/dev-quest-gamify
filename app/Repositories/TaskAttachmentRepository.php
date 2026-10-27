<?php

namespace App\Repositories;

use App\Models\TaskAttachment;

class TaskAttachmentRepository extends Repository
{
    protected function model(): string
    {
        return TaskAttachment::class;
    }
}
