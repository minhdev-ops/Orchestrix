# Chat Realtime — Kiểm chứng tích hợp Laravel ↔ JakartaEE (Protobuf/WebSocket)

> Trạng thái: **HOÀN THIỆN** — realtime chat hoạt động end-to-end (xác minh thực tế qua WS).
> Ngày cập nhật: 2026-08-15 · Dự án: `Orchestrix` + `03.JakartaEE/JakartaEE`

## 1. Mục đích

Trả lời 2 câu hỏi đang khiến tính năng chat không hoàn thiện:

1. **Hai bên Laravel ↔ JakartaEE đã kết nối với nhau chưa?**
2. **Chúng có thực sự giao tiếp bằng WebSocket + protobuf không?**

Tài liệu này ghi lại kiến trúc hiện tại, các nguyên nhân gốc tìm được, file Pest test đã viết, cách chạy, và hướng hoàn thiện.

---

## 2. Kiến trúc hiện tại (2 luồng tin nhắn song song)

```
 [Trình duyệt A]                    [Trình duyệt B]
      │ REST POST /api/chat/.../messages        │
      ▼                                        │
 [Laravel ChatController@sendMessage]           │
      │ ChatService::sendPrivateMessage         │
      │  (a) XADD chat:messages:stream (history)│
      │  (b) PUBLISH "chat" (realtime)          │
      ▼                                        ▼
        [Redis 127.0.0.1:6379]  (dùng chung)
              │ pub/sub "chat" , "chat:group:*"
              ▼
 [JakartaEE WildFly 8080] ── RedisSubscriber ──┐
      │ ChatEndpoint /ws/chat/{token}           │
      │ SessionManager.sendToUser(To)           │
      ▼                                        │
 [WS handshake tới từng browser] ◄─────────────┘
```

- **Luồng lưu trữ**: REST → `chat_messages` (MySQL) → giúp load lại lịch sử.
- **Luồng realtime**: Laravel `PUBLISH` → Redis → `RedisSubscriber` (JakartaEE) → `sendToUser` → WebSocket tới browser.

---

## 3. Kết quả kiểm chứng (đã chạy Pest)

### 3.1. Đã kết nối? — **CÓ (ở mức Redis), nhưng có 2 lỗi chặn realtime**

| Hạng mục | Trạng thái | Bằng chứng |
|----------|-----------|------------|
| Laravel → Redis publish channel `chat` | ✅ Hoạt động | Pest `healthcheck` + test `publishes...private message` |
| Redis ↔ JakartaEE cùng instance | ✅ `127.0.0.1:6379` | config 2 bên khớp |
| WS handshake WildFly (context `/JakartaEE-1.0-SNAPSHOT`) | ✅ **101 Switching Protocols** | python socket test |
| JakartaEE chấp nhận cross-origin | ✅ | handshake có `Origin: localhost:8000` vẫn 101 |
| JakartaEE đang chạy **bản đã có fix JSON-parse** | ⚠️ **Cần redeploy** | hiện IntelliJ deploy (16:18) trước fix cuối |
| Frontend kết nối WS đúng cổng | ✅ đã config `WS_URL` | meta `ws-url` → WildFly 8080 |

### 3.2. Có thực sự dùng WebSocket + protobuf? — **CHƯA đúng (do 2 lỗi codec)**

| # | Nguyên nhân gốc | Mức độ | Hậu quả |
|---|----------------|--------|---------|
| **1** | **`ChatService` dùng `xadd()` sai cú pháp Predis** ("`ERR wrong number of arguments`") | 🔴 Nghiêm trọng | Không ghi được stream history → JakartaEE không load được lịch sử khi client connect |
| **2** | **Laravel publish JSON string, JakartaEE mới chỉ hiểu protobuf** | 🔴 Nghiêm trọng | Trước fix, message JSON từ Laravel **rơi** tại `parseFrom()` → người nhận không realtime |
| 3 | `RedisSubscriber` chỉ gửi cho `getTo()`, thiếu echo sender | 🟠 **Đã sửa** | Sender không update sidebar realtime |
| 4 | Frontend decoder thiếu field `type/from/to` | 🟠 **Đã sửa** | `handleIncomingMessage` không vào nhánh `private` |

---

## 4. Chi tiết 2 lỗi cốt lõi và cách đã/hây sửa

### Lỗi 1 — Predis xadd sai cú pháp (file: `ChatService.php`)

```php
// TRƯỚC (sai): gọi xadd với 4 tham số kiểu phpredis → Predis tự build lệnh XADD → lỗi args
$this->redis()->xadd('chat:messages:stream', '*', ['data' => $encoded], 50);

// SAU (đúng): dùng executeRaw() với cú pháp XADD chuẩn
$this->streamAdd('chat:messages:stream', $encoded);
// → XADD chat:messages:stream MAXLEN ~ 50 * data <base64>
```

`Predis` class client **không có** method `xadd()`/`xrange()`/`xlen()` (đã kiểm chứng bằng `get_class_methods`). Mọi lệnh stream phải qua `executeRaw()`.

**File đã sửa:** `app/Modules/AgriVerse/Services/ChatService.php` — thêm private method `streamAdd()`.

### Lỗi 2 — Laravel publish JSON, Jakarta parse protobuf (file: `RedisSubscriber.java`)

Laravel `ChatService::buildMessage()` xuất `json_encode(...)` (String), nhưng `RedisSubscriber.handleChatMessage()` parse bằng `ChatProto.ChatMessage.parseFrom(bytes)` (protobuf) → ném exception → message rớt.

**Đã sửa** trong `RedisSubscriber.java`:
- Thêm `parseChatMessage(byte[])`: thử `parseFrom` trước; nếu fail thì parse JSON bằng Gson và build `ChatMessage.Builder`.
- Với `private`: `sendToUser(from, ...)` + `sendToUser(to, ...)` để **echo cả sender**.

### Lỗi 3 (thêm) — History stream JSON bị rớt ở `ChatEndpoint.onOpen`

`ChatEndpoint.onOpen` load lịch sử bằng `ChatProto.ChatMessage.parseFrom(data)` (protobuf) nhưng Laravel ghi stream dạng **base64-JSON** → lịch sử không gửi được khi client connect.

**Đã sửa**: chuyển logig parse thành method dùng chung `ChatUtils.parseChatMessage(byte[])` (xử lý cả protobuf lẫn JSON), được dùng bởi cả `RedisSubscriber` và `ChatEndpoint` để đồng bộ.

### Lỗi 4 & 5 (frontend)

- `useChatSocket.js`: decoder đọc đủ `type=1, from=2, to=3, content=4, timestamp=5`.
- `ChatPanel.vue`: `sendRawMessage` thêm optimistic update sidebar ngay khi gửi.
- WS base URL đọc từ `meta[name="ws-url"]` (config `WS_URL`), fallback `window.location.host` (production nginx `/ws/`).

---

## 5. Pest test đã viết

File: **`tests/Feature/Chat/ChatJakartaIntegrationTest.php`** (group `chat-integration`)

| Test | Xác minh |
|------|----------|
| `publishes a private message JSON string lên Redis channel "chat"` | Laravel ghi đúng JSON vào stream + stream non-empty |
| `Laravel dùng JSON (không) dùng protobuf khi publish` | payload channel là chuỗi JSON bắt đầu `{` |
| `protobuf PHP class ChatMessage serialize đúng schema (1..5)` | round-trip serialize/merge với `Proto\ChatMessage` |
| `frontend decoder cần đọc field type/from/to/content/timestamp` | field 1 (type) = tag `0a` trong protobuf |
| `gửi message qua REST API lưu DB và đẩy lên Redis` | REST → DB + stream |
| `healthcheck kết nối Redis` | `PING` → `PONG` |

### Cách chạy

```bash
cd Orchestrix
./vendor/bin/pest tests/Feature/Chat/

# Chỉ riêng bộ tích hợp JakartaEE
./vendor/bin/pest tests/Feature/Chat/ChatJakartaIntegrationTest.php
```

Kết quả hiện tại: **12 passed (21 assertions)** — gồm `ChatJakartaIntegrationTest` + `ChatTest`.

---

## 6. Trạng thái hoàn thiện (đã xác minh)

**Kết quả E2E (thực tế chạy):** 2 WS client (user 1 + user 2) cùng listen; gửi private message qua **Laravel REST** → Redis → JakartaEE → cả 2 client đều nhận realtime (protobuf đúng `type/from/to/content/timestamp`). Bao gồm system-join + tin nhắn thật. → **PASS: cả sender lẫn recipient nhận realtime.**

Đã hoàn tất:
- [x] `ChatService` dùng `streamAdd()` (XADD đúng cú pháp) cho cả private và group.
- [x] `ChatUtils.parseChatMessage()` dùng chung cho subscriber + endpoint (protobuf + JSON).
- [x] JakartaEE **redeploy** (WAR mới) lên local WildFly `127.0.0.1:8080`.
- [x] Frontend build lại với `WS_URL=ws://127.0.0.1:8080/JakartaEE-1.0-SNAPSHOT`.
- [x] E2E realtime test chạy PASS (script `/tmp/opencode/e2e_chat_test.py`).

Còn lại (khi cần deploy production):
- [ ] Build WAR + deploy lên **server** (không phải local) — production dùng `WS_URL=` rỗng → frontend tự trỏ `window.location.host` (nginx `/ws/` → `ROOT.war` context `/`).
- [ ] Kiểm tra lại khi có nhiều hơn 2 client và sau khi restart WildFly.

---

## 7. Bí quyết debug nhanh

```bash
# 1) Xác nhận Laravel publish và ghi stream đúng:
redis-cli -p 6379 SUBSCRIBE chat          # (terminal 1) nhìn message JSON
redis-cli -p 6379 XRANGE chat:messages:stream - +  # xem base64 history

# 2) WS handshake thật (101 = OK):
python3 -c "
import socket,base64,os
s=socket.create_connection(('127.0.0.1',8080),5)
k=base64.b64encode(os.urandom(16)).decode()
s.sendall((f'GET /JakartaEE-1.0-SNAPSHOT/ws/chat/abc HTTP/1.1\r\nHost: x\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: {k}\r\nSec-WebSocket-Version: 13\r\n\r\n').encode())
print(s.recv(128).decode())
"   # kỳ vọng: HTTP/1.1 101 Switching Protocols

# 3) Log WildFly:
docker logs jakartaee-local        # nếu chạy Docker
cat ~/1.Work/02.Tool/wildfly-39.0.1.Final/standalone/log/server.log
```

---

## 8. Kết luận

- **Hai bên ĐÃ kết nối** qua Redis dùng chung; WebSocket handshake hoạt động (101).
- **Raltime chat đã hoạt động end-to-end** — xác minh bằng E2E script: Laravel REST gửi tin → Redis → JakartaEE `RedisSubscriber` → 2 WS client đều nhận tin realtime (cả sender lẫn recipient).
- Đã sửa 4 lỗi: (1) `xadd()` sai cú pháp Predis làm hỏng history stream; (2) Laravel publish JSON nhưng Jakarta chỉ parse protobuf; (3) `ChatEndpoint.onOpen` load history JSON bằng protobuf-only parse → rớt; (4) sender không nhận echo (giờ `sendToUser(from)+sendToUser(to)`).
- Giờ khi client connect sẽ nhận **lịch sử** (base64-JSON → parse đúng) và **tin nhắn realtime** trong cùng luồng protobuf.

---

## 9. Nguồn code tham chiếu

| Thành phần | Đường dẫn |
|-----------|-----------|
| Laravel send realtime + history | `Orchestrix/app/Modules/AgriVerse/Services/ChatService.php` |
| Laravel REST controller | `Orchestrix/app/Modules/AgriVerse/Http/Controllers/Api/ChatController.php` |
| Protobuf PHP (schema 1..5) | `Orchestrix/app/Modules/AgriVerse/Protobuf/Proto/ChatMessage.php` |
| JakartaEE WS endpoint | `03.JakartaEE/JakartaEE/src/main/java/websocket/ChatEndpoint.java` |
| JakartaEE Redis subscriber (đã fix JSON+echo) | `03.JakartaEE/JakartaEE/src/main/java/redis/channel/RedisSubscriber.java` |
| JakartaEE shared parser (protobuf+JSON) | `03.JakartaEE/JakartaEE/src/main/java/websocket/ChatUtils.java` |
| JakartaEE Redis stream decode | `03.JakartaEE/JakartaEE/src/main/java/service/RedisService.java` |
| Frontend WS connect + decoder | `Orchestrix/app/Modules/AgriVerse/Resources/js/Composables/useChatSocket.js` |
| Frontend sidebar logic | `Orchestrix/app/Modules/AgriVerse/Resources/js/Components/ChatPanel.vue` |
| Proto schema | `03.JakartaEE/JakartaEE/src/main/resources/proto/chat.proto` |
| WS meta config | `Orchestrix/resources/views/app.blade.php` · `Orchestrix/config/agriverse.php` · `.env` |
| Pest test (mới) | `Orchestrix/tests/Feature/Chat/ChatJakartaIntegrationTest.php` |