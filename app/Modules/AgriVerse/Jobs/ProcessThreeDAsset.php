<?php

namespace App\Modules\AgriVerse\Jobs;

use App\Modules\AgriVerse\Models\ThreeDAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessThreeDAsset implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public ThreeDAsset $asset
    ) {}

    public function handle(): void
    {
        $this->asset->update(['compression_status' => 'processing']);

        $disk = Storage::disk('public');
        $inputPath = storage_path('app/public/'.$this->asset->original_path);

        if (! $disk->exists($this->asset->original_path)) {
            $this->asset->update(['compression_status' => 'failed']);

            return;
        }

        $outputDir = storage_path('app/public/3d-assets/compressed/'.$this->asset->uuid);
        $scriptDir = base_path('Modules/AgriVerse/Scripts');

        $cmd = sprintf(
            'cd %s && node process-asset.js %s %s 2>&1',
            escapeshellarg($scriptDir),
            escapeshellarg($inputPath),
            escapeshellarg($outputDir)
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->asset->update([
                'compression_status' => 'failed',
                'compression_settings' => ['error' => implode("\n", $output)],
            ]);

            return;
        }

        $storedBasename = pathinfo($this->asset->original_path, PATHINFO_FILENAME);
        $glbFilename = $storedBasename.'.glb';
        $glbPath = '3d-assets/compressed/'.$this->asset->uuid.'/'.$glbFilename;

        if ($disk->exists($glbPath)) {
            $compressedSize = $disk->size($glbPath);
            $this->asset->update([
                'compressed_filename' => $glbFilename,
                'compressed_path' => $glbPath,
                'compressed_file_size' => $compressedSize,
                'compression_status' => 'completed',
            ]);
        } else {
            $this->asset->update(['compression_status' => 'failed']);
        }
    }
}
