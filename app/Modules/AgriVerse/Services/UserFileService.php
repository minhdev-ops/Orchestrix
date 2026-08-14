<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Http\UploadedFile;

class UserFileService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'mp4', 'mov'];

    private const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB

    public static function userPath(int $userId, string $type = 'uploads'): string
    {
        return "users/{$userId}/{$type}";
    }

    public static function validate(UploadedFile $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, self::ALLOWED_EXTENSIONS)) {
            throw new \InvalidArgumentException('File type .'.$ext.' is not allowed.');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File size exceeds '.self::MAX_FILE_SIZE / 1024 / 1024 .'MB limit.');
        }
    }

    public static function store(UploadedFile $file, int $userId, string $type = 'uploads', ?string $filename = null): string
    {
        self::validate($file);

        $path = self::userPath($userId, $type);
        $name = $filename ?? uniqid().'_'.$file->getClientOriginalName();

        $result = $file->storeAs($path, $name, 'public');

        if ($result === false) {
            throw new \RuntimeException('Failed to store file: '.$name);
        }

        return $result;
    }

    public static function storeInFolder(UploadedFile $file, int $userId, string $subfolder, ?string $filename = null): string
    {
        self::validate($file);

        $path = self::userPath($userId, $subfolder);
        $name = $filename ?? uniqid().'_'.$file->getClientOriginalName();

        $result = $file->storeAs($path, $name, 'public');

        if ($result === false) {
            throw new \RuntimeException('Failed to store file: '.$name);
        }

        return $result;
    }

    public static function ensureDirectories(int $userId): void
    {
        $dirs = ['uploads', 'images', 'files', 'products', 'avatars'];

        foreach ($dirs as $dir) {
            $path = public_path(self::userPath($userId, $dir));
            if (! is_dir($path)) {
                @mkdir($path, 0755, true);
            }
        }
    }
}
