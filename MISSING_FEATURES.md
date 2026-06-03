# 📋 Danh sách các Chức năng còn thiếu và cần Hoàn thiện - Orchestrix

Dưới đây là kết quả rà soát chi tiết mã nguồn hiện tại (Backend & Frontend) đối chiếu với yêu cầu của một hệ thống quản trị chuyên nghiệp.

---

## 🛠️ 1. Hệ thống Quản trị Trung tâm (Admin Central)
Mặc dù đã có Dashboard và quản lý User/Project, nhưng các phần "xương sống" sau vẫn chưa có:
- [ ] **Cấu hình SMTP Email:** Cần giao diện để Admin nhập thông tin Server Mail (để gửi hóa đơn, reset mật khẩu).
- [ ] **Cấu hình Lưu trữ (Storage):** Tùy chọn lưu ảnh lên Local hoặc Cloud (S3, Cloudinary).
- [ ] **Hệ thống Menu động:** Hiện tại Sidebar đang hardcode trong Blade layout. Cần cho phép Admin kéo thả sắp xếp menu.
- [ ] **Trang Cài đặt chung (General Settings):** Thay đổi Logo, Favicon, Tiêu đề trang web.

---

---

## 📝 3. Module Blog
Đã có CKEditor và SEO, nhưng cần thêm:
- [ ] **Quản lý Media:** Thư viện ảnh tập trung cho bài viết (thay vì chỉ upload thumbnail).
- [ ] **Hệ thống Tag & Category Admin:** Hiện tại có model nhưng có thể thiếu giao diện CRUD riêng biệt cho chuyên mục và thẻ.
- [ ] **Bình luận (Comments):** Giao diện duyệt bình luận, ẩn/hiện bình luận của độc giả.

---

## 💼 4. Module Portfolio
Cần nâng cấp để đồng bộ với Module Blog:
- [ ] **Trình soạn thảo CKEditor:** Hiện tại Portfolio vẫn đang dùng textarea thường hoặc Markdown thô sơ cho phần mô tả dự án.
- [ ] **Quản lý SEO cho Dự án:** Thêm các trường Meta Title/Description cho từng dự án portfolio.
- [ ] **Thư viện ảnh Dự án (Project Gallery):** Một dự án cần nhiều ảnh slide thay vì chỉ 1 ảnh đại diện.

---

## 🚀 5. Các tính năng Kỹ thuật (Technical)
- [ ] **Hệ thống Thông báo (Notification):**
    - [ ] Gửi Telegram khi có hóa đơn mới hoặc khách thuê gửi yêu cầu.
    - [ ] Chuông thông báo (Realtime với Laravel Reverb/Pusher) trên giao diện Admin.
- [ ] **Sao lưu (Backup):** Chức năng click để tải về bản backup Database (.sql).
- [ ] **Xử lý hình ảnh:** Tích hợp `Intervention Image` để tự động resize và nén ảnh khi upload để tối ưu tốc độ.

---

## 🌐 6. Frontend (Khách hàng)
- [ ] **Tenant Portal:** Trang riêng cho khách thuê đăng nhập để xem hóa đơn và số điện nước của mình.
- [ ] **Trang tìm phòng:** Giao diện cho người lạ vào xem danh sách phòng trống và liên hệ thuê.

---
**Ghi chú:** Đây là bản danh sách chi tiết nhất để "hoàn hảo hóa" dự án Orchestrix. Bạn có muốn tôi thực hiện mục nào trong danh sách này ngay không?
