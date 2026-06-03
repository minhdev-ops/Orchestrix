<?php

namespace Modules\Portfolio\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Portfolio\Models\Project;

/**
 * ProjectRepository - Data Access Layer cho Projects.
 * Tập trung tất cả query logic liên quan đến Project.
 */
class ProjectRepository extends BaseRepository
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy tất cả projects, mới nhất đầu tiên.
     */
    public function getAllOrdered(): Collection
    {
        return $this->model->latest()->get();
    }

    /**
     * Lấy projects hiển thị, sắp xếp theo sort_order.
     */
    public function getVisible(): Collection
    {
        return $this->model->visible()->ordered()->get();
    }

    /**
     * Lấy projects được featured và visible.
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return $this->model
            ->visible()
            ->featured()
            ->ordered()
            ->take($limit)
            ->get();
    }

    /**
     * Phân trang projects visible.
     */
    public function paginateVisible(int $perPage = 9): LengthAwarePaginator
    {
        return $this->model->visible()->ordered()->paginate($perPage);
    }

    /**
     * Phân trang tất cả projects cho admin.
     */
    public function paginateAll(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * Tìm project theo slug.
     */
    public function findBySlug(string $slug): ?Project
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Lấy projects theo category.
     */
    public function getByCategory(string $category): Collection
    {
        return $this->model->visible()->inCategory($category)->ordered()->get();
    }

    /**
     * Lấy tất cả categories.
     */
    public function getAllCategories(): array
    {
        return $this->model->distinct()->pluck('category')->sort()->values()->toArray();
    }

    /**
     * Lấy projects liên quan (same category, excluding current).
     */
    public function getRelated(Project $project, int $limit = 3): Collection
    {
        return $this->model
            ->visible()
            ->inCategory($project->category)
            ->where('id', '!=', $project->id)
            ->take($limit)
            ->get();
    }

    /**
     * Đếm projects chưa ẩn.
     */
    public function countVisible(): int
    {
        return $this->model->visible()->count();
    }

    /**
     * Toggle trạng thái is_visible.
     */
    public function toggleVisibility(Project $project): bool
    {
        return $project->update(['is_visible' => ! $project->is_visible]);
    }

    /**
     * Toggle trạng thái is_featured.
     */
    public function toggleFeatured(Project $project): bool
    {
        return $project->update(['is_featured' => ! $project->is_featured]);
    }
}
