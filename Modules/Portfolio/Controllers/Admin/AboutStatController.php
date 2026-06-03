<?php

namespace Modules\Portfolio\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Portfolio\Models\AboutStat;
use Modules\Portfolio\Services\AboutService;

class AboutStatController extends Controller
{
    public function __construct(
        private readonly AboutService $aboutService
    ) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $this->aboutService->createStat($data);

        return back()->with('success', 'Đã thêm chỉ số thống kê.');
    }

    public function update(Request $request, AboutStat $about_stat)
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $this->aboutService->updateStat($about_stat, $data);

        return back()->with('success', 'Đã cập nhật chỉ số thống kê.');
    }

    public function destroy(AboutStat $about_stat)
    {
        $this->aboutService->deleteStat($about_stat);
        return back()->with('success', 'Đã xóa chỉ số thống kê.');
    }
}
