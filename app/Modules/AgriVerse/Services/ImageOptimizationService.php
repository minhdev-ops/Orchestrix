<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Drivers\Gd\Driver;

class ImageOptimizationService
{
    protected array $sizes = [
        'thumbnail' => ['width' => 150, 'height' => 150, 'crop' => true],
        'small' => ['width' => 300, 'height' => 300, 'crop' => false],
        'medium' => ['width' => 600, 'height' => 600, 'crop' => false],
        'large' => ['width' => 1200, 'height' => 1200, 'crop' => false],
    ];

    protected array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    protected int $maxSize = 5120; // 5MB

    /**
     * Process uploaded image with multiple sizes
     */
    public function process(UploadedFile $file, string $directory = 'products'): array
    {
        if (!in_array($file->getMimeType(), $this->allowedMimes)) {
            throw new \InvalidArgumentException('Định dạng ảnh không hỗ trợ.');
        }

        if ($file->getSize() > $this->maxSize * 1024) {
            throw new \InvalidArgumentException('Kích thước ảnh tối đa 5MB.');
        }

        $filename = $this->generateFilename($file);
        $results = [];

        $image = Image::make($file);

        foreach ($this->sizes as $sizeName => $config) {
            $resized = $image->copy();

            if ($config['crop']) {
                $resized->fit($config['width'], $config['height']);
            } else {
                $resized->resize($config['width'], $config['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Convert to WebP for better compression
            $resized->toWebp(85);

            $path = "{$directory}/{$sizeName}/{$filename}";
            Storage::disk('public')->put($path, $resized->toEncoded());

            $results[$sizeName] = [
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'width' => $resized->getWidth(),
                'height' => $resized->getHeight(),
            ];
        }

        // Also store original (optimized)
        $originalPath = "{$directory}/original/{$filename}";
        $image->toWebp(90);
        Storage::disk('public')->put($originalPath, $image->toEncoded());

        $results['original'] = [
            'path' => $originalPath,
            'url' => Storage::disk('public')->url($originalPath),
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
        ];

        return $results;
    }

    /**
     * Process single image for upload
     */
    public function optimize(UploadedFile $file, string $directory = 'uploads'): string
    {
        $filename = $this->generateFilename($file);

        $image = Image::make($file);
        $image->toWebp(85);

        $path = "{$directory}/{$filename}";
        Storage::disk('public')->put($path, $image->toEncoded());

        return $path;
    }

    /**
     * Add watermark to image
     */
    public function addWatermark(string $imagePath, string $watermarkText = 'AgriVerse'): string
    {
        $ fullPath = Storage::disk('public')->path($imagePath);

        if (!File::exists($fullPath)) {
            return $imagePath;
        }

        $image = Image::make($fullPath);

        // Add text watermark
        $image->text($watermarkText, $image->getWidth() - 20, $image->getHeight() - 20, function ($font) {
            $font->file(resource_path('fonts/inter.ttf'));
            $font->size(16);
            $font->color('rgba(255, 255, 255, 0.7)');
            $font->align('right');
            $font->valign('bottom');
        });

        $watermarkedPath = str_replace('/original/', '/watermarked/', $imagePath);
        $watermarkedDir = dirname(Storage::disk('public')->path($watermarkedPath));

        if (!File::exists($watermarkedDir)) {
            File::makeDirectory($watermarkedDir, 0755, true);
        }

        $image->toWebp(85);
        Storage::disk('public')->put($watermarkedPath, $image->toEncoded());

        return $watermarkedPath;
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file): string
    {
        return uniqid() . '.' . 'webp';
    }

    /**
     * Get image info
     */
    public function getInfo(string $path): ?array
    {
        $fullPath = Storage::disk('public')->path($path);

        if (!File::exists($fullPath)) {
            return null;
        }

        $image = Image::make($fullPath);

        return [
            'width' => $image->getWidth(),
            'height' => $image->getHeight(),
            'mime' => $image->getMimeType(),
            'size' => File::size($fullPath),
        ];
    }

    /**
     * Delete all sizes of an image
     */
    public function deleteAllSizes(string $directory, string $filename): bool
    {
        $deleted = true;

        foreach (array_keys($this->sizes) as $sizeName) {
            $path = "{$directory}/{$sizeName}/{$filename}";
            if (Storage::disk('public')->exists($path)) {
                $deleted = Storage::disk('public')->delete($path) && $deleted;
            }
        }

        // Delete original
        $originalPath = "{$directory}/original/{$filename}";
        if (Storage::disk('public')->exists($originalPath)) {
            $deleted = Storage::disk('public')->delete($originalPath) && $deleted;
        }

        return $deleted;
    }
}
