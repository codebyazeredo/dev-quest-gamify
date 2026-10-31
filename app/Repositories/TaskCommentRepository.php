<?php

namespace App\Repositories;

use App\Models\TaskComment;

class TaskCommentRepository extends Repository
{
    protected function model(): string
    {
        return TaskComment::class;
    }
}
