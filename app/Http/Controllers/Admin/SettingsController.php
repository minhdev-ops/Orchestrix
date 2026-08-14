<?php

/**
 * @deprecated Settings management is being migrated to the AgriVerse module.
 * This controller uses Blade views; the module version uses Inertia SPA.
 * Kept for backward compatibility. Will be removed in next major version.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings');
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|string|max:10',
            'mail_encryption' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'storage_driver' => 'nullable|string|max:50',
        ]);

        return back()->with('success', 'Cài đặt hệ thống đã được cập nhật.');
    }
}
