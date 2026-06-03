<?php

namespace Modules\Portfolio\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Portfolio\Models\AboutExperience;
use Modules\Portfolio\Services\AboutService;

class AboutExperienceController extends Controller
{
    public function __construct(
        private readonly AboutService $aboutService
    ) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:work,education,certification',
            'title' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'badge_url' => 'nullable|url|max:255',
            'certificate_url' => 'nullable|url|max:255',
        ]);

        $this->aboutService->createExperience($data);

        return back()->with('success', 'Đã thêm kinh nghiệm/học vấn.');
    }

    public function update(Request $request, AboutExperience $about_experience)
    {
        $data = $request->validate([
            'type' => 'required|in:work,education,certification',
            'title' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'badge_url' => 'nullable|url|max:255',
            'certificate_url' => 'nullable|url|max:255',
        ]);

        $this->aboutService->updateExperience($about_experience, $data);

        return back()->with('success', 'Đã cập nhật kinh nghiệm/học vấn.');
    }

    public function destroy(AboutExperience $about_experience)
    {
        $this->aboutService->deleteExperience($about_experience);
        return back()->with('success', 'Đã xóa kinh nghiệm/học vấn.');
    }
}
