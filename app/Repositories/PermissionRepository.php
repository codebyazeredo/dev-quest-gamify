<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;

class PermissionRepository extends Repository
{
    protected function model(): string
    {
        return Permission::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderBy('name');
    }
}
