<?php

/**
 * @deprecated Use App\Modules\AgriVerse\Http\Controllers\Admin\FileController instead.
 * This controller uses Blade views; the module version uses Inertia SPA.
 * Kept for backward compatibility. Will be removed in next major version.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class FilesController extends Controller
{
    public function index()
    {
        $path = public_path('userfiles');
        $stats = [
            'total_size' => 0,
            'total_files' => 0,
            'types' => [
                'images' => 0,
                'documents' => 0,
                'others' => 0,
            ],
        ];

        $recent_images = [];
        if (File::exists($path)) {
            $files = File::allFiles($path);
            $stats['total_files'] = count($files);

            // Sort files by modified time descending
            usort($files, function ($a, $b) {
                return $b->getMTime() - $a->getMTime();
            });

            foreach ($files as $file) {
                $stats['total_size'] += $file->getSize();
                $extension = strtolower($file->getExtension());

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                    $stats['types']['images']++;

                    // Collect up to 8 recent images
                    if (count($recent_images) < 8) {
                        $recent_images[] = [
                            'name' => $file->getFilename(),
                            'url' => asset('userfiles/'.str_replace(public_path('userfiles/'), '', $file->getRealPath())),
                            'size' => $this->formatBytes($file->getSize()),
                            'mtime' => date('Y-m-d H:i:s', $file->getMTime()),
                        ];
                    }
                } elseif (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'])) {
                    $stats['types']['documents']++;
                } else {
                    $stats['types']['others']++;
                }
            }
        }

        // Format size
        $stats['total_size_human'] = $this->formatBytes($stats['total_size']);

        return view('admin.files', compact('stats', 'recent_images'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
