<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\AiScanningJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

    public function destroy(Request $request, AiScanningJob $job)
    {
        $user = auth()->user();

        abort_unless(
            $user->isAdmin() || ($job->store && $job->store->owner_id === $user->id),
            403,
            'Bạn không có quyền xóa yêu cầu scan này.'
        );

        $job->delete();

        return redirect()->route('admin.agriverse.scans.index')
            ->with('success', 'Yêu cầu scan đã được xóa.');
    }
}
