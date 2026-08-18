# BỘ GIÁO DỤC VÀ ĐÀO TẠO
# TRƯỜNG ĐẠI HỌC NÔNG LÂM TP HCM
## KHOA CÔNG NGHỆ THÔNG TIN

---

# BẢN THUYẾT MINH
## MÔ HÌNH, SẢN PHẨM THAM DỰ CUỘC THI
## "IT-CHALLENGE" LẦN II NĂM 2026

Kính gửi: BTC CUỘC THI "IT-CHALLENGE"
(Khoa Công nghệ Thông tin, Trường Đại học Nông Lâm Tp. Hồ Chí Minh)

---

# NGHIÊN CỨU VÀ XÂY DỰNG HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ CÂY CẢNH NGHỆ THUẬT TÍCH HỢP CÔNG NGHỆ 3D VÀ TRUY XUẤT NGUỒN GỐC SỐ

---

## Danh Sách Thành Viên

| | HỌ VÀ TÊN | MSSV | LỚP | EMAIL | SỐ ĐIỆN THOẠI |
|---|---|---|---|---|---|
| Nhóm trưởng | Nguyễn Quang Minh | 23130193 | DH23DTC | nguyenquangminhitnlu@gmail.com | 0933194203 |
| Thành viên 2 | — | 24130029 | DH24DTA | nguye0707@gmail.com | 0394360026 |

---

## MỤC LỤC

1. [Tóm tắt sản phẩm](#1-tóm-tắt-sản-phẩm)
   - 1.1 Tổng quan sản phẩm
   - 1.2 Mô tả ngắn gọn
   - 1.3 Đối tượng sử dụng
   - 1.4 Lợi ích nổi bật
2. [Lý do chọn đề tài](#2-lý-do-chọn-đề-tài)
3. [Chức năng sản phẩm](#3-chức-năng-sản-phẩm)
   - 3.1 Chức năng hiện tại (Vòng 1)
   - 3.2 Chức năng phát triển (Vòng 2)
4. [Công nghệ áp dụng](#4-công-nghệ-áp-dụng)
   - 4.1 Công nghệ phần cứng
   - 4.2 Công nghệ phần mềm
   - 4.3 Công nghệ tích hợp & Hệ hạ tầng
   - 4.4 Ưu điểm công nghệ
5. [Kiến trúc hệ thống](#5-kiến-trúc-hệ-thống)
   - 5.1 Kiến trúc tổng quan
   - 5.2 Luồng dữ liệu
   - 5.3 Container Architecture (Docker)
   - 5.4 Bảo mật và hiệu suất
6. [Phương hướng phát triển](#6-phương-hướng-phát-triển)
   - 6.1 Giai đoạn 1 (Vòng 1)
   - 6.2 Giai đoạn 2 (Vòng 2)
   - 6.3 Giai đoạn 3 (Tương lai)
7. [Công cụ hỗ trợ](#7-công-cụ-hỗ-trợ)
8. [Mô tả giao diện & Demo](#8-mô-tả-giao-di-demo)
9. [Danh sách tính năng đầy đủ](#9-danh-sách-tính-năng-đầy-đủ)
10. [Tài liệu tham khảo](#10-tài-liệu-tham-khảo)
11. [Lời cảm ơn](#11-lời-cảm-ơn)

---

# 1. Tóm tắt sản phẩm

## 1.1. Tổng quan sản phẩm

AgriVerse là một nền tảng thương mại điện tử (Ecommerce) thế hệ mới, được thiết kế chuyên biệt và tối ưu hóa cho thị trường giao dịch cây cảnh nghệ thuật và bonsai cao cấp. Hệ thống không chỉ dừng lại ở việc mua bán trực tuyến thông thường mà còn hướng tới việc xây dựng một hệ sinh thái toàn diện bao gồm: thương mại đa người bán (Multi-vendor Marketplace), trực quan hóa tác phẩm bằng công nghệ 3D/AR, truy xuất nguồn gốc sinh học qua Hộ chiếu số (Digital Plant Passport), kết nối cộng đồng nghệ nhân thông qua diễn đàn và thư viện tri thức, cùng các công cụ quản lý vườn cá nhân, chẩn đoán bệnh cây bằng AI, và so sánh sản phẩm thông minh.

Hệ thống được triển khai dưới dạng Modular Monolith trên Laravel, với thời gian real-time xử lý bởi JakartaEE WebSocket server, đảm bảo khả năng mở rộng và bảo trì dễ dàng.

## 1.2. Mô tả ngắn gọn

Sản phẩm là một sàn giao dịch đa người bán (Multi-vendor Marketplace). Tại đây, các nhà vườn và nghệ nhân có thể đăng bán các tác phẩm bonsai của mình dưới dạng mô hình 3D xoay 360 độ. Người mua có thể tương tác thực tế ảo, xem xét chi tiết từng cành lá, thân, tra cứu lịch sử phát triển của cây qua Hộ chiếu số, và giao tiếp trực tiếp với người bán qua hệ thống chat real-time. Hệ thống cũng cung cấp một không gian diễn đàn, kho tài liệu chuyên ngành, và vườn cá nhân để người dùng theo dõi sự phát triển của cây.

Toàn bộ dữ liệu được quản lý tập trung qua MySQL, cache và pub/sub qua Redis, giao tiếp real-time qua WebSocket (JakartaEE WildFly container) và broadcast qua Laravel Reverb.

## 1.3. Đối tượng sử dụng

Hệ thống được thiết kế hướng tới 3 nhóm đối tượng chính:

**Người mua (Buyers / Người sưu tầm):** Những cá nhân có niềm đam mê với sinh vật cảnh, mong muốn tìm kiếm và sở hữu những tác phẩm bonsai đẹp, rõ ràng về nguồn gốc và hình dáng. Họ cũng là những người cần học hỏi kỹ thuật chăm sóc từ cộng đồng, so sánh sản phẩm, và theo dõi đơn hàng real-time.

**Người bán (Sellers / Nghệ nhân, Nhà vườn):** Các chủ nhà vườn, nghệ nhân tạo tác cây cảnh cần một không gian chuyên nghiệp, đẳng cấp để trưng bày và thương mại hóa các tác phẩm của mình dưới dạng 3D/AR, tiếp cận đúng tệp khách hàng tiềm năng, quản lý đơn hàng, và chat trực tiếp với người mua.

**Quản trị viên và Chuyên gia (Admins / Experts):** Đội ngũ vận hành hệ thống, kiểm duyệt nội dung, quản lý giao dịch, xử lý khiếu nại và hoàn tiền, và các chuyên gia tham gia vào việc hỗ trợ chẩn đoán bệnh lý thực vật, định giá tác phẩm.

## 1.4. Lợi ích nổi bật

**Xóa bỏ khoảng cách không gian:** Công nghệ 3D và AR (Thực tế tăng cường) giúp người mua cảm nhận độ chân thực của cây cảnh như đang đứng tại vườn, giải quyết triệt để vấn đề "mua hàng qua ảnh". Trình xem 3D xoay 360° sử dụng Google Model Viewer với nén Draco giúp tải nhanh trên mọi thiết bị.

**Đảm bảo tính minh bạch và giá trị:** Hộ chiếu thực vật số (Digital Plant Passport) lưu trữ toàn bộ lịch sử cắt tỉa, chuyển nhượng, giải thưởng, giúp gia tăng giá trị và tính độc bản của tác phẩm nghệ thuật. Mỗi sản phẩm được cấp UUID duy nhất.

**Kiến tạo cộng đồng chuyên sâu:** Tích hợp diễn đàn (Forum) với chức năng upvote, bình luận, đăng bài đa phương tiện; thư viện tri thức (Knowledge Base/Journal) với hơn 20 bài viết chuyên ngành; và vườn cá nhân (My Garden) giúp người dùng theo dõi quá trình phát triển cây.

**Tối ưu vận chuyển hàng đặc thù:** Hệ thống tự động tính toán chi phí vận chuyển phức tạp cho sinh vật sống (kèm chậu, đất) qua API Giao Hàng Tiết Kiệm (GHTK) real-time, với mã bưu điện tỉnh → huyện → xã.

**Kết nối Buyer-Seller tức thì:** Hệ thống chat real-time qua WebSocket (JakartaEE WildFly) hỗ trợ nhắn tin văn bản, hình ảnh, chia sẻ thẻ sản phẩm, lưu trữ bảo mật làm bằng chứng giao dịch.

**So sánh thông minh:** Cho phép người dùng đặt nhiều sản phẩm lên bảng so sánh side-by-side, xem thông số kỹ thuật, giá, đánh giá để đưa ra quyết định mua hàng chính xác.

**Trợ AI:** Hệ thống tích hợp AI chẩn đoán bệnh lý thực vật từ hình ảnh, giúp người dùng xác định vấn đề và nhận gợi ý chăm sóc.

**Hệ thống Affiliate:** Người dùng có thể giới thiệu sản phẩm, theo dõi hoa hồng và referral link.

---

# 2. Lý do chọn đề tài

Thị trường cây cảnh và bonsai tại Việt Nam cũng như trên thế giới có giá trị kinh tế rất lớn, tuy nhiên lại đang bị bỏ ngỏ trong tiến trình chuyển đổi số. Việc mua bán trực tuyến trên các nền tảng mạng xã hội hoặc sàn TMĐT đại trà hiện nay gặp nhiều rào cản:

**Rủi ro "treo đầu dê bán thịt chó":** Hình ảnh 2D dễ bị chỉnh sửa, không thể hiện được chiều sâu, độ vặn xoắn của thân cây, bộ đế (rễ), khiến người mua dễ bị lừa đảo hoặc thất vọng khi nhận hàng. AgriVerse giải quyết bằng mô hình 3D xoay 360° và AR.

**Mất mát thông tin quý giá:** Một tác phẩm bonsai có thể mất hàng chục năm để tạo hình qua nhiều đời chủ, nhưng những thông tin này thường chỉ được truyền miệng, dẫn đến việc khó định giá chính xác. AgriVerse giải quyết bằng Hộ chiếu số (Digital Plant Passport).

**Thiếu hụt kênh tri thức tập trung:** Người chơi mới rất khó tiếp cận các kỹ thuật chăm sóc, cắt tỉa đúng chuẩn, dẫn đến tỷ lệ cây chết sau khi mua cao. AgriVerse giải quyết bằng thư viện Journal và diễn đàn Forum.

**Khó vận chuyển hàng đặc thù:** Cây cảnh sống cần quy trình đóng gói và vận chuyển đặc biệt. AgriVerse tích hợp API GHTK tính phí real-time theo mã tỉnh → huyện → xã.

**Thiếu cơ chế bảo vệ giao dịch:** Không có hợp đồng điện tử, cơ chế hoàn tiền, hay escrow cho giao dịch cây cảnh giá trị cao. AgriVerse xây dựng hệ thống E-Contract, Refund workflow, và xác thực 2FA.

Nhận thấy những "nỗi đau" (pain points) này của thị trường, đề tài được lựa chọn nhằm nghiên cứu, ứng dụng các công nghệ tiên tiến (3D/AR, Digital Passport, WebSockets, AI, Protobuf) để xây dựng một giải pháp toàn diện. Mục tiêu là số hóa các tác phẩm nghệ thuật sống, chuẩn hóa quy trình giao dịch và xây dựng cộng đồng tri thức vững mạnh.

---

# 3. Chức năng sản phẩm

## 3.1. Chức năng hiện tại (Vòng 1) ✅

### Chức năng 1: Trình diễn tác phẩm Bonsai 3D & AR

Hệ thống cho phép người bán tải lên mô hình 3D (.glb/.gltf). Trình xem 3D tích hợp WebGL (Google Model Viewer) xoay 360°, phóng to/thu nhỏ, nén Draco Compression cho di động. Tính năng AR đặt mô hình ảo vào không gian thật qua WebXR.

**Công nghệ:** Google Model Viewer, WebXR, Draco Compression, Three.js

**Trang:** `Products/Show.vue`, `AR/Index.vue`, `Components/ARViewer.vue`

---

### Chức năng 2: Hộ chiếu thực vật số (Digital Plant Passport)

Mỗi sản phẩm được cấp UUID duy nhất. Hộ chiếu lưu timeline: ordered, transferred, awarded, pruned, flowered. Dữ liệu trong `DigitalPassportLog` với metadata JSON.

**Model:** `DigitalPassportLog`, `OwnershipHistory`, `Specimen`

---

### Chức năng 3: Diễn đàn Cộng đồng (Forum)

Thảo luận chuyên sâu: Kỹ thuật Bonsai, Khoe cây, Hỏi đáp bệnh lý. Hỗ trợ bài viết đa phương tiện, bình luận, Upvote/Like, phân loại danh mục. Real-time updates qua WebSocket.

**Trang:** `Forum/Index.vue`, `Forum/Show.vue`, `Forum/Create.vue`

---

### Chức năng 4: Chat thời gian thực (Real-time Messaging)

Nhắn tin trực tiếp Buyer ↔ Seller qua WebSocket (JakartaEE WildFly). Tin nhắn serialize qua Protobuf. Mỗi đơn hàng có chat thread riêng. Panel chat toàn cục.

**Công nghệ:** JakartaEE WebSocket, Protocol Buffers, Redis Pub/Sub

**Files:** `Services/ChatService.php`, `Composables/useChatSocket.js`, `Components/ChatBox.vue`, `Components/ChatPanel.vue`

---

### Chức năng 5: Thư viện Tri thức (Journal)

Kho tài liệu 20+ bài viết về 8 loài bonsai phổ biến và 11 bài hướng dẫn cơ bản. Phân loại theo chủ đề, crawl từ nguồn uy tín.

**Trang:** `Journal/Index.vue`, `Journal/Show.vue`

---

### Chức năng 6: Đặt hàng & Vận chuyển tự động

Cart → Checkout flow với selector địa chỉ Tỉnh → Huyện → Xã (từ DB `ghn_provinces`, `ghn_districts`, `ghn_wards`). Tự động tính phí GHTK real-time. Đơn hàng với timeline trạng thái (pending → confirmed → shipping → delivered → completed).

**Trang:** `Cart/Index.vue`, `Checkout/Index.vue`, `Checkout/Success.vue`, `Orders/Index.vue`, `Orders/Show.vue`

---

### Chức năng 7: Multi-vendor Marketplace

Seller đăng ký cửa hàng, quản lý sản phẩm, đơn hàng, đánh giá. Dashboard riêng. Admin quản lý toàn hệ thống qua panel quản trị.

**Trang Seller:** `Seller/Dashboard.vue`, `Seller/Products/Form.vue`, `Seller/Orders/Index.vue`, `Seller/Store/Edit.vue`, `Seller/Register.vue`

---

### Chức năng 8: Vườn cá nhân (My Garden)

Tạo vườn ảo, chia Zone, gắn cây, ghi chú và theo dõi sự phát triển theo thời gian.

**Model:** `Garden`, `GardenPlant`, `GardenZone`

**Trang:** `Garden/Index.vue`

---

### Chức năng 9: Tìm mẫu cây phù hợp (Quiz)

Quiz nhiều bước tìm loại cây phù hợp với điều kiện sống. Kết quả gợi ý sản phẩm từ database.

**Model:** `QuizQuestion`

**Trang:** `Quiz/Index.vue`

---

### Chức năng 10: Chẩn đoán bệnh cây (Plant Diagnostic)

Tải ảnh cây → AI (Gemini Pro Vision) phân tích → chẩn đoán bệnh → gợi ý chăm sóc.

**Model:** `PlantDiagnosis`, `DiagnosticSymptom`

**Service:** `AIPlantDoctorService`

**Trang:** `Diagnostic/Index.vue`

---

### Chức năng 11: So sánh sản phẩm (Compare)

Bảng so sánh side-by-side: thông số kỹ thuật, giá, đánh giá. Lưu qua localStorage.

**Trang:** `Compare/Index.vue`, `Components/CompareBar.vue`

---

### Chức năng 12: Affiliate (Tiếp thị liên kết)

Đăng ký làm affiliate, referral link, dashboard theo dõi hoa hồng, referral, conversion rate.

**Model:** `Affiliate`, `Referral`, `Commission`

**Trang:** `Affiliate/Register.vue`, `Affiliate/Dashboard.vue`

---

### Chức năng 13: Thông báo (Notifications)

Real-time notifications về trạng thái đơn hàng, bình luận, tin nhắn chat. Push notification qua `DeviceToken`.

**Trang:** `Notifications/Index.vue`

---

### Chức năng 14: Xác thực hai yếu tố (2FA)

TOTP (Google Authenticator), Recovery codes. Quản lý qua Settings/Security.

**Service:** `TwoFactorService`

**Trang:** `Settings/TwoFactor.vue`

---

### Chức năng 15: Theo dõi vận chuyển (Tracking)

Theo dõi trạng thái đơn hàng real-time, mã vận đơn, estimated delivery. Liên kết API GHTK.

**Trang:** `Tracking/Index.vue`

---

### Chức năng 16: Banner & SEO

Banner quảng cáo trang chủ. SEO Service tự động meta tags, JSON-LD, Open Graph.

**Model:** `Banner`, `AnalyticsEvent`

**Service:** `SeoService`, `AnalyticsService`

---

### Chức năng 17: Hệ thống Coupon & Voucher

Model `Coupon` và `UserVoucher`. Áp dụng mã giảm giá khi thanh toán.

### Chức năng 18: Đánh giá sản phẩm (Reviews)

Buyer đánh giá sau khi nhận hàng. Seller quản lý reviews.

**Trang:** `Seller/Reviews/Index.vue`

### Chức năng 19: Hợp đồng điện tử (E-Contract)

Tạo hợp đồng mua bán, ký số, theo dõi trạng thái (draft → signed → active → expired).

**Model:** `Contract`

**Trang:** `Contracts/Show.vue`

### Chức năng 20: Hoàn tiền (Refund)

Buyer yêu cầu hoàn tiền với lý do. Seller/Admin xử lý. Workflow: pending → approved/rejected → refunded.

**Model:** `Refund`

---

## 3.2. Chức năng phát triển tại Vòng 2 🔄

### 2.1. Trợ lý AI chuyên gia (AI Plant Doctor Nâng cao)

Nâng cấp chatbot AI trả lời câu hỏi chăm sóc cây theo ngữ cảnh. Phân tích chuỗi ảnh theo thời gian. Tự động routing tới Chuyên gia thực vật khi AI không chắc chắn.

**Công nghệ:** Gemini Pro Vision + RAG, Vector Database

### 2.2. Sàn đấu giá trực tuyến (Bonsai Auction)

Module Đấu giá English Auction cho siêu phẩm bonsai. Real-time qua WebSocket. Extensions tự động khi có người đặt giá cuối.

**Công nghệ:** JakartaEE WebSocket, Redis sorted sets, Protobuf

### 2.3. Mô phỏng Tạo tác 3D (Virtual Pruning Simulator)

Cắt tỉa thử nghiệm trên mô hình 3D trước khi thực hiện ngoài đời thực. Three.js mesh manipulation, real-time rendering.

**Công nghệ:** Three.js, WebGL, mesh manipulation

### 2.4. Thanh toán trực tuyến (Payment Gateway)

Tích hợp VNPay, MOMO, ZaloPay. Cơ chế giữ tiền (Escrow) cho giao dịch an toàn.

**Công nghệ:** Payment Gateway API, Escrow logic, `PaymentService`, `Transaction` model

### 2.5. Ứng dụng di động (Mobile App)

Flutter native với camera LiDAR (iPhone Pro), push notification real-time, trải nghiệm AR.

**Công nghệ:** Flutter, ARKit/ARCore, LiDAR, WebSocket

### 2.6. NFT Hộ chiếu số (Blockchain Passport)

Chuyển đổi Hộ chiếu số sang NFT trên blockchain. ERC-721, Smart Contracts, IPFS.

**Công nghệ:** Ethereum/Polygon, ERC-721, Smart Contracts, IPFS

---

# 4. Công nghệ áp dụng

## 4.1. Công nghệ phần cứng

- Máy ảnh DSLR/Điện thoại thông minh (Photogrammetry 3D)
- Máy chủ triển khai: VPS Linux (Ubuntu) + Docker Engine
- Camera LiDAR (iPhone Pro/iPad Pro — Mobile App tương lai)

## 4.2. Công nghệ phần mềm

| Thành phần | Công nghệ | Phiên bản |
|---|---|---|
| Core Backend | Laravel (PHP) | 12.x (PHP 8.2+) |
| ORM | Eloquent | Laravel built-in |
| Real-time Engine | JakartaEE WebSocket | WildFly 40.0 (Java 21) |
| Data Protocol | Google Protocol Buffers | Latest |
| Frontend Framework | Vue.js | 3.x (Composition API) |
| SPA Bridge | Inertia.js | Latest |
| CSS Framework | Tailwind CSS | 4.x |
| UI Components | PrimeVue | Latest |
| JS Routes | Ziggy.js | Latest |
| Đồ họa 3D | Three.js + TresJS | Latest |
| 3D Viewer | Google Model Viewer | `<model-viewer>` |
| Compression | Google Draco | 3D mesh compression |
| Queue Worker | Laravel Queue | Redis driver |
| Task Scheduler | Laravel Scheduler | Cron-based |

## 4.3. Công nghệ tích hợp & Hệ hạ tầng

| Thành phần | Chi tiết |
|---|---|
| Database | MySQL 8.x |
| Cache & Session | Redis 7.x (cache, session, queue, pub/sub) |
| Vận chuyển | API GHTK (Giao Hàng Tiết Kiệm) — tính phí real-time |
| AI Engine | Gemini Pro Vision — chẩn đoán bệnh lý thực vật |
| Container | Docker + Docker Compose |
| Web Server | Nginx (reverse proxy) |
| WebSocket | JakartaEE WildFly 40.0 |
| CI/CD | GitHub Actions → VPS deploy |
| SSL | Cloudflare Flexible SSL |
| Domain | agriverse.slink.id.vn |

## 4.4. Ưu điểm công nghệ

**Hiệu năng & UX:** Vue 3 + Inertia.js SPA-like experience. Draco Compression cho 3D trên di động (3G/4G). PrimeVue UI components sẵn sàng.

**Khả năng mở rộng:** Modular Monolith — tách module Shop, Forum, 3D, Diagnostic... không ảnh hưởng toàn hệ thống. 60+ Models, 25+ Services, 20+ Controllers.

**Tương tác tức thì:** JakartaEE WebSocket cho chat. Laravel Events + Redis Broadcast cho forum/order updates. Protobuf serialize tin nhắn.

**Bảo mật:** 2FA (TOTP), CSRF protection, Rate limiting, Session-based auth, Authorization Policies.

**DevOps:** Docker Compose 7 containers (nginx, php, queue, scheduler, mysql, redis, wildfly). GitHub Actions CI/CD auto-deploy.

---

# 5. Kiến trúc hệ thống

## 5.1. Kiến trúc tổng quan

```
Orchestrix/
├── app/Modules/AgriVerse/
│   ├── Console/          # Artisan commands
│   ├── Database/         # Migrations + Seeders (63 tỉnh, 696 huyện, 10051 xã)
│   ├── Events/           # OrderCreated, etc.
│   ├── Exceptions/       # InsufficientStockException
│   ├── Http/Controllers/
│   │   ├── Shop/         # 20+ Buyer controllers
│   │   ├── Seller/       # Seller controllers
│   │   └── Admin/        # Admin controllers
│   ├── Jobs/             # Queue jobs
│   ├── Listeners/        # Event listeners
│   ├── Models/           # 60+ Eloquent models
│   ├── Notifications/    # Email templates
│   ├── Policies/         # Authorization
│   ├── Protobuf/         # Protocol Buffer definitions
│   ├── Providers/        # Service providers
│   ├── Routes/           # shop.php, admin.php, api.php
│   ├── Services/         # 25+ business services
│   └── Resources/js/     # Vue 3 frontend
│       ├── Layouts/      # MarketplaceLayout, AdminLayout
│       ├── Pages/        # 45+ page components
│       ├── Components/   # ChatBox, CompareBar, ARViewer...
│       └── Composables/  # useChat, useAuth, useCompare...
├── docker/               # Nginx, MySQL configs
├── docker-compose.yml    # 7 containers
├── Dockerfile
└── .github/workflows/deploy.yml
```

## 5.2. Các luồng dữ liệu

### Luồng 1: Truy cập sản phẩm
```
Browser → Nginx → Laravel (Inertia) → Eloquent → MySQL
                                      → Vue 3 render
                                      → 3D Asset → Model Viewer (WebGL)
```

### Luồng 2: Chat real-time
```
Buyer/Seller → WebSocket (JakartaEE/WildFly)
            → Protobuf serialize → Redis Pub/Sub
            → WebSocket broadcast → Receiver
```

### Luồng 3: Đặt hàng & Vận chuyển
```
Checkout Form → Laravel Controller → Validate → Create Order (DB transaction)
             → GHTK API → Calculate shipping fee
             → OrderCreated Event → Listener (notifications)
             → Redirect to Success page
```

### Luồng 4: Chẩn đoán bệnh cây (AI)
```
Upload ảnh → AIPlantDoctorService → Gemini Pro Vision API
           → Phân tích → Trả diagnosis + suggestions
           → Lưu PlantDiagnosis (DB) → Hiển thị kết quả
```

### Luồng 5: Upload mô hình 3D
```
Seller upload .glb/.gltf → File storage
                        → Product.model_3d_url = path
                        → Buyer load via <model-viewer>
                        → WebGL render + Draco decompression
```

## 5.3. Container Architecture (Docker)

```yaml
docker-compose.yml:
├── nginx        # Reverse proxy (Cloudflare SSL)
├── php          # Laravel application (FPM)
├── queue        # Laravel queue worker (Redis driver)
├── scheduler    # Laravel task scheduler (cron)
├── mysql        # MySQL 8.x database
├── redis        # Redis 7.x
└── wildfly      # JakartaEE WebSocket (WildFly 40.0, Java 21)
```

**Lưu ý triển khai:**
- Nginx dùng DNS resolver (`resolver 127.0.0.11`) cho runtime container resolution
- PHP mount `sys_temp_dir` để tránh timeout build npm
- WildFly cần `USER jboss` sau build để fix permission deployments
- Cloudflare Flexible SSL — origin listen port 80

## 5.4. Bảo mật và hiệu suất

**Bảo mật:**
- CSRF protection (trừ Forum, CKFinder)
- Session-based authentication + encrypted cookies
- Authorization Policies (OrderPolicy, ProductPolicy...)
- Rate limiting cho API endpoints
- 2FA (TOTP) cho tài khoản敏感
- Input validation ở frontend (Form Request) và backend

**Hiệu suất:**
- Redis cache cho sản phẩm, danh mục, session
- Eager loading (`.with()`) chống N+1 query
- ImageOptimizationService resize/compress ảnh upload
- Vite code splitting cho frontend bundles
- DB transaction cho order placement

---

# 6. Phương hướng phát triển

## 6.1. Giai đoạn 1 (Vòng 1 — Hiện tại) ✅

- ✅ Core TMĐT: Đăng sản phẩm, 3D viewer, Hộ chiếu số
- ✅ Vận chuyển GHTK với mã tỉnh → huyện → xã
- ✅ Diễn đàn (Forum) với categories, upvote, comments
- ✅ Chat real-time WebSocket (JakartaEE)
- ✅ Checkout flow với địa chỉ Tỉnh→Huyện→Xã
- ✅ Multi-vendor Marketplace
- ✅ AI Plant Doctor (Gemini Pro Vision)
- ✅ Vườn cá nhân (My Garden)
- ✅ Quiz tìm mẫu cây
- ✅ So sánh sản phẩm (Compare)
- ✅ Affiliate system
- ✅ Xác thực 2FA
- ✅ Notifications real-time
- ✅ Docker deployment + CI/CD (GitHub Actions)
- ✅ Journal/Knowledge base (20+ bài viết)
- ✅ Truy xuất nguồn gốc (Tracking)
- ✅ Banner & SEO
- ✅ Hợp đồng điện tử (E-Contract)
- ✅ Hoàn tiền (Refund)
- ✅ Đánh giá sản phẩm (Reviews)
- ✅ Coupon & Voucher

## 6.2. Giai đoạn 2 (Vòng 2) 🔄

- 🔄 Payment Gateway online (VNPay, MOMO) + Escrow
- 🔄 Nâng cấp AI Plant Doctor với chatbot AI
- 🔄 Dashboard Analytics cho Admin/Seller
- 🔄 Push Notification hệ thống
- 🔄 Sàn đấu giá trực tuyến (Bonsai Auction)
- 🔄 Mô phỏng cắt tỉa 3D (Virtual Pruning Simulator)
- 🔄 Nâng cấp Hộ chiếu số với nhiều sự kiện hơn

## 6.3. Giai đoạn 3 (Tương lai)

- 📋 Mobile App (Flutter) — camera LiDAR cho quét 3D
- 📋 NFT Hộ chiếu số (ERC-721 trên Ethereum/Polygon)
- 📋 Multi-language support (i18n)
- 📋 Warehouse/Fulfillment center
- 📋 IoT sensor theo dõi môi trường cho My Garden

---

# 7. Công cụ hỗ trợ

| Loại | Công cụ | Mục đích |
|---|---|---|
| Version Control | Git + GitHub | Quản lý mã nguồn, code review |
| Project Management | Trello | Quản lý tiến độ, sprint planning |
| Development IDE | PHP Storm, VS Code | Phát triển backend & frontend |
| Design UI/UX | Figma, Stitch | Thiết kế giao diện, prototype |
| Testing — API | Postman | Test REST API |
| Testing — E2E | Playwright | Kiểm thử end-to-end |
| Testing — Unit | Pest PHP | Unit test cho Laravel |
| Container | Docker + Docker Compose | Triển khai đồng nhất |
| CI/CD | GitHub Actions | Tự động deploy khi push |
| Database | MySQL 8.x | Quản lý database |
| Cache | Redis 7.x | Cache, session, pub/sub |
| 3D Modeling | Blender + Photogrammetry | Tạo mô hình 3D |
| AI | Google AI Studio (Gemini) | API chẩn đoán bệnh cây |

---

# 8. Mô tả giao diện & Demo

## 8.1. Mô tả giao diện

**Triết lý:** "Botanical Heritage" — tối giản, sang trọng, cảm hứng bonsai Nhật Bản.

**Bảng màu:**
- Sage Green (#486730) — màu chính
- Terracotta (#8b4f27) — màu phụ
- Sand (#f4f1ea) — nền nhẹ
- White (#FCF9F8) — nền chính

**Typography:** Roboto cho body, font display cho tiêu đề.

**Key pages:**
- **Trang chủ:** Hero ảnh bonsai full-width, AQI real-time, grid sản phẩm, stores, journal
- **Chi tiết SP:** 3D viewer沉浸式, thông số, đánh giá, related products, chat
- **Checkout:** Địa chỉ Tỉnh→Huyện→Xã, phí GHTK, xác nhận đơn
- **Diễn đàn:** Categories, bài viết, bình luận, upvote
- **Seller Dashboard:** Thống kê, quản lý đơn, sản phẩm, reviews

## 8.2. Demo sản phẩm

- **GitHub:** https://github.com/minhdev-ops/Orchestrix
- **Demo online:** agriverse.slink.id.vn

---

# 9. Danh sách tính năng đầy đủ

| Module | Mô tả | Trạng thái |
|--------|--------|-----------|
| Shop (Products) | Đăng/bán SP, variants, ảnh, 3D | ✅ |
| Shop (Stores) | Cửa hàng, verification | ✅ |
| Shop (Cart/Checkout) | Giỏ hàng, Checkout, GHTK | ✅ |
| Shop (Orders) | Quản lý đơn, timeline | ✅ |
| Shop (Chat) | Chat real-time WebSocket | ✅ |
| Shop (Forum) | Diễn đàn cộng đồng | ✅ |
| Shop (Journal) | Thư viện bài viết | ✅ |
| Shop (Garden) | Vườn cá nhân | ✅ |
| Shop (Quiz) | Tìm mẫu cây | ✅ |
| Shop (Diagnostic) | Chẩn đoán bệnh (AI) | ✅ |
| Shop (Sustainability) | Báo cáo bền vững | ✅ |
| Shop (AR) | Trình xem AR | ✅ |
| Shop (Compare) | So sánh SP | ✅ |
| Shop (Contract) | Hợp đồng điện tử | ✅ |
| Shop (Affiliate) | Tiếp thị liên kết | ✅ |
| Shop (Tracking) | Theo dõi vận chuyển | ✅ |
| Seller | Dashboard, quản lý đơn/sp | ✅ |
| Admin | Quản trị hệ thống | ✅ |
| Auth | Đăng nhập, 2FA, quên MK | ✅ |
| Payment (Online) | Cổng thanh toán online | 🔄 |
| Auction | Đấu giá real-time | ❌ |
| Virtual Pruning | Mô phỏng cắt tỉa 3D | ❌ |
| NFT Passport | Blockchain NFT | ❌ |
| Mobile App | Flutter | ❌ |

---

# 10. Tài liệu tham khảo

1. Laravel 12.x: https://laravel.com/docs/12.x
2. Vue.js 3 + Inertia.js: https://vuejs.org, https://inertiajs.com
3. Google Model Viewer: https://modelviewer.dev
4. Three.js: https://threejs.org
5. Laravel Reverb: https://reverb.laravel.com
6. JakartaEE WebSocket: https://jakarta.ee/specifications/websocket/
7. Protocol Buffers: https://protobuf.dev
8. GHTK API: https://docs.giaohangtietkiem.vn
9. PrimeVue: https://primevue.org
10. Ziggy.js: https://ziggy.dev
11. Gemini API: https://ai.google.dev
12. Tài liệu chuyên ngành chăm sóc, định hình bonsai

---

# 11. Lời cảm ơn

Em xin gửi lời cảm ơn đến quý Thầy Cô trong khoa Công nghệ Thông tin, trường Đại học Nông Lâm TP. Hồ Chí Minh đã truyền đạt cho em những nền tảng kiến thức vững chắc trong suốt những năm học vừa qua.

Cuối cùng, em xin gửi lời tri ân đến gia đình và bạn bè đã luôn động viên, hỗ trợ và tạo mọi điều kiện tốt nhất để em hoàn thành tốt đề tài này. Mặc dù đã rất cố gắng, nhưng do hạn chế về mặt thời gian và kinh nghiệm thực tế, đề tài chắc chắn không tránh khỏi những thiếu sót. Em rất mong nhận được sự góp ý và chỉ bảo thêm từ hội đồng đánh giá để hệ thống có thể hoàn thiện hơn trong tương lai.

Xin trân trọng cảm ơn!

TP. Hồ Chí Minh, ngày     tháng     năm 2026
Tác giả hoặc đại diện nhóm tác giả
(Ký, ghi rõ họ tên)
