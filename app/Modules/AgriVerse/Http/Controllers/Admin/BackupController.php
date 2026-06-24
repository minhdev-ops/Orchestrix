<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Services\BackupService;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * List all backups
     */
    public function index()
    {
        $backups = $this->backupService->listBackups();
        $stats = $this->backupService->getStats();

        return view('admin.backups.index', compact('backups', 'stats'));
    }

    /**
     * Create full backup
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,files,full',
        ]);

        try {
            $type = $request->type;

            if ($type === 'database') {
                $this->backupService->backupDatabase();
            } elseif ($type === 'files') {
                $this->backupService->backupFiles();
            } else {
                $this->backupService->createFullBackup();
            }

            return redirect()->route('admin.agriverse.backups.index')
                ->with('success', "Đã tạo backup {$type} thành công!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a backup
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,files',
            'filename' => 'required|string',
        ]);

        $deleted = $this->backupService->deleteBackup($request->type, $request->filename);

        if ($deleted) {
            return redirect()->route('admin.agriverse.backups.index')
                ->with('success', 'Đã xóa backup!');
        }

        return back()->withErrors(['error' => 'Không thể xóa backup']);
    }

    /**
     * Get backup stats
     */
    public function stats()
    {
        return response()->json($this->backupService->getStats());
    }
}
