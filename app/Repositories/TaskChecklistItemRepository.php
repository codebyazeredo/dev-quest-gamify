<?php

namespace App\Repositories;

use App\Models\TaskChecklistItem;

class TaskChecklistItemRepository extends Repository
{
    protected function model(): string
    {
        return TaskChecklistItem::class;
    }
}
