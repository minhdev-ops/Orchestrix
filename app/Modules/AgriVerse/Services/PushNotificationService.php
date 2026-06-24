<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Modules\AgriVerse\Models\DeviceToken;

class PushNotificationService
{
    protected string $projectId = '';
    protected string $serviceAccountKey = '';

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id') ?? '';
        $this->serviceAccountKey = config('services.firebase.credentials') ?? '';
    }

    /**
     * Send push notification to a user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::where('user_id', $user->id)
            ->active()
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return ['success' => 0, 'failed' => 0, 'message' => 'No device tokens found'];
        }

        return $this->sendToMultiple($tokens, $title, $body, $data);
    }

    /**
     * Send push notification to multiple tokens
     */
    public function sendToMultiple(array $tokens, string $title, string $body, array $data = []): array
    {
        $success = 0;
        $failed = 0;
        $invalidTokens = [];

        foreach ($tokens as $token) {
            try {
                $result = $this->sendFcmMessage($token, $title, $body, $data);
                if ($result['success']) {
                    $success++;
                } else {
                    $failed++;
                    if (isset($result['invalid_token']) && $result['invalid_token']) {
                        $invalidTokens[] = $token;
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error('Push notification failed', ['token' => $token, 'error' => $e->getMessage()]);
            }
        }

        // Cleanup invalid tokens
        if (!empty($invalidTokens)) {
            DeviceToken::whereIn('token', $invalidTokens)->delete();
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'invalid_tokens' => count($invalidTokens),
        ];
    }

    /**
     * Send to topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        return $this->sendFcmMessage("/topics/{$topic}", $title, $body, $data);
    }

    /**
     * Send FCM message via HTTP v1 API
     */
    protected function sendFcmMessage(string $token, string $title, string $body, array $data = []): array
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map(fn ($v) => (string) $v, $data),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'agriverse_default',
                        'sound' => 'default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
                'webpush' => [
                    'headers' => [
                        'TTL' => '86400',
                    ],
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if ($response->successful()) {
            return ['success' => true];
        }

        $error = $response->json('error', []);

        // Check if token is invalid
        if (in_array($error['code'] ?? null, [404, 400])) {
            return ['success' => false, 'invalid_token' => true, 'message' => $error['message'] ?? 'Unknown error'];
        }

        return ['success' => false, 'message' => $error['message'] ?? 'Unknown error'];
    }

    /**
     * Get OAuth2 access token
     */
    protected function getAccessToken(): ?string
    {
        try {
            $serviceAccount = json_decode($this->serviceAccountKey, true);

            if (!$serviceAccount) {
                Log::error('Invalid Firebase service account key');
                return null;
            }

            $now = time();
            $expiry = $now + 3600;

            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = $this->base64UrlEncode(json_encode([
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $expiry,
            ]));

            $signatureInput = "{$header}.{$payload}";

            openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], 'SHA256');
            $signature = $this->base64UrlEncode($signature);

            $jwt = "{$header}.{$payload}.{$signature}";

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Failed to get Firebase access token', $response->json());
            return null;
        } catch (\Exception $e) {
            Log::error('Firebase auth error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Register device token
     */
    public function registerToken(User $user, string $token, string $platform = 'web', ?string $deviceName = null): DeviceToken
    {
        // Check if token already exists
        $existing = DeviceToken::where('token', $token)->first();

        if ($existing) {
            $existing->touchLastUsed();
            return $existing;
        }

        return DeviceToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'platform' => $platform,
            'device_name' => $deviceName,
            'last_used_at' => now(),
        ]);
    }

    /**
     * Unregister device token
     */
    public function unregisterToken(string $token): bool
    {
        return DeviceToken::where('token', $token)->delete() > 0;
    }

    /**
     * Unregister all user tokens
     */
    public function unregisterAllTokens(User $user): int
    {
        return DeviceToken::where('user_id', $user->id)->delete();
    }

    /**
     * Get user tokens
     */
    public function getUserTokens(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return DeviceToken::where('user_id', $user->id)->get();
    }

    /**
     * Cleanup inactive tokens
     */
    public function cleanupInactiveTokens(int $days = 90): int
    {
        $cutoff = now()->subDays($days);
        return DeviceToken::where('last_used_at', '<', $cutoff)->delete();
    }

    /**
     * Base64 URL encode
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Send order notification
     */
    public function sendOrderNotification(User $user, string $type, array $orderData): array
    {
        $messages = [
            'created' => [
                'title' => 'Đơn hàng mới',
                'body' => "Đơn hàng #{$orderData['id']} đã được tạo thành công.",
            ],
            'confirmed' => [
                'title' => 'Đơn hàng xác nhận',
                'body' => "Đơn hàng #{$orderData['id']} đã được xác nhận.",
            ],
            'shipping' => [
                'title' => 'Đơn hàng đang giao',
                'body' => "Đơn hàng #{$orderData['id']} đang được giao đến bạn.",
            ],
            'delivered' => [
                'title' => 'Đơn hàng đã giao',
                'body' => "Đơn hàng #{$orderData['id']} đã được giao thành công.",
            ],
            'cancelled' => [
                'title' => 'Đơn hàng hủy',
                'body' => "Đơn hàng #{$orderData['id']} đã bị hủy.",
            ],
        ];

        $message = $messages[$type] ?? $messages['created'];

        return $this->sendToUser($user, $message['title'], $message['body'], array_merge([
            'type' => 'order',
            'order_id' => (string) $orderData['id'],
            'action' => $type,
        ], $orderData));
    }

    /**
     * Send chat notification
     */
    public function sendChatNotification(User $user, string $senderName, string $message): array
    {
        return $this->sendToUser($user, "Tin nhắn từ {$senderName}", $message, [
            'type' => 'chat',
        ]);
    }

    /**
     * Send promotion notification
     */
    public function sendPromotionNotification(User $user, string $title, string $body, string $promoCode = null): array
    {
        $data = ['type' => 'promotion'];
        if ($promoCode) {
            $data['promo_code'] = $promoCode;
        }

        return $this->sendToUser($user, $title, $body, $data);
    }

    /**
     * Broadcast promotion to all users
     */
    public function broadcastPromotion(string $title, string $body, string $promoCode = null): array
    {
        $tokens = DeviceToken::active()->pluck('token')->toArray();

        $data = ['type' => 'promotion'];
        if ($promoCode) {
            $data['promo_code'] = $promoCode;
        }

        return $this->sendToMultiple($tokens, $title, $body, $data);
    }

    /**
     * Get notification stats
     */
    public function getStats(): array
    {
        return [
            'total_tokens' => DeviceToken::count(),
            'active_tokens' => DeviceToken::active()->count(),
            'by_platform' => DeviceToken::select('platform', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
                ->groupBy('platform')
                ->pluck('count', 'platform')
                ->toArray(),
        ];
    }
}
