# Orchestrix Bonsai — Kế hoạch phát triển

## Tổng quan

- **Nền tảng:** Laravel 12 + Vue 3 (Inertia SPA) + PrimeVue 4 + Tailwind CSS 4
- **Tích hợp sẵn:** GHN (Giao Hàng Nhanh), MoMo Payment, Spatie RBAC, Digital Passport
- **Module:** AgriVerse (app/Modules/AgriVerse)

---

## Phase 1: User Features (Đặt hàng & Vận chuyển)

| # | Task | Mô tả | Đầu ra |
|---|------|-------|--------|
| 1.1 | Checkout: chọn dịch vụ GHN | Thêm bước chọn dịch vụ vận chuyển (GHN Express, Standard, Same Day) + hiển thị phí ship | Checkout/Index.vue + CheckoutController |
| 1.2 | Checkout: danh sách dịch vụ theo quận/huyện | Gọi GHNService.getServices() -> radio group | GHNService + giao diện |
| 1.3 | Order Detail: thông tin vận chuyển | Hiển thị phương thức, phí, mã vận đơn, link tracking | Orders/Show.vue + OrderController |
| 1.4 | Trang theo dõi đơn hàng | Real-time tracking từ GHN | Tracking/Index.vue + GHN trackOrder |
| 1.5 | Wishlist: hoàn thiện UI | Toggle trái tim fill/unfill, counter | Products/Index.vue + Wishlist/Index.vue |
| 1.6 | User Address: thêm trường | Thêm `is_shop_default` cho địa chỉ trả hàng | Migration + AddressController |

---

## Phase 2: Admin Dashboard & Module mới

| # | Task | Mô tả | Đầu ra |
|---|------|-------|--------|
| 2.1 | Dashboard nâng cao | Doanh thu 7 ngày, đơn mới, user mới, top sản phẩm, biểu đồ | Admin/Dashboard.vue + DashboardController |
| 2.2 | Quản lý User | Filter, search, block/unlock, lịch sử giao dịch | Admin/Users/ + UserController |
| 2.3 | Quản lý Banner | CRUD, drag-drop sort, active/inactive, date range | Admin/Banners/ + BannerController |
| 2.4 | Quản lý Diễn đàn | Duyệt bài, xóa, ghim, chuyển chuyên mục | Admin/Forum/ + ForumController |
| 2.5 | Quản lý Bài viết chăm sóc | CRUD bài viết, categories, featured image | Admin/CareGuides/ + CareGuideController |
| 2.6 | Quản lý File người dùng | List, preview, xóa file | Admin/Files/ + FileManagerController |
| 2.7 | Quản lý Doanh thu | Lọc ngày/tháng/năm, biểu đồ, export | Admin/Revenue/ + ReportController |

---

## Phase 3: Seller Registration & Verification

| # | Task | Mô tả | Đầu ra |
|---|------|-------|--------|
| 3.1 | Migration: seller_verifications | Ảnh chân dung, email, SĐT, CMND/CCCD, status | Migration + SellerVerification model |
| 3.2 | User Model: trường mới | `seller_verified_at`, `seller_type`, `seller_level` | Migration + User model |
| 3.3 | Gói dịch vụ phân loại | SubscriptionPlans (Free/Cơ bản/Chuyên nghiệp) -> phân loại người bán | Seeder + logic |
| 3.4 | Form đăng ký bán | Upload ảnh chân dung, nhập CMND/CCCD, email, SĐT | SellerRegister.vue |
| 3.5 | Admin duyệt người bán | Xem hồ sơ, duyệt/từ chối | Admin/Sellers/ + VerificationController |
| 3.6 | Seller dashboard: trạng thái | Hiển thị xác thực, gói dịch vụ | Profile/Index.vue |

---

## Phase 4: Product Approval Workflow

| # | Task | Mô tả | Đầu ra |
|---|------|-------|--------|
| 4.1 | Product: thêm status | `pending_review`, `rejected`, `approved` + `reject_reason` | Migration + Product model |
| 4.2 | Form đăng sản phẩm (seller) | Chọn danh mục, upload ảnh, giá, số lượng, tags | Seller/Products/Form.vue |
| 4.3 | Admin duyệt sản phẩm | Filter pending, duyệt/từ chối + lý do | Admin/Products/ + ProductController |
| 4.4 | Auto-create Digital Passport | Khi duyệt -> tạo passport cho sản phẩm | DigitalPassportController |
| 4.5 | Cấp quyền quản lý vận đơn | Seller được quyền tạo vận đơn GHN | StoreSubscription + permissions |

---

## Phase 5: Seller Store & Order Management

| # | Task | Mô tả | Đầu ra |
|---|------|-------|--------|
| 5.1 | Trang cá nhân seller | DS sản phẩm đang bán, đánh giá, thông tin shop | Profile/Index.vue (dynamic) |
| 5.2 | Seller quản lý đơn hàng | List đơn nhận, cập nhật trạng thái (xác nhận->đóng gói->giao GHN) | Seller/Orders/ + OrderController |
| 5.3 | Seller tạo vận đơn GHN | Nhập cân nặng, kích thước, chọn dịch vụ -> mã vận đơn | Seller/Orders/Shipping.vue + GHNService |
| 5.4 | Digital Passport log | Ghi log mỗi lần cập nhật trạng thái | DigitalPassportLog auto |
| 5.5 | Seller dashboard | Doanh thu, đơn xử lý, sản phẩm hết hàng | Seller/Dashboard.vue |

---

## Phase 6: Buyer Flow & Hoàn thiện

| # | Task | Mô tả | Đầu ra |
|---|------|-------|--------|
| 6.1 | Buyer chờ xác nhận | Notification + email khi seller xác nhận đơn | Event/Listener |
| 6.2 | Buyer tracking real-time | Chờ xác nhận -> đóng gói -> GHN -> vận chuyển -> đã nhận | Tracking/Index.vue |
| 6.3 | Xác nhận đã nhận hàng | Buyer confirm -> giải phóng tiền cho seller | Transaction release |
| 6.4 | Digital Passport công khai | Xem vòng đời sản phẩm trên trang chi tiết | Product/Show.vue |
| 6.5 | Chat buyer-seller | Nhóm chat theo từng đơn hàng | ChatBox + Message model |

---

## Phase 7: Testing & Hoàn thiện

| # | Task | Mô tả |
|---|------|-------|
| 7.1 | Test full flow | Register seller -> verify -> create product -> admin approve -> buyer order -> seller confirm -> GHN ship -> buyer receive |
| 7.2 | Test GHN shipping | Chọn dịch vụ + tính phí + tạo vận đơn + tracking |
| 7.3 | Test address | CRUD + set default |
| 7.4 | Test wishlist | Toggle + display |
| 7.5 | Test admin | Tất cả CRUD modules |
| 7.6 | Test seller | Dashboard + store page |
