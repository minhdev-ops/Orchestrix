<?php

namespace Modules\Portfolio\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Portfolio\Models\Skill;

/**
 * SkillRepository - Data Access Layer cho Skills.
 */
class SkillRepository extends BaseRepository
{
    public function __construct(Skill $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy tất cả skills, sắp xếp theo sort_order.
     */
    public function getAllOrdered(): Collection
    {
        return $this->model->ordered()->get();
    }

    /**
     * Lấy skills hiển thị, nhóm theo category.
     */
    public function getVisibleGrouped(): \Illuminate\Support\Collection
    {
        return Skill::getGroupedByCategory();
    }

    /**
     * Lấy skills hiển thị.
     */
    public function getVisible(): Collection
    {
        return $this->model->visible()->ordered()->get();
    }

    /**
     * Lấy skills được featured.
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return $this->model->visible()->featured()->ordered()->take($limit)->get();
    }

    /**
     * Phân trang cho admin.
     */
    public function paginateAll(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->ordered()->paginate($perPage);
    }

    /**
     * Tìm skill theo slug.
     */
    public function findBySlug(string $slug): ?Skill
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Lấy tất cả categories.
     */
    public function getAllCategories(): array
    {
        return $this->model->distinct()->pluck('category')->sort()->values()->toArray();
    }
}
