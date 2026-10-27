<?php

namespace App\Repositories;

use App\Models\BusinessSector;
use Illuminate\Database\Eloquent\Builder;

class BusinessSectorRepository extends Repository
{
    protected function model(): string
    {
        return BusinessSector::class;
    }

    protected function query(): Builder
    {
        return parent::query()->withCount('clients')->orderBy('name');
    }

    public function hasClients(BusinessSector $sector): bool
    {
        return $sector->clients()->exists();
    }
}
