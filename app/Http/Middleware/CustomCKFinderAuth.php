<?php

namespace App\Http\Middleware;

use App\Modules\AgriVerse\Services\UserFileService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CustomCKFinderAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            config(['ckfinder.authentication' => function () {
                return false;
            }]);

            return $next($request);
        }

        $user = auth()->user();
        $userId = $user->id;
        $userFolder = "/userfiles/users/{$userId}";

        // Auto-create user directories
        UserFileService::ensureDirectories($userId);
        if (! is_dir($userFolder)) {
            if (! is_dir($userFolder)) {
                if (! mkdir($userFolder, 0755, true) && ! is_dir($userFolder)) {
                    Log::warning('Failed to create directory: '.$userFolder);
                }
            }
        }

        // Define per-user backend
        $userBackend = [
            'name' => "user_{$userId}",
            'adapter' => 'local',
            'baseUrl' => config('app.url')."/userfiles/users/{$userId}",
            'root' => public_path("/userfiles/users/{$userId}"),
            'chmodFiles' => 0777,
            'chmodFolders' => 0755,
            'filesystemEncoding' => 'UTF-8',
        ];

        // Merge into existing backends
        $backends = config('ckfinder.backends', []);
        $backends["user_{$userId}"] = $userBackend;

        // Admin gets access to the default pool too
        if ($user->role === 'admin') {
            $backends['default'] = [
                'name' => 'default',
                'adapter' => 'local',
                'baseUrl' => config('app.url').'/userfiles/',
                'root' => public_path('/userfiles/'),
                'chmodFiles' => 0777,
                'chmodFolders' => 0755,
                'filesystemEncoding' => 'UTF-8',
            ];
        }

        config(['ckfinder.backends' => $backends]);

        config(['ckfinder.authentication' => function () {
            return true;
        }]);

        // Per-user resource types
        $userImages = [
            'name' => 'My Images',
            'directory' => 'images',
            'maxSize' => '16M',
            'allowedExtensions' => 'bmp,gif,jpeg,jpg,png,webp,svg',
            'deniedExtensions' => '',
            'backend' => "user_{$userId}",
        ];

        $userFiles = [
            'name' => 'My Files',
            'directory' => 'files',
            'maxSize' => '64M',
            'allowedExtensions' => '7z,aiff,asp,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,webp,wma,wmv,xls,xlsx,zip',
            'deniedExtensions' => '',
            'backend' => "user_{$userId}",
        ];

        $resourceTypes = [$userImages, $userFiles];

        // Admin gets global resource types too
        if ($user->role === 'admin') {
            $resourceTypes[] = [
                'name' => 'All Images',
                'directory' => 'images',
                'maxSize' => '16M',
                'allowedExtensions' => 'bmp,gif,jpeg,jpg,png,webp',
                'deniedExtensions' => '',
                'backend' => 'default',
            ];
            $resourceTypes[] = [
                'name' => 'All Files',
                'directory' => 'files',
                'maxSize' => '64M',
                'allowedExtensions' => '7z,aiff,asp,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,webp,wma,wmv,xls,xlsx,zip',
                'deniedExtensions' => '',
                'backend' => 'default',
            ];
        }

        config(['ckfinder.resourceTypes' => $resourceTypes]);

        // ACL — user can only access their own backend files
        config(['ckfinder.accessControl' => [
            [
                'role' => '*',
                'resourceType' => '*',
                'folder' => '/',
                'FOLDER_VIEW' => true,
                'FOLDER_CREATE' => true,
                'FOLDER_RENAME' => true,
                'FOLDER_DELETE' => true,
                'FILE_VIEW' => true,
                'FILE_UPLOAD' => true,
                'FILE_RENAME' => true,
                'FILE_DELETE' => true,
                'IMAGE_RESIZE' => true,
                'IMAGE_RESIZE_CUSTOM' => true,
            ],
        ]]);

        return $next($request);
    }
}
