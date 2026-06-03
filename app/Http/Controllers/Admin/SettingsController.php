<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Portfolio\Models\Setting;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            // Xác định group dựa trên prefix của key hoặc mặc định
            $group = 'general';
            if (str_starts_with($key, 'mail_')) $group = 'mail';
            if (str_starts_with($key, 'storage_')) $group = 'storage';

            Setting::set($key, $value, $group);
        }

        // Clear config cache để nhận cấu hình mới nếu cần
        // Artisan::call('config:clear');

        return back()->with('success', 'Cài đặt hệ thống đã được cập nhật.');
    }
}
