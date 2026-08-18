# AgriVerse Hub — Feature Test Scripts

> **Mục đích:** Hướng dẫn kiểm thử tất cả chức năng của AgriVerse Marketplace theo từng vai trò (Admin / Seller / Buyer).
> Dữ liệu test demo: `GET /agriverse/demo-seed` (chỉ với môi trường không phải production)

**Demo Accounts:**

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@orchestrix.com | 12345678 |
| Seller | seller@orchestrix.com | 12345678 |
| Buyer | buyer@orchestrix.com | 12345678 |

---

## Table of Contents

1. [Guest / Buyer — Người Mua](#1-guest--buyer)
2. [Seller — Người Bán](#2-seller)
3. [Admin — Quản Trị Viên](#3-admin)
4. [API Endpoint Tests](#4-api)
5. [Compare — So Sánh Sản Phẩm](#139-compare--so-sánh-sản-phẩm)
6. [Cross-Cutting Concerns](#5-cross-cutting)

---

## 1. Guest / Buyer

### 1.1 Homepage

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse` → Kiểm tra Hero section hiển thị (header glass, banner "Bonsai Việt", stats).
2. Scroll xuống → Kiểm tra **AQI Widget** hiển thị chỉ số chất lượng không khí (dùng geolocation browser).
3. Kiểm tra **Category Chips** (hàng ngang) — click vào một chip → chuyển đến `/agriverse/san-pham?category={slug}`.
4. Kiểm tra **Product Grid** — card sản phẩm hiển thị ảnh, tên, giá, nút "3D".
5. Click "3D" trên card → mở `xem-3d/{product}` (AR viewer).
6. Kiểm tra **Footer** — links, social icons, copyright.
7. **Mobile:** Thu nhỏ trình duyệt → kiểm tra hamburger menu, AQI vẫn hiển thị, grid chuyển 1 cột.

### 1.2 Product Listing

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/san-pham` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/san-pham` → Kiểm tra grid sản phẩm hiển thị đúng số lượng.
2. **Search:** Nhập từ khóa → danh sách lọc theo tên sản phẩm.
3. **Category filter:** Click filter danh mục → chỉ hiển thị sản phẩm thuộc danh mục đó.
4. **Price range:** Kéo thanh giá → danh sách lọc theo khoảng giá.
5. **Stock toggle:** Bật "Còn hàng" → chỉ hiển thị sản phẩm còn stock.
6. **Sort:** Chọn "Giá: Thấp đến cao" → sắp xếp đúng.
7. **Pagination:** Click trang 2 → chuyển trang.
8. **Empty state:** Tìm kiếm từ khóa không tồn tại → hiển thị thông báo "Không tìm thấy sản phẩm".

### 1.3 Product Detail

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/san-pham/{product}` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/san-pham/15` → Kiểm tra:
   - 3D model viewer (`<model-viewer>`) — xoay, zoom được.
   - Tên sản phẩm, giá, stock badge.
   - Variant selector (nếu có).
   - Quantity picker.
2. Click **"Thêm vào giỏ hàng"** (chưa đăng nhập) → chuyển đến trang đăng nhập hoặc thêm thành công vào guest cart.
3. Click **trái tim (wishlist)** → nếu chưa đăng nhập → redirect login. Nếu đã login → toggle wishlist.
4. Sau khi thêm yêu thích (tim đỏ), click icon **trái tim** trên header → vào `/agriverse/yeu-thich` → kiểm tra sản phẩm vừa yêu thích hiển thị trong grid.
5. Scroll xuống → **Mô tả sản phẩm** hiển thị đúng.
5. **Store info card** — hiển thị tên shop, avatar, nút "Xem shop".
6. **Care instructions** — hiển thị hướng dẫn chăm sóc.
7. **Sustainability badge** — hiển thị chứng nhận.
8. **Reviews** — danh sách đánh giá (nếu có), sorting, xem thêm.
9. **Related products** — các sản phẩm liên quan, click được.
10. **AR mode:** Click "Xem AR" → mở AR viewer với camera (WebXR).
11. **So sánh giá:** Kiểm tra "compare price" (giá cũ) hiển thị và gạch ngang.

### 1.4 Store Listing

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/cua-hang` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/cua-hang` → Kiểm tra grid các cửa hàng (avatar, tên shop, số sản phẩm, badge active).
2. Click vào một cửa hàng → vào `/agriverse/cua-hang/{store}`.

### 1.5 Store Detail

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/cua-hang/{store}` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Kiểm tra header shop (logo, verified badge, seller type badge).
2. Kiểm tra product grid — chỉ hiển thị sản phẩm của shop đó.
3. Filter / search trong shop hoạt động.

### 1.6 Categories

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/danh-muc` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/danh-muc` → Kiểm tra danh sách danh mục với icon, số lượng sản phẩm.
2. Click vào một danh mục → chuyển đến `/agriverse/san-pham?category={slug}`.
3. Kiểm tra subcategories (nếu có).

### 1.7 Forum — Xem

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/dien-dan` |
| **Preconditions** | Không cần đăng nhập để xem |

**Test Script:**
1. Mở `/agriverse/dien-dan` → Kiểm tra danh sách bài viết (title, excerpt, author avatar, comment count, date).
2. **Search** — nhập từ khóa → lọc bài viết.
3. **Category filter chips** — click chip → lọc theo chuyên mục.
4. **Sort** — sắp xếp bài viết.
5. Click bài viết → vào `/agriverse/dien-dan/{post}`.
6. **Create post button** — nếu chưa đăng nhập → redirect login.
7. Kiểm tra chi tiết bài viết (nội dung, comments, likes).

### 1.8 Forum — Đăng nhập để tạo bài

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/dien-dan/tao-bai-viet` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở form tạo bài viết → nhập tiêu đề, nội dung, chọn danh mục.
2. Upload hình ảnh (nếu có) → kiểm tra upload API hoạt động.
3. Submit → bài viết hiển thị trong danh sách forum.
4. Sửa bài viết → kiểm tra.
5. Xóa bài viết → kiểm tra.
6. Comment vào bài viết → comment hiển thị.
7. Like bài viết → tim đổi màu.

### 1.9 Quiz — Tìm Mẫu Vật

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/cay-tim-nguoi` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/cay-tim-nguoi` → Kiểm tra multi-step quiz.
2. Trả lời từng bước (choice cards, grid, image, rows) → click "Tiếp theo".
3. Bước cuối → click "Phân tích" → loading animation hiển thị.
4. Kết quả hiển thị 3 mẫu cây gợi ý → click vào sản phẩm → chuyển đến product detail.

### 1.10 Diagnostic — Chẩn Đoán Cây

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/chan-doan` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/chan-doan` → Upload ảnh cây bị bệnh.
2. Chọn triệu chứng từ checklist.
3. Click "Chẩn đoán" → AI analysis loading.
4. Kết quả hiển thị: tên bệnh, mức độ nghiêm trọng, care recommendations.
5. Lưu lịch sử (nếu đã login).

### 1.11 Journal — Bài Viết & Kiến Thức

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/bai-viet` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/bai-viet` → Kiểm tra grid articles (hero image, title, abstract, author, date, tags).
2. **Search** — tìm kiếm bài viết.
3. **Tag filter** — click tag → lọc.
4. **Peer-reviewed badge** — kiểm tra hiển thị.
5. Click bài viết → vào `/agriverse/bai-viet/{article}`.

### 1.12 Sustainability — Phát Triển Bền Vững

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/phat-trien-ben-vung` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở trang → Kiểm tra carbon footprint metrics, eco-impact charts.
2. Click "Tải PDF" → tải báo cáo PDF.
3. Click "Methodology" → modal hiển thị phương pháp tính toán.

### 1.13 Air Quality

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/khong-khi` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/khong-khi` → Kiểm tra chỉ số AQI theo khu vực.
2. Cho phép geolocation → dữ liệu cập nhật theo vị trí.

### 1.14 AR Viewer

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/xem-3d/{product}` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/xem-3d/{product}` → `model-viewer` hiển thị.
2. **Xoay:** Kéo chuột → model xoay 360°.
3. **Zoom:** Scroll → phóng to/thu nhỏ.
4. **Auto-rotate:** Model tự động xoay.
5. **Fullscreen:** Click fullscreen → model toàn màn hình.
6. **Reset camera:** Click reset → model về vị trí ban đầu.
7. **AR mode (mobile):** Click "AR" → kích hoạt camera WebXR.

### 1.15 Support — Hỗ Trợ

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/ho-tro` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/ho-tro` → Kiểm tra knowledge base search.
2. **FAQ accordion** — click câu hỏi → mở rộng câu trả lời.
3. **Specialist consultation booking** — form đặt lịch.
4. **Warranty claims** — form yêu cầu bảo hành.
5. **Contact options** — phone, email, chat.

### 1.16 404

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/404` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở `/agriverse/404` → Kiểm tra hero "Lối đi trong vườn sương mù".
2. 3 suggestion cards hiển thị — click được.
3. Quote John Muir hiển thị.

### 1.17 Cart (Guest + Auth)

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/gio-hang` |
| **Preconditions** | Có sản phẩm trong giỏ |

**Test Script:**
1. **Guest:** Thêm sản phẩm vào giỏ từ product detail → kiểm tra giỏ hàng.
2. **API:** `POST /agriverse/api/cart/add` — thêm thành công.
3. Mở `/agriverse/gio-hang`:
   - Danh sách item hiển thị (ảnh, tên, variant, số lượng, giá).
   - Quantity controls (tăng/giảm) → API `PUT /agriverse/api/cart/{cart}/update`.
   - Remove button → API `DELETE /agriverse/api/cart/{cart}/remove`.
4. **Checkout CTA:** Click "Thanh toán" → nếu chưa login → redirect login.
5. **Empty state:** Xóa hết item → hiển thị "Giỏ hàng trống".

### 1.18 Checkout

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/thanh-toan` |
| **Preconditions** | Đã đăng nhập, có sản phẩm trong giỏ |

**Test Script:**
1. Mở `/agriverse/thanh-toan` → Kiểm tra multi-step form:
   - **Step 1 — Shipping:** Form thông tin giao hàng (tên, SĐT, địa chỉ).
   - **Step 2 — Payment:** Chọn phương thức thanh toán.
   - **Step 3 — Review:** Xem lại đơn hàng.
2. **Progress bar** — hiển thị đúng bước hiện tại.
3. Xác nhận đơn hàng → `POST /agriverse/api/checkout/process`.
4. Chuyển đến `/agriverse/thanh-toan/thanh-cong/{order}`.

### 1.19 Checkout Success

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/thanh-toan/thanh-cong/{order}` |
| **Preconditions** | Vừa tạo đơn hàng thành công |

**Test Script:**
1. Kiểm tra order ID hiển thị.
2. Seller contact card (store, phone, email).
3. Hướng dẫn next-step (chờ xác nhận, chuyển khoản...).

### 1.20 Orders — Danh Sách

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/don-hang` |
| **Preconditions** | Đã đăng nhập, có ít nhất 1 đơn hàng |

**Test Script:**
1. Mở `/agriverse/don-hang` → Kiểm tra danh sách đơn hàng.
2. **Status badges:** pending (vàng), confirmed (xanh dương), shipped (tím), completed (xanh lá), cancelled (đỏ) — kiểm tra màu sắc.
3. Click đơn hàng → vào `/agriverse/don-hang/{order}`.

### 1.21 Orders — Chi Tiết

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/don-hang/{order}` |
| **Preconditions** | Đã đăng nhập, là chủ đơn hàng |

**Test Script:**
1. Kiểm tra status badge.
2. Product info (ảnh, tên, số lượng, giá).
3. Buyer/Seller info.
4. **Timeline tracker** — các mốc thời gian.
5. **Summary** — tổng tiền, phí ship, giảm giá.
6. **Hành động:**
   - Nếu status = pending → nút "Hủy đơn".
   - Nếu status = shipped → nút "Đã nhận hàng".
   - Nếu đã nhận → nút "Yêu cầu hoàn tiền".

### 1.22 Wishlist

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/yeu-thich` |
| **Preconditions** | Đã đăng nhập, có ít nhất 1 sản phẩm yêu thích |

**Test Script:**
1. Mở `/agriverse/yeu-thich` → Kiểm tra grid sản phẩm yêu thích.
2. Click **heart toggle** → bỏ yêu thích → sản phẩm biến mất.
3. **Add to cart** từ wishlist.
4. **Empty state** — nếu chưa có yêu thích.

### 1.23 Payment

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/thanh-toan/{order}` |
| **Preconditions** | Đã đăng nhập, đơn hàng tồn tại |

**Test Script:**
1. Mở trang → Kiểm tra danh sách phương thức thanh toán.
2. Chọn phương thức → redirect `payment.process`.
3. **Banking:** Mở `/agriverse/thanh-toan/{order}/chuyen-khoan` → form thông tin chuyển khoản.
4. Upload proof → `POST /agriverse/thanh-toan/{order}/upload-proof`.
5. **VNPay / MoMo callbacks** — kiểm tra IPN endpoints:
   - `GET /agriverse/thanh-toan/{order}/vnpay-callback`
   - `POST /agriverse/thanh-toan/{order}/vnpay-ipn`
   - `GET /agriverse/thanh-toan/{order}/momo-callback`
   - `POST /agriverse/thanh-toan/{order}/momo-ipn`

### 1.24 Contracts (Buyer)

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/hop-dong/{contract}` |
| **Preconditions** | Đã đăng nhập, có hợp đồng |

**Test Script:**
1. Mở trang → Kiểm tra thông tin hợp đồng (sản phẩm, bên mua, bên bán).
2. **Status:** "Đang hiệu lực" / "Chờ ký".
3. **Ký hợp đồng** (nếu chưa ký) → gửi signature.
4. **Tải PDF** hợp đồng.
5. Kiểm tra quyền — buyer chỉ xem được hợp đồng của mình.

### 1.25 Tracking — Theo Dõi Vận Chuyển

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/theo-doi-van-chuyen` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở trang → Nhập mã đơn hàng / mã vận đơn.
2. Click "Tra cứu" → hiển thị trạng thái vận chuyển.
3. **API lookup:** `POST /agriverse/api/tracking/lookup`.

### 1.26 Notifications

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/thong-bao` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở trang → Kiểm tra danh sách notification.
2. **Unread count badge** trên header / sidebar.
3. Click "Đánh dấu đã đọc" → từng cái.
4. Click "Đánh dấu tất cả đã đọc".
5. **Empty state** — nếu chưa có thông báo.

### 1.27 Profile

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/ho-so/{user?}` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở `/agriverse/ho-so` → Kiểm tra public profile:
   - Avatar, name, badges (verified, seller).
   - Bio, email, phone, member since.
   - Nếu là seller → hiển thị store section.
2. Xem profile người dùng khác (nếu có).

### 1.28 Settings — Account

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/tai-khoan/cai-dat` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở trang → Tabbed settings:
   - **Profile tab:** Sửa tên, email, SĐT → lưu → kiểm tra API `POST /agriverse/api/settings/profile`.
   - **Password tab:** Đổi mật khẩu → form validation (mật khẩu cũ, mới, xác nhận) → API `POST /agriverse/api/settings/password`.
   - **Notifications tab:** Toggle notification preferences → API `POST /agriverse/api/settings/notifications`.

### 1.29 2FA — Two-Factor Auth

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/cai-dat/2fa` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở trang → Kiểm tra trạng thái 2FA (đã bật/tắt).
2. **Setup:** Click "Thiết lập" → QR code hiển thị.
3. **Enable:** Quét QR, nhập OTP → bật 2FA.
4. **Recovery codes:** Hiển thị danh sách codes.
5. **Regenerate:** Click "Tạo lại" → codes mới.
6. **Disable:** Tắt 2FA → xác nhận.
7. **Verify login (2FA):** Đăng xuất, đăng nhập lại → nhập OTP từ authenticator app.

### 1.30 Address Management

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/dia-chi` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở `/agriverse/dia-chi` → Danh sách địa chỉ.
2. **Thêm mới:** `/agriverse/dia-chi/them-moi` → form (tên, SĐT, tỉnh/thành, quận/huyện, phường/xã, địa chỉ chi tiết).
3. **Sửa:** Click sửa → form edit.
4. **Xóa:** Click xóa → xác nhận.
5. **Set default:** Click "Mặc định" → API `POST /agriverse/api/addresses/{address}/set-default`.

### 1.31 Cart Add (Guest API)

| Field | Value |
|-------|-------|
| **URL** | `POST /agriverse/api/cart/add` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Gửi POST với `product_id`, `quantity`, `variant` (nếu có) → trả về 200.
2. Kiểm tra thêm quá stock → báo lỗi.
3. Kiểm tra sản phẩm không tồn tại → 404.

### 1.32 Wishlist Toggle (Guest)

| Field | Value |
|-------|-------|
| **URL** | `POST /agriverse/api/wishlist/{product}/toggle` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Gửi POST → nếu chưa login → trả về lỗi (vì wishlist cần user).
2. Nếu đã login → toggle (thêm/xóa).

### 1.33 Chat — Buyer ↔ Seller

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/api/orders/{order}/chat` |
| **Preconditions** | Đã đăng nhập, có đơn hàng |

**Test Script:**
1. Mở chat theo đơn hàng → hiển thị tin nhắn.
2. Gửi tin nhắn mới → `POST /agriverse/api/orders/{order}/chat`.
3. **Conversation list:** `GET /agriverse/api/chat/conversations`.
4. **Start conversation:** `POST /agriverse/api/chat/start` (với seller về sản phẩm).
5. **Group chat:**
   - `GET /agriverse/api/chat/groups` — danh sách nhóm.
   - `POST /agriverse/api/chat/groups` — tạo nhóm.
   - `POST /agriverse/api/chat/groups/{group}/join` — tham gia nhóm.
   - Gửi tin nhắn group, xem thành viên.

### 1.34 Affiliate — Dashboard

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/affiliate` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở trang → Kiểm tra commission summary (total, withdrawn, available).
2. **Commission history table** — danh sách hoa hồng.
3. **Register:** `/agriverse/affiliate/register` → form đăng ký, chọn payout method.
4. **Get link:** `/agriverse/affiliate/link` → link giới thiệu.
5. **Stats:** `/agriverse/affiliate/stats` → thống kê.

### 1.35 Garden — Khu Vườn

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/khu-vuon` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Mở `/agriverse/khu-vuon` → Kiểm tra hero stats (total plants, health score ring chart).
2. **Zone cards** — danh sách khu vực trong vườn.
3. **Care-needed alerts** — thông báo cây cần chăm sóc.
4. **Watering/Feeding schedule** — lịch tưới/bón phân.
5. **Tương tác với cây:**
   - `POST /agriverse/khu-vuon/plants/{plant}/water` — tưới cây.
   - `POST /agriverse/khu-vuon/plants/{plant}/fertilize` — bón phân.
   - `PUT /agriverse/khu-vuon/plants/{plant}/move` — di chuyển.
   - `PUT /agriverse/khu-vuon/plants/{plant}/stage` — cập nhật giai đoạn.
   - `DELETE /agriverse/khu-vuon/plants/{plant}` — xóa cây.

### 1.36 Buy Now

| Field | Value |
|-------|-------|
| **URL** | `POST /agriverse/api/cart/{product}/buy-now` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. Click "Mua ngay" trên product detail → API tạo order trực tiếp (bỏ qua giỏ hàng) → redirect checkout.

### 1.37 Order Actions (API)

| Field | Value |
|-------|-------|
| **URL** | Various under `/agriverse/api/orders/` |
| **Preconditions** | Đã đăng nhập, là chủ đơn hàng |

**Test Script:**
1. **Cancel order:** `POST /agriverse/api/orders/{order}/cancel` → status thành "cancelled".
2. **Confirm received:** `POST /agriverse/api/orders/{order}/confirm-received` → status thành "completed".
3. **Request refund:** `POST /agriverse/api/orders/{order}/refund` → tạo yêu cầu hoàn tiền.

### 1.38 GHTK Address & Shipping Fee

| Field | Value |
|-------|-------|
| **URL** | Various under `/agriverse/api/ghtk/` |
| **Preconditions** | Đã đăng nhập |

**Test Script:**
1. `GET /agriverse/api/ghtk/provinces` — danh sách tỉnh/thành.
2. `POST /agriverse/api/ghtk/districts` — quận/huyện theo tỉnh.
3. `POST /agriverse/api/ghtk/wards` — phường/xã theo quận.
4. `POST /agriverse/api/ghtk/shipping-fee` — tính phí ship.

### 1.39 Compare — So Sánh Sản Phẩm

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/so-sanh?ids=1,2` |
| **Preconditions** | Không cần đăng nhập |

**Test Script:**
1. Mở trang sản phẩm → click nút **So sánh** (icon `compare_arrows`) trên card hoặc chi tiết → kiểm tra bar xuất hiện dưới cùng.
2. Thêm sản phẩm thứ 2 → bar hiển thị "2 sản phẩm đang so sánh" + nút "So sánh ngay".
3. Click **"So sánh ngay"** → vào `/agriverse/so-sanh?ids=...` → kiểm tra bảng so sánh:
   - Tên, giá, giá cũ (gạch ngang), badge giảm giá.
   - Danh mục, mô tả, số lượng đã bán.
   - Thông số kỹ thuật (technical_specs) nếu có.
4. Click **"Thêm vào giỏ"** từ bảng so sánh.
5. Click **Xoá** (icon close) trên một sản phẩm → sản phẩm biến mất, cập nhật danh sách.
6. **Empty state** — nếu không có sản phẩm nào → thông báo "Chưa có sản phẩm để so sánh".
7. **Maximum 4 SP** — thêm >4 sản phẩm → sản phẩm cũ nhất bị thay thế.

---

### 1.36 Login / Register — Social Login (Facebook, Google)

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/dang-nhap`, `GET /agriverse/dang-ky` |
| **Preconditions** | Không cần đăng nhập |

#### 1.36.1 Cấu hình Facebook Developer Console

> **Lỗi thường gặp:** `"JSSDK option is not toggled"` — xảy ra khi chưa bật JS SDK trong Facebook Developer Console.

**Các bước cấu hình:**

1. Vào [https://developers.facebook.com](https://developers.facebook.com) → đăng nhập tài khoản Facebook.
2. Chọn **App** của AgriVerse (nếu chưa có → **Create App** → chọn **Consumer**).
3. Ở menu trái → **Facebook Login** → **Settings**.
4. Bật các tùy chọn sau:
   | Setting | Value |
   |---------|-------|
   | **Log in with JavaScript SDK** | ✅ **Yes** |
   | **Client OAuth Login** | ✅ Yes |
   | **Web OAuth Login** | ✅ Yes |
   | **Force Web OAuth Reauthentication** | ❌ No |
   | **Enforce HTTPS** | ✅ Yes |
   | **Embedded Browser OAuth Login** | ❌ No |
5. **Valid OAuth Redirect URIs** — thêm:
   ```
   https://agriverse.slink.id.vn/auth/facebook/callback
   ```
6. **App Mode** (trên cùng): chuyển từ **Development** → **Live** (nếu ứng dụng đã sẵn sàng cho production).
7. Kiểm tra **App ID** và **App Secret** tại **Settings → Basic**:
   - `App ID` = giá trị trong `.env` `FACEBOOK_APP_ID`
   - `App Secret` = giá trị trong `.env` `FACEBOOK_APP_SECRET`
8. **Domain** — mục **App Domains** (Settings → Basic) thêm: `agriverse.slink.id.vn`

**.env tương ứng:**
```env
FACEBOOK_APP_ID=4528392054097850
FACEBOOK_APP_SECRET=4adeca0b0e306619f4624a4ee383316b
VITE_FACEBOOK_APP_ID=4528392054097850
```

#### 1.36.2 Cấu hình Google Developer Console

1. Vào [https://console.cloud.google.com](https://console.cloud.google.com) → chọn project AgriVerse.
2. **APIs & Services** → **Credentials** → **OAuth 2.0 Client IDs**.
3. Thêm **Authorized JavaScript origins**:
   ```
   https://agriverse.slink.id.vn
   ```
4. Thêm **Authorized redirect URIs**:
   ```
   https://agriverse.slink.id.vn/auth/google/callback
   ```
5. Kiểm tra `.env` có đúng:
   ```env
   GOOGLE_CLIENT_ID=8404251349-...apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=GOCSPX-...
   VITE_GOOGLE_CLIENT_ID=8404251349-...apps.googleusercontent.com
   ```

#### 1.36.3 Test Script

1. Mở `/agriverse/dang-nhap` → Kiểm tra form có nút **"Đăng nhập bằng Google"** và **"Đăng nhập bằng Facebook"**.
2. Click **Facebook** → popup Facebook Login hiện ra → nhập email/password Facebook → cho phép → redirect về site, đăng nhập thành công.
3. Click **Google** → chọn tài khoản Google → redirect về site, đăng nhập thành công.
4. **Register:** Vào `/agriverse/dang-ky` → click **Google/Facebook** → đăng ký nhanh (không cần nhập password).
5. Sau khi đăng nhập bằng social → vào **Settings → Profile** → kiểm tra email, avatar được đồng bộ từ social account.
6. **Logout → Login lại bằng social** → không cần cấp quyền lại (nếu session còn).
7. **Lỗi:** Nếu Facebook báo lỗi → kiểm tra cấu hình ở Developer Console (mục 1.36.1).

---

## 2. Seller

### 2.1 Seller Registration

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/register` |
| **Preconditions** | Đã đăng nhập với role Buyer |

**Test Script:**
1. Mở `/agriverse/seller/register` → Multi-step form:
   - **Step 1 — Identity:** Upload CMND/CCCD, nhập thông tin cá nhân.
   - **Step 2 — Business:** Thông tin kinh doanh (tên shop, loại hình).
   - **Step 3 — Store Details:** Mô tả shop, logo, SĐT, địa chỉ.
2. Submit → chờ admin duyệt.
3. **Status page:** `/agriverse/seller/status` — kiểm tra trạng thái (pending / approved / rejected).
4. Nếu bị từ chối → retry flow (sửa thông tin, gửi lại).

### 2.2 Seller Dashboard

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/dashboard` |
| **Preconditions** | Đã đăng nhập với role Seller, đã được duyệt |

**Test Script:**
1. Mở trang → Kiểm tra 4 stat cards: Products, Orders, Revenue, Reviews.
2. **Recent orders table** — 5 đơn hàng gần nhất.
3. **Quick action buttons** — "Thêm sản phẩm", "Xem đơn hàng".
4. Layout: sidebar nav (Dashboard, Products, Orders, Store, Reviews).

### 2.3 Seller Products — List

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/products` |
| **Preconditions** | Seller đã đăng nhập |

**Test Script:**
1. Mở trang → Kiểm tra table sản phẩm (image, name, price, stock, status, actions).
2. **Filter/Search** — tìm kiếm sản phẩm.
3. Click "Thêm sản phẩm" → `/agriverse/seller/products/create`.

### 2.4 Seller Products — Create/Edit

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/products/create` / `.../{product}/edit` |
| **Preconditions** | Seller đã đăng nhập |

**Test Script:**
1. **Create form:**
   - Name (bắt buộc).
   - Description (rich text).
   - Price, Compare price (giá cũ).
   - Category (dropdown).
   - Stock (số lượng).
   - Image URL.
   - 3D Model URL.
   - Status toggle (active/inactive).
2. **Validation:** Bỏ trống name → báo lỗi. Price < 0 → báo lỗi.
3. Submit → sản phẩm hiển thị trong danh sách.
4. **Edit:** Sửa thông tin → lưu → cập nhật.
5. **Delete:** Xóa sản phẩm → xác nhận → biến mất khỏi danh sách.
6. **API permission:** Seller chỉ thấy/tạo/sửa/xóa sản phẩm của mình.

### 2.5 Seller Store — Edit

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/store` |
| **Preconditions** | Seller đã đăng nhập |

**Test Script:**
1. Mở trang → Form chỉnh sửa store (name, description, phone, logo URL, address).
2. Sửa thông tin → lưu → API `PUT /agriverse/seller/store`.
3. Kiểm tra store đã cập nhật trên store detail page (public).

### 2.6 Seller Orders — List

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/orders` |
| **Preconditions** | Seller đã đăng nhập, có ít nhất 1 đơn hàng |

**Test Script:**
1. Mở trang → Table: ID, product, buyer, total, status, date.
2. Click vào đơn hàng → `/agriverse/seller/orders/{order}`.

### 2.7 Seller Orders — Detail & Actions

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/orders/{order}` |
| **Preconditions** | Seller đã đăng nhập |

**Test Script:**
1. Kiểm tra chi tiết đơn hàng từ góc nhìn seller.
2. **Hành động theo status:**
   - **pending → confirm:** `POST /agriverse/seller/orders/{order}/confirm`.
   - **confirmed → ship:** `POST /agriverse/seller/orders/{order}/ship`.
   - **shipped → deliver:** `POST /agriverse/seller/orders/{order}/deliver`.
   - **Bất kỳ → cancel:** `POST /agriverse/seller/orders/{order}/cancel`.
3. **Shipping:**
   - `GET /agriverse/seller/orders/{order}/shipping` — chọn dịch vụ vận chuyển.
   - `POST /agriverse/seller/orders/{order}/create-shipment` — tạo vận đơn.
4. Kiểm tra timeline tracker cập nhật sau mỗi bước.

### 2.8 Seller Reviews

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/reviews` |
| **Preconditions** | Seller đã đăng nhập, có sản phẩm được đánh giá |

**Test Script:**
1. Mở trang → Danh sách đánh giá sản phẩm của seller.
2. Xem rating, nội dung, người đánh giá.

### 2.9 Seller Shipping Services (GHTK)

| Field | Value |
|-------|-------|
| **URL** | `GET /agriverse/seller/orders/{order}/shipping` |
| **Preconditions** | Seller, đơn hàng đã confirmed |

**Test Script:**
1. Kiểm tra danh sách dịch vụ vận chuyển (GHTK).
2. Chọn dịch vụ → tạo vận đơn.
3. Kiểm tra mã vận đơn được gắn với đơn hàng.

---

## 3. Admin

### 3.1 Admin Dashboard

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse` |
| **Preconditions** | Đã đăng nhập với role Admin |

**Test Script:**
1. Mở `/admin/agriverse` → Kiểm tra stat cards: Users, Products, Orders, Revenue.
2. **7-day revenue chart** — bar chart hiển thị doanh thu 7 ngày.
3. **Recent orders table** — 5 đơn hàng gần nhất.
4. **Quick links** — "Quản lý sản phẩm", "Quản lý người dùng", etc.
5. Layout: sidebar collapsible, header breadcrumb.

### 3.2 Admin Products

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/products` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Kiểm tra table sản phẩm (tất cả sản phẩm, không filter theo seller).
2. **Search / Filter / Sort**.
3. **Create:** `GET /admin/agriverse/products/create` — form tạo sản phẩm (khác với seller form).
4. **Edit:** Sửa bất kỳ sản phẩm nào.
5. **Delete:** Xóa bất kỳ sản phẩm nào.
6. **3D Model:**
   - Upload: `POST /admin/agriverse/products/{product}/3d-model`.
   - Delete: `DELETE /admin/agriverse/products/{product}/3d-model`.
7. **Approve/Reject:**
   - `POST /admin/agriverse/products/{product}/approve` → product published.
   - `POST /admin/agriverse/products/{product}/reject` → product rejected.
8. **Permission:** Admin thấy tất cả, không bị scope.

### 3.3 Admin Users

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/users` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Table: ID, name, email, role, status (active/inactive), registered date.
2. **Search** — tìm theo tên, email.
3. Click user → `GET /admin/agriverse/users/{user}` — chi tiết user.
4. **Toggle active:** `POST /admin/agriverse/users/{user}/toggle-active` → bật/tắt trạng thái.
5. **Delete:** `DELETE /admin/agriverse/users/{user}` → xóa user (có xác nhận).

### 3.4 Admin Stores

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/stores` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Danh sách cửa hàng (avatar, tên, chủ shop, số sản phẩm, status).
2. **Create:** Tạo store mới.
3. **Edit:** Sửa thông tin store.
4. **Delete:** Xóa store (có xác nhận).

### 3.5 Admin Sellers (Verification)

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/sellers` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Danh sách yêu cầu đăng ký seller (user info, ID documents, status).
2. Click vào yêu cầu → `GET /admin/agriverse/sellers/{verification}`:
   - Xem giấy tờ, thông tin người đăng ký.
   - **Approve:** `POST /admin/agriverse/sellers/{verification}/approve`.
   - **Reject:** `POST /admin/agriverse/sellers/{verification}/reject` (kèm lý do).
3. Kiểm tra user sau khi approve có role Seller, có store được tạo.

### 3.6 Admin Orders

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/orders` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Table tất cả đơn hàng (ID, buyer, seller, total, status, date).
2. **Search / Filter by status**.
3. **Create order:** `GET /admin/agriverse/orders/create` — tạo đơn thủ công.
4. Click order detail → `GET /admin/agriverse/orders/{order}`.
5. **Update status:** `POST /admin/agriverse/orders/{order}/status`.
6. **Delete:** Xóa đơn hàng.

### 3.7 Admin Categories

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/categories` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Danh sách danh mục (tên, slug, icon, số sản phẩm).
2. **Create:** Thêm danh mục mới.
3. **Edit:** Sửa tên/slug/icon.
4. **Delete:** Xóa danh mục (kiểm tra sản phẩm trong danh mục có bị ảnh hưởng?).

### 3.8 Admin Coupons

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/coupons` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Danh sách mã giảm giá (code, discount type, value, usage limit, expiry).
2. **Create:** Tạo coupon (code, % hoặc số tiền, điều kiện áp dụng, hạn dùng).
3. **Edit:** Sửa coupon.
4. **Delete:** Xóa coupon.

### 3.9 Admin Banners

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/banners` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Danh sách banner (image preview, title, link, active status, order).
2. **Create:** Upload banner, nhập title, link.
3. **Edit:** Sửa banner.
4. **Delete:** Xóa banner.
5. **Reorder:** `POST /admin/agriverse/banners/reorder` — kéo thả sắp xếp.

### 3.10 Admin Contracts

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/contracts` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Danh sách hợp đồng (ID, sản phẩm, bên mua, bên bán, status, signature status).
2. Xem chi tiết → tải PDF.
3. **Delete:** Xóa hợp đồng.

### 3.11 Admin Refunds

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/refunds` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Danh sách yêu cầu hoàn tiền (user, order, amount, reason, status).
2. Click detail → xem lý do, bằng chứng.
3. **Approve:** `POST /admin/agriverse/refunds/{refund}/approve`.
4. **Reject:** `POST /admin/agriverse/refunds/{refund}/reject`.

### 3.12 Admin Forum

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/forum` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Danh sách bài viết forum.
2. **Approve:** Duyệt bài viết.
3. **Reject:** Từ chối.
4. **Pin:** Ghim bài viết.
5. **Delete:** Xóa.

### 3.13 Admin Forum Categories

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/forum-categories` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. CRUD danh mục forum (tên, mô tả, màu sắc, order).

### 3.14 Admin Chat Groups

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/chat-groups` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Danh sách nhóm chat.
2. **Create:** Tạo nhóm mới.
3. **View:** Xem chi tiết nhóm, thành viên.
4. **Approve/Reject member:** Duyệt/thành viên chờ.
5. **Delete:** Xóa nhóm.

### 3.15 Admin Transactions

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/transactions` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Danh sách giao dịch (ID, user, type, amount, status, date).
2. Click chi tiết.

### 3.16 Admin Reports

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/reports` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → Dashboard thống kê (revenue, orders, users, sellers charts).
2. **Export** — xuất báo cáo (nếu có).

### 3.17 Admin Subscription Plans

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/plans` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. CRUD gói đăng ký (name, price, duration, features, status).
2. Tạo gói miễn phí / trả phí.

### 3.18 Admin AI Scans

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/scans` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Danh sách AI scanning jobs (user, product, status, date).
2. Xem kết quả scan (3D model generated).
3. Delete job.

### 3.19 Admin Backups

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/backups` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Danh sách backup (`GET /admin/agriverse/backups`).
2. **Stats:** `GET /admin/agriverse/backups/stats` — dung lượng, số lượng.
3. **Create:** `POST /admin/agriverse/backups` — tạo backup mới.
4. **Delete:** Xóa backup cũ.

### 3.20 Admin Files

| Field | Value |
|-------|-------|
| **URL** | `GET /admin/agriverse/files` |
| **Preconditions** | Admin đã đăng nhập |

**Test Script:**
1. Mở trang → File browser (CKFinder hoặc custom).
2. Upload, rename, delete file.

---

## 4. API

### 4.1 Auth Endpoints (Public)

| Endpoint | Test |
|----------|------|
| `POST /api/register` | Đăng ký với name, email, password, phone → trả về user + token |
| `POST /api/login` | Đăng nhập → trả về token + user |
| `GET /api/active/{email}/{key}` | Kích hoạt tài khoản qua email |
| `GET /api/re-active` | Gửi lại email kích hoạt |
| `POST /api/forget-pass` | Quên mật khẩu → gửi email reset |
| `GET /api/reset-pass/{email}/{key}` | Reset mật khẩu |
| `PUT /api/login/google` | Đăng nhập Google |
| `PUT /api/login/facebook` | Đăng nhập Facebook |

### 4.2 Auth Endpoints (Auth Required)

| Endpoint | Test |
|----------|------|
| `GET /api/user/detail` | Xem thông tin user hiện tại |
| `POST /api/user/update` | Cập nhật profile |
| `POST /api/change-pass` | Đổi mật khẩu |
| `GET /api/logout` | Đăng xuất (revoke token) |

### 4.3 Permission Enforcement

**Key test scenarios:**

| Scenario | Expected |
|----------|----------|
| Buyer gọi `POST /api/products` | 403 (no permission) |
| Seller sửa sản phẩm của seller khác | 403 hoặc 404 |
| Buyer xem sản phẩm draft (chưa publish) | 404 |
| Seller xem sản phẩm draft của mình | 200 (thấy) |
| Admin xem tất cả (kể cả draft) | 200 |
| Employee gọi `DELETE /api/products/{id}` | 403 (no permission) |
| Guest gọi bất kỳ API auth nào | 401 |

### 4.4 Cart API

| Endpoint | Test |
|----------|------|
| `GET /api/cart` | Xem giỏ hàng của user hiện tại |
| `POST /api/cart` | Thêm sản phẩm |
| `PUT /api/cart/{cart}` | Cập nhật số lượng |
| `DELETE /api/cart/{cart}` | Xóa item |
| `DELETE /api/cart` | Xóa toàn bộ giỏ |

### 4.5 Wishlist API

| Endpoint | Test |
|----------|------|
| `GET /api/wishlist` | Danh sách yêu thích |
| `POST /api/wishlist` | Thêm sản phẩm |
| `DELETE /api/wishlist/{wishlist}` | Xóa sản phẩm |

### 4.6 Reviews API

| Endpoint | Test |
|----------|------|
| `GET /api/products/{product}/reviews` | Xem đánh giá (public) |
| `POST /api/products/{product}/reviews` | Thêm đánh giá (cần permission order.view) |
| `DELETE /api/reviews/{review}` | Xóa đánh giá |

### 4.7 Notifications API

| Endpoint | Test |
|----------|------|
| `GET /api/notifications` | Danh sách thông báo |
| `GET /api/notifications/unread-count` | Số thông báo chưa đọc |
| `PUT /api/notifications/{id}/read` | Đánh dấu đã đọc |
| `PUT /api/notifications/read-all` | Đánh dấu tất cả đã đọc |
| `DELETE /api/notifications/{id}` | Xóa thông báo |

### 4.8 Coupon API

| Endpoint | Test |
|----------|------|
| `GET /api/coupons` | Danh sách coupon (admin) |
| `POST /api/coupons/validate` | Validate coupon code |

### 4.9 3D/AI Services API

| Endpoint | Test |
|----------|------|
| `GET /api/products/{product}/3d-model` | Lấy model 3D của sản phẩm |
| `GET /api/products/{product}/ar-config` | Cấu hình AR |
| `POST /api/services/3d-scan` | Yêu cầu AI scan |
| `GET /api/services/scan-status/{job}` | Kiểm tra trạng thái scan |
| `GET /api/services/my-scans` | Lịch sử scan của user |

### 4.10 Plant Doctor API

| Endpoint | Test |
|----------|------|
| `POST /api/plant-doctor/diagnose` | Upload ảnh + triệu chứng → kết quả chẩn đoán |
| `GET /api/plant-doctor/history` | Lịch sử chẩn đoán |
| `GET /api/plant-doctor/history/{diagnosis}` | Chi tiết chẩn đoán |
| `DELETE /api/plant-doctor/history/{diagnosis}` | Xóa lịch sử |

### 4.11 Analytics API

| Endpoint | Test |
|----------|------|
| `POST /api/analytics/track` | Track event |
| `POST /api/analytics/page-view` | Track page view |
| `POST /api/analytics/product-view/{product}` | Track product view |
| `POST /api/analytics/add-to-cart` | Track add-to-cart event |
| `GET /api/analytics/recently-viewed` | Sản phẩm đã xem gần đây |

### 4.12 Permission Enforcement Matrix

| Feature | Guest | Buyer | Seller | Employee | Admin |
|---------|-------|-------|--------|----------|-------|
| Browse products (published) | ✅ | ✅ | ✅ | ✅ | ✅ |
| View draft product | ❌ | ❌ | ✅ (own) | ✅ (assigned) | ✅ |
| Create product | ❌ | ❌ | ✅ | ✅ | ✅ |
| Edit product | ❌ | ❌ | ✅ (own) | ✅ (assigned) | ✅ |
| Delete product | ❌ | ❌ | ✅ (own) | ❌ | ✅ |
| Publish product | ❌ | ❌ | ✅ (own) | ❌ | ✅ |
| Upload 3D asset | ❌ | ❌ | ✅ | ✅ | ✅ |
| View 3D asset | ❌ | ✅ | ✅ | ✅ | ✅ |
| Compress 3D asset | ❌ | ❌ | ✅ (own) | ❌ | ✅ |
| View orders | ❌ | ✅ (own) | ✅ (own) | ❌ | ✅ |
| Create order | ❌ | ✅ | ✅ | ❌ | ✅ |
| Cancel order | ❌ | ✅ (own) | ✅ (own) | ❌ | ✅ |
| View contracts | ❌ | ✅ (own) | ✅ (own) | ❌ | ✅ |
| Sign contract | ❌ | ✅ (own) | ✅ (own) | ❌ | ✅ |
| Manage users | ❌ | ❌ | ❌ | ❌ | ✅ |
| View reports | ❌ | ❌ | ❌ | ❌ | ✅ |
| Manage coupons | ❌ | ❌ | ❌ | ❌ | ✅ |
| Manage banners | ❌ | ❌ | ❌ | ❌ | ✅ |
| Manage backups | ❌ | ❌ | ❌ | ❌ | ✅ |

### 4.13 API — Reports & Admin

| Endpoint | Permission | Test |
|----------|------------|------|
| `GET /api/reports/seller/revenue` | order.view | Seller thấy doanh thu của mình |
| `GET /api/reports/buyer/stats` | order.view | Buyer thấy thống kê mua hàng |
| `GET /api/admin/commissions` | admin.access | Admin thấy tất cả hoa hồng |
| `GET /api/admin/revenue` | admin.access | Admin thấy tất cả doanh thu |
| `GET /api/admin/sellers` | admin.access | Admin thấy tất cả seller |

### 4.14 API — Digital Passport

| Endpoint | Permission | Test |
|----------|------------|------|
| `GET /api/products/{product}/digital-passport` | product.view | Xem passport |
| `POST /api/products/{product}/digital-passport` | product.edit | Tạo passport |
| `PUT /api/passport/{passport}` | product.edit | Sửa passport |
| `DELETE /api/passport/{passport}` | product.edit | Xóa passport |
| `GET /api/products/{product}/passport-summary` | product.view | Xem summary |

### 4.15 API — Subscriptions & Plans

| Endpoint | Permission | Test |
|----------|------------|------|
| `GET /api/subscriptions` | subscription.view | Xem danh sách subscription |
| `GET /api/subscriptions/{subscription}` | subscription.view | Xem chi tiết |
| `POST /api/subscriptions` | subscription.create | Tạo subscription (admin) |
| `POST /api/subscriptions/{subscription}/cancel` | subscription.edit | Hủy subscription |
| `GET /api/subscription-plans` | plan.view | Xem danh sách gói |
| `GET /api/subscription-plans/{plan}` | plan.view | Xem chi tiết gói |

### 4.16 API — Forum

| Endpoint | Permission | Test |
|----------|------------|------|
| `GET /api/forum/categories` | Public | Xem danh mục forum |
| `GET /api/forum/posts` | Public | Xem bài viết |
| `POST /api/forum/posts` | Auth | Tạo bài viết |
| `GET /api/forum/posts/{post}` | Public | Xem chi tiết |
| `PUT /api/forum/posts/{post}` | Auth (owner) | Sửa bài viết |
| `DELETE /api/forum/posts/{post}` | Auth (owner) | Xóa bài viết |
| `GET /api/forum/posts/{post}/comments` | Public | Xem comments |
| `POST /api/forum/posts/{post}/comments` | Auth | Thêm comment |
| `POST /api/forum/posts/{post}/like` | Auth | Like/unlike |
| `GET /api/forum/my-posts` | Auth | Bài viết của tôi |

---

## 5. Cross-Cutting

### 5.1 Mobile Responsiveness

Kiểm tra tất cả các trang chính trên kích thước màn hình 375px (mobile):
- Header chuyển sang hamburger menu.
- Grid sản phẩm chuyển 1-2 cột.
- Sidebar ẩn / trở thành horizontal scroll.
- Modal / drawer hiển thị đúng.
- Footer responsive.

### 5.2 Error Handling

| Scenario | Expected |
|----------|----------|
| Truy cập route không tồn tại | 404 page |
| API không auth | 401 |
| API không permission | 403 |
| Form submit với dữ liệu không hợp lệ | Validation errors (422) |
| Server error (500) | Error page hoặc fallback message |

### 5.3 UI/Accessibility

| Check | Description |
|-------|-------------|
| Contrast | Tất cả text có contrast ratio ≥ 4.5:1 (WCAG AA) |
| Focus indicators | Có thể tab qua các interactive elements |
| Image alt text | Tất cả ảnh có alt attribute |
| Skip to content | Có skip navigation link |
| ARIA labels | Form inputs, buttons có nhãn phù hợp |
| Touch targets | Nút ≥ 44px trên mobile |

### 5.4 Performance

| Metric | Target |
|--------|--------|
| Page load (4G) | < 3s |
| 3D model load | < 3s (với Draco compression) |
| API response | < 500ms |
| AR startup | < 2s |
| Chart render | < 1s |

### 5.5 Security

| Check | Description |
|-------|-------------|
| CSRF | All state-changing requests require CSRF token |
| XSS | User input escaped/sanitized |
| SQL injection | Eloquent ORM (parameterized queries) |
| Auth rate limiting | Login attempts limited (throttle:api) |
| File upload validation | Only allowed types, size limits |
| Backup access | Only admin can manage backups |
| 3D model sanitization | Validate uploaded .glb/.gltf files |
