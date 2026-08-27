<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    abstract protected function model(): string;

    protected function query(): Builder
    {
        $modelClass = $this->model();

        return $modelClass::query();
    }

    public function findOrFail(int $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function create(array $attributes): Model
    {
        $modelClass = $this->model();

        return $modelClass::create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model;
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage);
    }
}
