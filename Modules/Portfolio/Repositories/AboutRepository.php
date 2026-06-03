<?php

namespace Modules\Portfolio\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Portfolio\Models\About;
use Modules\Portfolio\Models\AboutExperience;
use Modules\Portfolio\Models\AboutStat;

/**
 * AboutRepository - Data Access Layer cho About + Stats + Experiences.
 * About là singleton, nên repository này phản ánh điều đó.
 */
class AboutRepository extends BaseRepository
{
    public function __construct(About $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy About singleton (tạo mới nếu chưa có).
     */
    public function getSingleton(): About
    {
        return About::getSingleton();
    }

    /**
     * Lấy tất cả stats theo sort_order.
     */
    public function getStats(): Collection
    {
        return AboutStat::ordered()->get();
    }

    /**
     * Tạo mới stat.
     */
    public function createStat(array $data): AboutStat
    {
        return AboutStat::create($data);
    }

    /**
     * Cập nhật stat.
     */
    public function updateStat(AboutStat $stat, array $data): bool
    {
        return $stat->update($data);
    }

    /**
     * Xóa stat.
     */
    public function deleteStat(AboutStat $stat): bool
    {
        return $stat->delete();
    }

    /**
     * Lấy tất cả experiences, sắp xếp theo sort_order và start_date.
     */
    public function getExperiences(): Collection
    {
        return AboutExperience::ordered()->get();
    }

    /**
     * Lấy experiences theo type.
     */
    public function getExperiencesByType(string $type): Collection
    {
        return AboutExperience::ofType($type)->ordered()->get();
    }

    /**
     * Tạo mới experience.
     */
    public function createExperience(array $data): AboutExperience
    {
        return AboutExperience::create($data);
    }

    /**
     * Cập nhật experience.
     */
    public function updateExperience(AboutExperience $experience, array $data): bool
    {
        return $experience->update($data);
    }

    /**
     * Xóa experience.
     */
    public function deleteExperience(AboutExperience $experience): bool
    {
        return $experience->delete();
    }
}
