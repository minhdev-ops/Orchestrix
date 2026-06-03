<?php

namespace Modules\Portfolio\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Modules\Portfolio\Models\Project;
use Modules\Portfolio\Repositories\ProjectRepository;

/**
 * ProjectService - Business Logic Layer cho Projects.
 * Xử lý tất cả logic nghiệp vụ: validation, file upload, data transform.
 * Controller chỉ gọi Service, không gọi trực tiếp Repository.
 */
class ProjectService
{
    public function __construct(
        private readonly ProjectRepository $projectRepository
    ) {}

    // =========================================================================
    // Read Operations
    // =========================================================================

    public function getFeaturedProjects(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return $this->projectRepository->getFeatured($limit);
    }

    public function getVisibleProjects(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->projectRepository->getVisible();
    }

    public function paginateVisibleProjects(int $perPage = 9): LengthAwarePaginator
    {
        return $this->projectRepository->paginateVisible($perPage);
    }

    public function paginateAllForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->projectRepository->paginateAll($perPage);
    }

    public function findBySlug(string $slug): ?Project
    {
        return $this->projectRepository->findBySlug($slug);
    }

    public function getRelatedProjects(Project $project, int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        return $this->projectRepository->getRelated($project, $limit);
    }

    public function getAllCategories(): array
    {
        return $this->projectRepository->getAllCategories();
    }

    public function countAll(): int
    {
        return $this->projectRepository->count();
    }

    // =========================================================================
    // Write Operations
    // =========================================================================

    /**
     * Tạo project mới từ validated data.
     * Xử lý: image upload, tech_stack parsing.
     */
    public function createProject(array $data, ?UploadedFile $image = null): Project
    {
        $data = $this->prepareProjectData($data, $image);
        return $this->projectRepository->create($data);
    }

    /**
     * Cập nhật project từ validated data.
     */
    public function updateProject(Project $project, array $data, ?UploadedFile $image = null): bool
    {
        $data = $this->prepareProjectData($data, $image, $project);
        return $this->projectRepository->update($project, $data);
    }

    /**
     * Xóa project và ảnh liên quan.
     */
    public function deleteProject(Project $project): bool
    {
        $this->deleteProjectImage($project);
        return $this->projectRepository->delete($project);
    }

    /**
     * Toggle trạng thái hiển thị.
     */
    public function toggleVisibility(Project $project): bool
    {
        return $this->projectRepository->toggleVisibility($project);
    }

    /**
     * Toggle trạng thái featured.
     */
    public function toggleFeatured(Project $project): bool
    {
        return $this->projectRepository->toggleFeatured($project);
    }

    /**
     * Tăng view count.
     */
    public function incrementView(Project $project): void
    {
        $project->incrementViewCount();
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    /**
     * Chuẩn bị data trước khi lưu:
     * - Upload ảnh nếu có
     * - Chuyển đổi tech_stack_input thành array
     */
    private function prepareProjectData(array $data, ?UploadedFile $image, ?Project $existing = null): array
    {
        // Handle image upload
        if ($image !== null) {
            // Xóa ảnh cũ nếu đang update
            if ($existing?->image) {
                Storage::disk('public')->delete($existing->image);
            }
            $data['image'] = $image->store('portfolio/projects', 'public');
        }

        // Parse tech_stack từ string CSV → array
        if (isset($data['tech_stack_input']) && ! empty($data['tech_stack_input'])) {
            $data['tech_stack'] = array_values(
                array_filter(
                    array_map('trim', explode(',', $data['tech_stack_input']))
                )
            );
        }

        // Clean up helper fields
        unset($data['tech_stack_input']);

        // Normalize booleans từ form checkbox
        $data['is_featured'] = ! empty($data['is_featured']);
        $data['is_visible']  = ! empty($data['is_visible']);

        return $data;
    }

    /**
     * Xóa file ảnh của project khỏi storage.
     */
    private function deleteProjectImage(Project $project): void
    {
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }
    }
}
