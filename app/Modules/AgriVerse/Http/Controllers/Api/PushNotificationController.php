<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Services\PushNotificationService;

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
    public function stats()
    {
        return response()->json($this->pushService->getStats());
    }
}
