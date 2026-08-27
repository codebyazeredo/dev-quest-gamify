<?php

namespace App\Repositories;

use App\Models\Title;
use Illuminate\Database\Eloquent\Builder;

class TitleRepository extends Repository
{
    protected function model(): string
    {
        return Title::class;
    }

    protected function query(): Builder
    {
        return parent::query()->with('achievement')->orderBy('name');
    }

    public function hasUnlocks(Title $title): bool
    {
        return $title->userTitles()->exists();
    }
}
