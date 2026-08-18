<?php

namespace App\Jobs;

use App\Modules\AgriVerse\Models\ThreeDAsset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OptimizeThreeDAsset implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ThreeDAsset $asset
    ) {}

    public function handle(): void
    {
        $this->asset->update(['compression_status' => 'processing']);

        try {
            $originalPath = Storage::disk('public')->path($this->asset->original_path);
            $ext = strtolower($this->asset->format);

            if (! file_exists($originalPath)) {
                throw new \Exception("File not found: {$originalPath}");
            }

            // Determine output path for compressed version
            $compressedDir = '3d-assets/compressed/'.$this->asset->product_id;
            $outputFilename = pathinfo($this->asset->original_filename, PATHINFO_FILENAME).'.glb';
            $outputPath = Storage::disk('public')->path($compressedDir);

            if (! is_dir($outputPath)) {
                mkdir($outputPath, 0755, true);
            }

            $outputFile = $outputPath.'/'.$outputFilename;
            $originalSize = filesize($originalPath);

            // --- Asset Optimization Pipeline ---
            // Uses available CLI tools if present, otherwise copies file as-is

            $compressedSize = $originalSize;

            if (in_array($ext, ['obj', 'fbx', 'gltf'])) {
                // Try to convert to GLB using gltf-pipeline if available
                $gltfPipeline = exec('which gltf-pipeline 2>/dev/null', $out, $code);
                if ($code === 0) {
                    $cmd = 'gltf-pipeline -i '.escapeshellarg($originalPath)
                         .' -o '.escapeshellarg($outputFile)
                         .' --draco.compressMeshes 2>&1';
                    exec($cmd, $gltfOut, $gltfCode);
                    if ($gltfCode === 0 && file_exists($outputFile)) {
                        $compressedSize = filesize($outputFile);
                    }
                }
            } elseif ($ext === 'glb') {
                // Already GLB - try draco compression
                $draco = exec('which draco_encoder 2>/dev/null', $out, $code);
                if ($code === 0) {
                    $cmd = 'draco_encoder -i '.escapeshellarg($originalPath)
                         .' -o '.escapeshellarg($outputFile)
                         .' -cl 10 -qp 14 2>&1';
                    exec($cmd, $dracoOut, $dracoCode);
                    if ($dracoCode === 0 && file_exists($outputFile)) {
                        $compressedSize = filesize($outputFile);
                    }
                }
            }

            // Fallback: if no compression tool ran, copy original as compressed
            if (! file_exists($outputFile)) {
                copy($originalPath, $outputFile);
            }

            // Generate thumbnail (placeholder - in production use a proper renderer)
            $thumbnailDir = '3d-assets/thumbnails/'.$this->asset->product_id;
            if (! is_dir(Storage::disk('public')->path($thumbnailDir))) {
                mkdir(Storage::disk('public')->path($thumbnailDir), 0755, true);
            }
            $thumbnailFile = $thumbnailDir.'/'.$outputFilename.'.jpg';
            // TODO: generate actual thumbnail using Three.js headless or screenshot

            $this->asset->update([
                'compressed_filename' => $outputFilename,
                'compressed_path' => $compressedDir.'/'.$outputFilename,
                'compressed_file_size' => $compressedSize,
                'compression_status' => 'completed',
                'compression_settings' => [
                    'original_size' => $originalSize,
                    'compressed_size' => $compressedSize,
                    'ratio' => $originalSize > 0 ? round((1 - $compressedSize / $originalSize) * 100, 1) : 0,
                    'format' => 'glb',
                    'tool' => $ext === 'glb' ? 'draco' : 'gltf-pipeline',
                ],
                'thumbnail_path' => $thumbnailFile,
            ]);

            Log::info("3D Asset optimized: {$this->asset->id} ({$originalSize} -> {$compressedSize} bytes)");

        } catch (\Exception $e) {
            $this->asset->update([
                'compression_status' => 'failed',
                'compression_settings' => ['error' => $e->getMessage()],
            ]);

            Log::error("3D Asset optimization failed for {$this->asset->id}: ".$e->getMessage());
        }
    }
}
