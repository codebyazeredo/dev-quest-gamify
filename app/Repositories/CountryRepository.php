<?php

namespace App\Repositories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;

class CountryRepository extends Repository
{
    private const DEFAULT_ISO2 = 'BR';

    protected function model(): string
    {
        return Country::class;
    }

    protected function query(): Builder
    {
        return parent::query()->orderBy('name');
    }

    public function defaultId(): int
    {
        return (int) Country::query()->where('iso2', self::DEFAULT_ISO2)->valueOrFail('id');
    }
}
