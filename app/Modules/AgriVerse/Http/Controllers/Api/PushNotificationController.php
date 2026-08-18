<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Services\PushNotificationService;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    protected PushNotificationService $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Register device token
     */
    public function register(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|string|in:web,android,ios',
            'device_name' => 'nullable|string|max:255',
        ]);

        $deviceToken = $this->pushService->registerToken(
            $request->user(),
            $request->token,
            $request->platform,
            $request->device_name
        );

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký nhận thông báo thành công.',
            'device' => $deviceToken,
        ]);
    }

    /**
     * Unregister device token
     */
    public function unregister(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $device = \App\Modules\AgriVerse\Models\DeviceToken::where('token', $request->token)->first();

        if (! $device || $device->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $deleted = $this->pushService->unregisterToken($request->token);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Đã hủy nhận thông báo.' : 'Token không tồn tại.',
        ]);
    }

    /**
     * Get user devices
     */
    public function devices(Request $request)
    {
        $devices = $this->pushService->getUserTokens($request->user());

        return response()->json([
            'data' => $devices,
        ]);
    }

    /**
     * Send test notification
     */
    public function test(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
        ]);

        $result = $this->pushService->sendToUser(
            $request->user(),
            $request->title,
            $request->body,
            ['type' => 'test']
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi thông báo test.',
            'result' => $result,
        ]);
    }

    /**
     * Get notification stats (admin)
     */
    public function stats(Request $request)
    {
        if (! $request->user()?->hasRole('admin')) {
            abort(403);
        }

        return response()->json($this->pushService->getStats());
    }
}
