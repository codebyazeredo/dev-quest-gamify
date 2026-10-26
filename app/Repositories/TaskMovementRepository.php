<?php

namespace App\Repositories;

use App\Models\TaskMovement;

class TaskMovementRepository extends Repository
{
    protected function model(): string
    {
        return TaskMovement::class;
    }
}
