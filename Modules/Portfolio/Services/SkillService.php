<?php

namespace Modules\Portfolio\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Modules\Portfolio\Models\Skill;
use Modules\Portfolio\Repositories\SkillRepository;

/**
 * SkillService - Business Logic Layer cho Skills.
 */
class SkillService
{
    public function __construct(
        private readonly SkillRepository $skillRepository
    ) {}

    // =========================================================================
    // Read Operations
    // =========================================================================

    public function getVisibleSkills(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->skillRepository->getVisible();
    }

    public function getFeaturedSkills(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return $this->skillRepository->getFeatured($limit);
    }

    public function getSkillsGroupedByCategory(): \Illuminate\Support\Collection
    {
        return $this->skillRepository->getVisibleGrouped();
    }

    public function paginateAllForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->skillRepository->paginateAll($perPage);
    }

    public function findBySlug(string $slug): ?Skill
    {
        return $this->skillRepository->findBySlug($slug);
    }

    public function getAllCategories(): array
    {
        return $this->skillRepository->getAllCategories();
    }

    public function countAll(): int
    {
        return $this->skillRepository->count();
    }

    // =========================================================================
    // Write Operations
    // =========================================================================

    /**
     * Tạo skill mới.
     */
    public function createSkill(array $data, ?UploadedFile $iconImage = null): Skill
    {
        $data = $this->prepareSkillData($data, $iconImage);
        return $this->skillRepository->create($data);
    }

    /**
     * Cập nhật skill.
     */
    public function updateSkill(Skill $skill, array $data, ?UploadedFile $iconImage = null): bool
    {
        $data = $this->prepareSkillData($data, $iconImage, $skill);
        return $this->skillRepository->update($skill, $data);
    }

    /**
     * Xóa skill.
     */
    public function deleteSkill(Skill $skill): bool
    {
        // Xóa icon image nếu có
        if ($skill->icon_url && ! filter_var($skill->icon_url, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($skill->icon_url);
        }
        return $this->skillRepository->delete($skill);
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    private function prepareSkillData(array $data, ?UploadedFile $iconImage, ?Skill $existing = null): array
    {
        if ($iconImage !== null) {
            if ($existing?->icon_url) {
                Storage::disk('public')->delete($existing->icon_url);
            }
            $data['icon_url'] = $iconImage->store('portfolio/skills', 'public');
        }

        $data['is_visible']  = ! empty($data['is_visible']);
        $data['is_featured'] = ! empty($data['is_featured']);

        // Ensure level is within bounds
        $data['level'] = max(0, min(100, (int) ($data['level'] ?? 80)));

        return $data;
    }
}
