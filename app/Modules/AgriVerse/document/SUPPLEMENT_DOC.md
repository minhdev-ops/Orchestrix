# Bổ sung tài liệu IT-Challenge — AgriVerse

> File này bổ sung những nội dung còn thiếu hoặc chưa chi tiết trong file `NguyenQuangMinh_23130193_IT_Challenge Bang B.docx`

---

## 1. Tính năng đã triển khai nhưng chưa được mô tả đầy đủ trong tài liệu

### 1.1. Hệ thống Affiliate (Tiếp thị liên kết)
- Người dùng đăng ký làm affiliate, nhận referral link
- Dashboard theo dõi hoa hồng, số referral, tỷ lệ chuyển đổi
- Mô hình tính hoa hồng theo đơn hàng thành công

### 1.2. Hệ thống Vườn cá nhân (My Garden)
- Người dùng có thể tạo vườn ảo, chia_zone, theo dõi sự phát triển cây
- Gắn ảnh, ghi chú theo thời gian cho từng cây trong vườn

### 1.3. So sánh sản phẩm (Compare)
- So sánh side-by-side nhiều sản phẩm trên cùng 1 bảng
- So sánh thông số kỹ thuật, giá, đánh giá
- Lưu lịch sử so sánh qua localStorage

### 1.4. Hệ thống Hợp đồng điện tử (E-Contract)
- Tạo hợp đồng mua bán giữa Buyer và Seller
- Ký số, theo dõi trạng thái hợp đồng (draft → signed → active → expired)

### 1.5. Hệ thống Hoàn tiền (Refund)
- Buyer có thể yêu cầu hoàn tiền với lý do cụ thể
- Seller/Admin xử lý yêu cầu hoàn tiền
- Workflow: pending → approved/rejected → refunded

### 1.6. Xác thực 2FA (Two-Factor Authentication)
- Hỗ trợ TOTP (Google Authenticator)
- Recovery codes khi mất thiết bị
- Cài đặt/trong phần Settings/Security

### 1.7. Hệ thống Phân tích (Analytics)
- Theo dõi lượt xem sản phẩm, hành vi người dùng
- Dashboard Analytics cho Admin và Seller
- Service `AnalyticsService` ghi lại sự kiện

### 1.8. Hệ thống Push Notification
- Service `PushNotificationService` hỗ trợ gửi thông báo push
- Quản lý device token qua model `DeviceToken`

### 1.9. Tối ưu hình ảnh (Image Optimization)
- Service `ImageOptimization` tự động resize/compress ảnh upload
- Hỗ trợ thumbnail cho product listing

### 1.10. SEO Service
- Service `SeoService` tự động tạo meta tags, structured data (JSON-LD)
- Hỗ trợ Open Graph tags cho share lên mạng xã hội

### 1.11. Đăng ký/Ký gửi bán hàng (Seller Verification)
- Seller đăng ký qua form `Seller/Register.vue`
- Admin duyệt qua `SellerVerification` model
- Dashboard quản lý đơn hàng cho Seller

### 1.12. Hệ thống Voucher & Mã giảm giá (Coupon)
- Model `Coupon` và `UserVoucher`
- Áp dụng mã giảm giá khi thanh toán

### 1.13. Hệ thống Banner quảng cáo
- Model `Banner` cho trang chủ

### 1.14. Đánh giá sản phẩm (Reviews)
- Buyer đánh giá sau khi nhận hàng
- Seller quản lý đánh giá qua `Seller/Reviews/Index.vue`

### 1.15. Hệ thống Tag & Manufacturer
- `Tag` cho phân loại/tagging sản phẩm
- `Manufacturer` cho thông tin nhà sản xuất

### 1.16. Product Variant & Product Type
- Hỗ trợ nhiều biến thể sản phẩm (size, màu sắc...)
- Phân loại theo loại cây (`ProductType`)

### 1.17. Hệ thống Specimen (Mẫu vật)
- Model `Specimen` lưu thông tin mẫu vật cụ thể

### 1.18. Hệ thống BonsaiStyle
- Model `BonsaiStyle` phân loại phong cách bonsai

### 1.19. Hệ thống Subscription (Gói dịch vụ)
- Model `SubscriptionPlan`, `Subscription`, `StoreSubscription`
- Các gói dịch vụ cho Seller (nâng cấp cửa hàng)

---

## 2. Công nghệ cần bổ sung hoặc chỉnh sửa trong tài liệu

### 2.1. Backend API
- Laravel 12 + PHP 8.2 (đúng)
- JakartaEE (Java) xử lý WebSocket chat + Protobuf — **đã triển khai** qua Docker container WildFly
- Protocol Buffers (Protobuf) — có thư mục `Protobuf/` với file `.proto`

### 2.2. Frontend
- Vue 3 Composition API + Inertia.js (đúng)
- PrimeVue được sử dụng làm UI component library (Toast, ConfirmDialog...)
- Ziggy.js để gọi named routes từ JS

### 2.3. Database
- MySQL (đúng)
- Redis cho cache + queue + WebSocket broadcasting

### 2.4. Vận chuyển
- **Đã chuyển từ GHN sang GHTK** (Giao Hàng Tiết Kiệm) — cần cập nhật tài liệu
- GHTK API tính phí vận chuyển real-time
- Fallback mock khi chưa cấu hình token

### 2.5. AI
- AIPlantDoctorService — tích hợp AI chẩn đoán bệnh cây
- **Gemini Pro Vision** cần xác nhận cấu hình API key

### 2.6. Hộ chiếu số (Digital Passport)
- Model `DigitalPassportLog` lưu lịch sử sự kiện (ordered, transferred, awarded...)
- UUID cho mỗi sản phẩm
- **Chưa có Blockchain/NFT** — chỉ là passport cơ bản, không phải NFT

---

## 3. Kiến trúc hệ thống cần bổ sung chi tiết

### 3.1. Modular Monolith
```
app/Modules/AgriVerse/
├── Console/          # Artisan commands
├── Database/         # Migrations, Seeders
├── Events/           # Laravel Events
├── Exceptions/       # Custom exceptions
├── Http/Controllers/ # Controller logic
├── Jobs/             # Queue jobs
├── Listeners/        # Event listeners
├── Models/           # Eloquent models (60+ models)
├── Notifications/    # Email/notification templates
├── Policies/         # Authorization policies
├── Protobuf/         # Protocol Buffer definitions
├── Providers/        # Service providers
├── Routes/           # Route definitions (shop, admin, api)
├── Services/         # Business logic services (25+ services)
└── Resources/        # Vue components, pages, layouts
```

### 3.2. Container Architecture
```
docker-compose.yml:
├── nginx         # Reverse proxy
├── php           # Laravel application
├── queue         # Laravel queue worker
├── scheduler     # Laravel scheduler
├── mysql         # Database
├── redis         # Cache + Session + Queue + WebSocket
└── wildfly       # JakartaEE WebSocket server
```

### 3.3. Real-time Architecture
- Laravel Reverb (WebSocket broadcasting) cho通知 và Forum events
- JakartaEE WildFly container xử lý chat WebSocket riêng
- Redis Pub/Sub làm message broker giữa Laravel và JakartaEE

---

## 4. Chức năng Checkout & Thanh toán cần bổ sung chi tiết

### 4.1. Flow Checkout
1. Thêm sản phẩm vào Giỏ hàng (Cart)
2. Chọn/Thêm địa chỉ giao hàng (tỉnh → huyện → xã từ DB)
3. Hệ thống tự động tính phí vận chuyển qua GHTK API
4. Xác nhận đơn hàng → Tạo Order + OrderStatus
5. Chuyển hướng trang thành công

### 4.2. Thanh toán
- **Hiện tại**: Thanh toán trực tiếp (Buyer liên hệ Seller)
- PaymentService đã có nhưng chưa tích hợp cổng thanh toán online
- Banking/Index.vue hỗ trợ chuyển khoản ngân hàng (QR code)

---

## 5. Các điểm cần chỉnh sửa trong tài liệu gốc

| # | Nội dung cần sửa | Chi tiết |
|---|---|---|
| 1 | Mục 4.3 "API Giao Hàng Nhanh (GHN)" | Đã chuyển sang GHTK (Giao Hàng Tiết Kiệm) |
| 2 | Mục 3.1 Chức năng 6 | Cần bổ sung chi tiết flow Checkout, Cart, Address selector (Tỉnh→Huyện→Xã) |
| 3 | Mục 4.2 "Google Protocol Buffers" | Đúng nhưng cần giải thích rõ hơn用途: chat message serialization |
| 4 | Mục 4.4 "Reverb" | Cần xác nhận đang dùng Reverb hay WebSocket server riêng (JakartaEE) |
| 5 | Mục 6 Giai đoạn 2 | Payment Gateway vẫn đang trong giai đoạn phát triển |
| 6 | Mục 5 Kiến trúc | Cần bổ sung sơ đồ Container Architecture (Docker) |
| 7 | Mục 3.2 Chức năng vòng 2 | AI Plant Doctor **đã có** trong vòng 1 (AIPlantDoctorService) |
| 8 | Mục 1.4 Lợi ích | Cần bổ sung: Affiliate, E-Contract, Refund, Voucher |

---

## 6. Danh sách đầy đủ tính năng theo Module

| Module | Mô tả | Trạng thái |
|--------|--------|-----------|
| Shop (Products) | Đăng/bán sản phẩm, variants, ảnh, 3D | ✅ Hoàn thành |
| Shop (Stores) | Cửa hàng người bán, verification | ✅ Hoàn thành |
| Shop (Cart/Checkout) | Giỏ hàng, Checkout, GHTK shipping | ✅ Hoàn thành |
| Shop (Orders) | Quản lý đơn hàng, trạng thái, timeline | ✅ Hoàn thành |
| Shop (Chat) | Chat real-time WebSocket | ✅ Hoàn thành |
| Shop (Forum) | Diễn đàn cộng đồng | ✅ Hoàn thành |
| Shop (Journal) | Thư viện bài viết/knowledge base | ✅ Hoàn thành |
| Shop (Garden) | Vườn cá nhân | ✅ Hoàn thành |
| Shop (Quiz) | Tìm mẫu cây phù hợp | ✅ Hoàn thành |
| Shop (Diagnostic) | Chẩn đoán bệnh cây (AI) | ✅ Hoàn thành |
| Shop (Sustainability) | Báo cáo bền vững | ✅ Hoàn thành |
| Shop (AR) | Trình xem AR | ✅ Hoàn thành |
| Shop (Compare) | So sánh sản phẩm | ✅ Hoàn thành |
| Shop (Contract) | Hợp đồng điện tử | ✅ Hoàn thành |
| Shop (Affiliate) | Tiếp thị liên kết | ✅ Hoàn thành |
| Shop (Tracking) | Theo dõi vận chuyển | ✅ Hoàn thành |
| Seller | Dashboard, quản lý đơn, sản phẩm | ✅ Hoàn thành |
| Admin | Quản trị hệ thống | ✅ Hoàn thành |
| Auth | Đăng nhập, 2FA, quên mật khẩu | ✅ Hoàn thành |
| Payment (Online) | Cổng thanh toán online | 🔄 Đang phát triển |
| Auction | Đấu giá real-time | ❌ Chưa triển khai |
| Virtual Pruning | Mô phỏng cắt tỉa 3D | ❌ Chưa triển khai |
| NFT Passport | Blockchain NFT hộ chiếu | ❌ Chưa triển khai |
| Mobile App | Ứng dụng di động Flutter | ❌ Chưa triển khai |

---

## 7. Tài liệu tham khảo bổ sung

- Laravel Reverb (WebSocket): https://reverb.laravel.com
- JakartaEE WebSocket: https://jakarta.ee/specifications/websocket/
- Google Protocol Buffers: https://protobuf.dev
- GHTK API: https://docs.giaohangtietkiem.vn
- Google Model Viewer: https://modelviewer.dev
- PrimeVue: https://primevue.org
- Ziggy.js (Laravel routes in JS): https://ziggy.dev
- Vue 3 Composition API: https://vuejs.org/guide/extras/composition-api-faq.html
- Inertia.js: https://inertiajs.com
