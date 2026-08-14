<?php

namespace App\Modules\AgriVerse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $path,
        protected array $sizes = ['thumb' => [150, 150], 'medium' => [640, 480], 'large' => [1920, 1080]]
    ) {}

    public function handle(): void
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($this->path)) {
            return;
        }

        $manager = ImageManager::gd();
        $image = $manager->read($disk->get($this->path));

        $dir = pathinfo($this->path, PATHINFO_DIRNAME);
        $filename = pathinfo($this->path, PATHINFO_FILENAME);
        $ext = pathinfo($this->path, PATHINFO_EXTENSION);

        foreach ($this->sizes as $label => [$width, $height]) {
            $resized = $image->cover($width, $height);
            $resizedPath = "{$dir}/{$label}_{$filename}.{$ext}";
            $disk->put($resizedPath, $resized->encode());
        }
    }
}
