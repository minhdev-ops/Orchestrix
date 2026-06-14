<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\AiScanningJob;

class AiScanningController
{
    public function index(Request $request)
    {
        $query = AiScanningJob::with(['store', 'asset']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(15);

        return Inertia::render('Admin/Scans/Index', [
            'jobs' => $jobs,
        ]);
    }

    public function show(AiScanningJob $job)
    {
        $job->load(['store', 'asset.product']);
        return Inertia::render('Admin/Scans/Show', [
            'job' => $job,
        ]);
    }

    public function destroy(AiScanningJob $job)
    {
        $job->delete();
        return redirect()->route('admin.agriverse.scans.index')
            ->with('success', 'Yêu cầu scan đã được xóa.');
    }
}
