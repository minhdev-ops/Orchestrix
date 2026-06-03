<?php

namespace Modules\Portfolio\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Portfolio\Models\About;
use Modules\Portfolio\Models\AboutExperience;
use Modules\Portfolio\Models\AboutStat;
use Modules\Portfolio\Repositories\AboutRepository;

/**
 * AboutService - Business Logic Layer cho About section.
 */
class AboutService
{
    public function __construct(
        private readonly AboutRepository $aboutRepository
    ) {}

    // =========================================================================
    // Read Operations
    // =========================================================================

    public function getAboutInfo(): About
    {
        return $this->aboutRepository->getSingleton();
    }

    public function getStats(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->aboutRepository->getStats();
    }

    public function getExperiences(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->aboutRepository->getExperiences();
    }

    public function getWorkExperiences(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->aboutRepository->getExperiencesByType('work');
    }

    public function getEducations(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->aboutRepository->getExperiencesByType('education');
    }

    // =========================================================================
    // Write Operations - About
    // =========================================================================

    public function updateAbout(array $data, ?UploadedFile $avatar = null, ?UploadedFile $resume = null): About
    {
        $about = $this->aboutRepository->getSingleton();

        // Handle Avatar Upload
        if ($avatar !== null) {
            if ($about->avatar) {
                Storage::disk('public')->delete($about->avatar);
            }
            $data['avatar'] = $avatar->store('portfolio/about', 'public');
        }

        // Handle Resume Upload
        if ($resume !== null) {
            if ($about->resume_url && ! filter_var($about->resume_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($about->resume_url);
            }
            $data['resume_url'] = $resume->store('portfolio/documents', 'public');
        }

        $this->aboutRepository->update($about, $data);
        return $about->fresh();
    }

    // =========================================================================
    // Write Operations - Stats
    // =========================================================================

    public function createStat(array $data): AboutStat
    {
        $data['about_id'] = $this->aboutRepository->getSingleton()->id;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        return $this->aboutRepository->createStat($data);
    }

    public function updateStat(AboutStat $stat, array $data): bool
    {
        $data['sort_order'] = $data['sort_order'] ?? $stat->sort_order;
        return $this->aboutRepository->updateStat($stat, $data);
    }

    public function deleteStat(AboutStat $stat): bool
    {
        return $this->aboutRepository->deleteStat($stat);
    }

    // =========================================================================
    // Write Operations - Experiences
    // =========================================================================

    public function createExperience(array $data): AboutExperience
    {
        $data['about_id'] = $this->aboutRepository->getSingleton()->id;
        $data['is_current'] = ! empty($data['is_current']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        return $this->aboutRepository->createExperience($data);
    }

    public function updateExperience(AboutExperience $experience, array $data): bool
    {
        $data['is_current'] = ! empty($data['is_current']);
        $data['sort_order'] = $data['sort_order'] ?? $experience->sort_order;
        return $this->aboutRepository->updateExperience($experience, $data);
    }

    public function deleteExperience(AboutExperience $experience): bool
    {
        return $this->aboutRepository->deleteExperience($experience);
    }
}
