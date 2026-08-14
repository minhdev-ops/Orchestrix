<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->modelClass());
    }

    abstract protected function modelClass(): string;

    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(Model|int|string $model, array $data): Model
    {
        if (! ($model instanceof Model)) {
            $model = $this->findOrFail($model);
        }

        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            return $model->fresh();
        });
    }

    public function delete(Model|int|string $model): bool
    {
        if (! ($model instanceof Model)) {
            $model = $this->findOrFail($model);
        }

        return DB::transaction(function () use ($model) {
            return $model->delete();
        });
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->query();
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }

        return $query->latest()->paginate($perPage);
    }
}
