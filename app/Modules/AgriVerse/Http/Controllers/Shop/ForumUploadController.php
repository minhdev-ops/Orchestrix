<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Services\UserFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ForumUploadController
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $userId = (int) auth()->id();
        $path = UserFileService::storeInFolder($request->file('image'), $userId, 'forum');

        return response()->json([
            'url' => Storage::url($path),
            'success' => true,
        ]);
    }

    public function listImages(Request $request)
    {
        $userId = (int) auth()->id();
        $base = UserFileService::userPath($userId, 'forum');

        if (! Storage::disk('public')->exists($base)) {
            return response()->json(['images' => []]);
        }

        $files = Storage::disk('public')->files($base);
        $images = collect($files)
            ->filter(fn ($path) => in_array(pathinfo($path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
            ->values()
            ->map(fn ($path) => [
                'url' => Storage::url($path),
                'name' => basename($path),
                'size' => Storage::disk('public')->size($path),
                'modified' => Storage::disk('public')->lastModified($path),
            ]);

        return response()->json(['images' => $images]);
    }
}
