<?php

namespace Modules\Portfolio\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * BaseRepository - Abstract base class cho tất cả repositories.
 * Cung cấp các CRUD operations cơ bản với Eloquent.
 */
abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Lấy tất cả records.
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Tìm record theo ID.
     */
    public function find(int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Tìm hoặc throw exception.
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Tìm theo điều kiện.
     */
    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    /**
     * Tạo mới record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Cập nhật record.
     */
    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    /**
     * Xóa record.
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Phân trang.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Đếm tổng số records.
     */
    public function count(): int
    {
        return $this->model->count();
    }
}
