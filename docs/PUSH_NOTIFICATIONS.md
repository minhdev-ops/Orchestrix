# Push Notifications - Complete Implementation

## ✅ Đã Triển Khai

### Push Notification Service (FCM)
**Files created:**
- `app/Modules/AgriVerse/Models/DeviceToken.php`
- `app/Modules/AgriVerse/Services/PushNotificationService.php`
- `app/Modules/AgriVerse/Http/Controllers/Api/PushNotificationController.php`
- `routes/push_notifications.php`
- `database/migrations/2026_06_15_000006_create_device_tokens_table.php`
- `config/services.php` (firebase section)

### Features
- Register/unregister device tokens
- Send push to individual user
- Send push to multiple tokens
- Send to topic (FCM topics)
- Order notifications (created, confirmed, shipping, delivered, cancelled)
- Chat notifications
- Promotion broadcast
- Device tracking (platform, device_name, last_used)
- Auto-cleanup inactive tokens (90 days)
- Notification stats

### Database Table
- `device_tokens` - User device tokens

### API Endpoints
```
POST /api/notifications/push/register    - Register device token
POST /api/notifications/push/unregister  - Unregister device token
GET  /api/notifications/push/devices     - List user devices
POST /api/notifications/push/test        - Send test notification
```

### Usage
```php
$pushService = app(PushNotificationService::class);

// Register device
$pushService->registerToken($user, $token, 'web', 'Chrome');

// Send to user
$pushService->sendToUser($user, 'Tiêu đề', 'Nội dung');

// Send order notification
$pushService->sendOrderNotification($user, 'confirmed', ['id' => 123]);

// Send chat notification
$pushService->sendChatNotification($user, 'Nguyễn Văn A', 'Xin chào!');

// Broadcast promotion
$pushService->broadcastPromotion('Giảm 20%', 'Sử dụng mã SALE20', 'SALE20');

// Get stats
$stats = $pushService->getStats();
```

### Firebase Setup
1. Tạo Firebase project tại https://console.firebase.google.com
2. Enable Cloud Messaging
3. Tạo Service Account Key (JSON)
4. Copy JSON vào `FIREBASE_CREDENTIALS` trong `.env`
5. Set `FIREBASE_PROJECT_ID` trong `.env`

### Client-Side Integration
```javascript
// Register FCM
import { getMessaging, getToken } from 'firebase/messaging';

const messaging = getMessaging();
const token = await getToken(messaging, {
    vapidKey: 'YOUR_VAPID_KEY'
});

// Send token to server
fetch('/api/notifications/push/register', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify({
        token: token,
        platform: 'web'
    })
});

// Handle notifications
messaging.onMessage((payload) => {
    console.log('Notification:', payload);
});
```

---

*Generated: 2026-06-15*
