# Test Plan: AgriVerse Admin Panel

## 1. Tổng quan ứng dụng (App Overview)
- **Mục đích**: Quản trị trung tâm cho nền tảng thương mại nông nghiệp số (AgriVerse).
- **Tech Stack**: Laravel 11, Blade Templates, TailwindCSS.
- **Base URL**: `http://127.0.0.1:8000/admin/agriverse`

---

## 2. Danh sách Modules

| Module | Mô tả | Risk Level | Số Scenarios dự kiến |
|---|---|---|---|
| **Dashboard** | Trang tổng quan thống kê doanh thu, số lượng đơn hàng, cửa hàng, sản phẩm. | 🟡 Medium | 5 |
| **Sản phẩm (Products)** | Quản lý danh sách sản phẩm, thêm/sửa/xóa, cập nhật trạng thái (Draft, Published, Archived). | 🔴 High | 12 |
| **Cửa hàng (Stores)** | Quản lý cửa hàng của các đối tác, thay đổi trạng thái (Active, Inactive). | 🔴 High | 8 |
| **Đơn hàng (Orders)** | Xem chi tiết đơn hàng, lịch sử trạng thái, và cập nhật trạng thái đơn hàng. | 🔴 High | 15 |
| **Danh mục (Categories)** | Quản lý cây danh mục sản phẩm. | 🟡 Medium | 7 |
| **Hợp đồng (Contracts)** | Quản lý các hợp đồng số giữa người mua và người bán. | 🔴 High | 6 |
| **Mã giảm giá (Coupons)** | Tạo và quản lý mã giảm giá, giới hạn số lần sử dụng và thời hạn. | 🟡 Medium | 8 |
| **Gói dịch vụ (Plans)** | Quản lý các gói đăng ký cho đối tác (Basic, Pro, Advanced). | 🟡 Medium | 6 |
| **Báo cáo (Reports)** | Dashboard chuyên sâu báo cáo doanh thu, sản phẩm bán chạy. | 🟢 Low | 4 |
| **Quét AI (Scans)** | Quản lý hàng đợi và kết quả quét 3D Model bằng AI. | 🟡 Medium | 5 |

---

## 3. User Flows (Luồng người dùng chính)

### Flow 1: Thêm Sản Phẩm Mới (Happy Path)
1. Người dùng truy cập trang Danh sách Sản phẩm (`/admin/agriverse/products`).
2. Bấm nút **"Thêm sản phẩm"**.
3. Điền đầy đủ thông tin vào Form: Tên sản phẩm, Giá bán, Tồn kho, Danh mục, Trạng thái (Published).
4. Bấm **"Tạo sản phẩm"**.
5. Hệ thống lưu thành công, hiển thị Flash message "Sản phẩm đã được tạo", và quay về danh sách.

### Flow 2: Xử lý Đơn Hàng (Order Management)
1. Truy cập trang Đơn hàng (`/admin/agriverse/orders`).
2. Nhấn **"Chi tiết"** trên một đơn hàng đang ở trạng thái Pending.
3. Kiểm tra thông tin người mua và chi tiết sản phẩm.
4. Chọn trạng thái mới (VD: "Confirmed" hoặc "Shipping") từ dropdown và nhập Ghi chú.
5. Nhấn **"Cập nhật"**. Hệ thống lưu trạng thái, thêm vào lịch sử (Status history tracker).

### Flow 3: Thêm Mã Giảm Giá
1. Truy cập trang Mã giảm giá (`/admin/agriverse/coupons`).
2. Bấm **"Thêm mã"**.
3. Nhập mã code, loại giảm (Percent/Fixed), giá trị, và thời hạn.
4. Bấm **"Tạo mã"**.

---

## 4. Test Scenarios (Kịch bản kiểm thử)

| ID | Module | Scenario | Priority | Loại | Automation Candidate |
|---|---|---|---|---|---|
| TC_PRD_01 | Products | Tạo sản phẩm mới với đầy đủ thông tin hợp lệ | P1 | Happy | ✅ Có |
| TC_PRD_02 | Products | Bỏ trống các trường bắt buộc (Tên, Giá, Tồn kho) khi tạo sản phẩm | P2 | Negative | ✅ Có |
| TC_PRD_03 | Products | Cập nhật trạng thái sản phẩm từ Draft sang Published | P1 | Happy | ✅ Có |
| TC_PRD_04 | Products | Tìm kiếm sản phẩm theo tên không tồn tại | P3 | Edge | ❌ Không |
| TC_ORD_01 | Orders | Xem chi tiết đơn hàng (kiểm tra tổng tiền tính toán đúng) | P1 | Happy | ✅ Có |
| TC_ORD_02 | Orders | Cập nhật trạng thái đơn hàng thành công | P1 | Happy | ✅ Có |
| TC_ORD_03 | Orders | Chuyển đổi trạng thái đơn hàng không hợp lệ (VD: Delivered -> Pending) | P2 | Negative | ✅ Có |
| TC_STR_01 | Stores | Thêm mới cửa hàng hợp lệ | P2 | Happy | ✅ Có |
| TC_CUP_01 | Coupons | Tạo mã giảm giá vượt quá 100% | P2 | Negative | ✅ Có |
| TC_CUP_02 | Coupons | Đặt Expiration Date trong quá khứ | P2 | Negative | ✅ Có |

---

## 5. Automation Strategy (Chiến lược tự động hóa)
* **Framework đề xuất**: Playwright + TypeScript.
* **Page Object Model (POM)**: Khuyến khích tạo các file Page Object theo từng module (ProductPage, OrderPage).
* **Tiếp theo**: Dùng Mode FULL để tự động sinh test scripts hoàn chỉnh dựa trên các scenarios ở trên.

---

## 6. Kết quả Khám Phá Cửa Hàng Public (Storefront - /agriverse)

Trong quá trình test giao diện người dùng (Storefront) tại `/agriverse`, hệ thống phát hiện các lỗi nghiêm trọng (Critical Bugs) ảnh hưởng trực tiếp đến End-to-End flow:

| Bug ID | Mức độ | Tính năng | Mô tả lỗi | Nguyên nhân gốc rễ (Root Cause) |
|---|---|---|---|---|
| BUG_SF_01 | 🔴 Critical | Phân loại (Categories) | Hiển thị "0 sản phẩm" ở tất cả danh mục, dù có sản phẩm thuộc danh mục đó. | Lỗi filter/slug mismatch hoặc không load đúng relationship trong Controller. |
| BUG_SF_02 | 🟡 Medium | Xác thực (Authentication) | Sau khi đăng nhập thành công (Buyer), thanh điều hướng vẫn hiện nút "Đăng nhập" / "Đăng ký". | Lỗi xử lý state phiên đăng nhập (Session/Vue State) trên Header component. |
| BUG_SF_03 | 🔴 Critical | Giỏ hàng (Cart) | Bấm "Thêm vào giỏ" gây crash trắng trang và văng về Home. Không thêm được hàng. | Frontend gọi sai endpoint `POST /api/cart` (gây lỗi 401 do thiếu token/session) thay vì `POST /agriverse/api/cart/add`. |
| BUG_SF_04 | 🔴 Critical | Thanh toán (Checkout) | Bấm "Đặt hàng" gây crash trắng trang và văng về Home. Không tạo được đơn hàng. | Component Checkout bị fix cứng gọi `POST /api/orders` (lỗi 401) thay vì endpoint chuẩn `POST /agriverse/api/checkout/process`. |
| BUG_SF_05 | 🔴 Critical | Yêu thích (Wishlist) | Truy cập trang Yêu thích (`/agriverse/yeu-thich`) bị văng màn hình trắng. | Lỗi logic render giao diện Vue (Unhandled render exception) ở trang Index Wishlist. |

### Đề xuất hành động tức thời:
- Phải ưu tiên **FIX gấp BUG_SF_03 và BUG_SF_04** (liên quan đến API endpoint trên Vue component) vì đây là "Blocker" ngăn chặn toàn bộ quá trình test luồng Mua hàng (Checkout Flow).
- Sửa lại file `.vue` (nút Add to Cart và form Checkout) để gọi đúng API endpoint mà Ziggy/Laravel đã expose.
