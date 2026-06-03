# 🗺️ Kế hoạch Phát triển & Danh mục Chức năng cần thực hiện - Orchestrix

Tài liệu này liệt kê chi tiết các chức năng và các trang (Pages) cần phải xây dựng thêm để biến Orchestrix thành một hệ thống quản trị đa nhiệm hoàn chỉnh.

---

## 1. Hệ thống Quản trị Trung tâm (Admin Central)
Đây là "bộ não" của toàn hệ thống, quản lý các module và dự án con.

### Chức năng cần làm:
- [x] **Dashboard Tổng hợp:** Trang thống kê dữ liệu từ tất cả các module (lượt xem Blog, số lượng dự án Portfolio).
- [ ] **Hệ thống Phân quyền (ACL):** Quản lý Vai trò (Roles) và Quyền (Permissions) cho từng người dùng vào từng module.
- [x] **Quản lý Project:** Giao diện để thêm mới Project và gán Domain/Module cho Project đó (thay vì sửa file JSON thủ công).
- [ ] **Cấu hình Hệ thống:** Quản lý thông tin chung, SMTP Email, cấu hình lưu trữ (S3/Local).

### Các trang cần xây dựng:
- [x] `admin/dashboard`: Trang chủ quản trị với các biểu đồ Chart.js (đã hoàn thiện giao diện và data binding).
- [x] `admin/projects`: Danh sách và form thêm/sửa Project (đã xong).
- [ ] `admin/users`: Quản lý nhân viên và phân quyền.
- [ ] `admin/settings`: Cài đặt hệ thống tổng thể.

---

---

## 3. Module Blog & Portfolio
Phục vụ cho việc quảng bá thương hiệu cá nhân hoặc doanh nghiệp.

### Chức năng cần làm:
- [x] **Trình soạn thảo bài viết:** Tích hợp CKEditor hoặc TinyMCE để viết bài có hình ảnh, video (đã xong cho Blog).
- [x] **Quản lý SEO:** Thêm các trường Meta Title, Meta Description, Schema.org cho bài viết và dự án (đã xong cho Blog).
- [ ] **Tối ưu hình ảnh:** Tự động resize và convert ảnh sang định dạng WebP để tăng tốc độ tải trang frontend.

### Các trang cần xây dựng:
- [x] `admin/blog/posts/create`: Trang viết bài với trình soạn thảo giàu văn bản (đã xong).
- [ ] `admin/portfolio/projects`: Giao diện quản lý danh mục dự án, cho phép kéo thả thứ tự hiển thị.

---

## 4. Các tính năng Kỹ thuật bổ trợ (Technical Features)
- [x] **Log hoạt động (Activity Logs):** Ghi lại ai đã sửa dữ liệu gì, vào lúc nào để dễ dàng kiểm soát (đã xây dựng Service và Migration).
- [ ] **Thông báo (Notification System):** Hệ thống thông báo nội bộ (chuông báo trên admin) và thông báo qua Telegram/Email.
- [ ] **Backup dữ liệu:** Chức năng sao lưu Database và Source code định kỳ.

---

---

## ✅ Báo cáo Lỗi & Kiểm thử (Cập nhật 29/04/2026)

### Kết quả:
1. **Backend (PHPUnit):** 6/6 test passed. Logic load module và các route chính đã ổn định.
2. **Frontend Admin (Playwright):** 3/3 test passed. Giao diện Premium Dashboard, quản lý module và điều hướng sidebar hoạt động hoàn hảo.
3. **Database:** Đã nạp dữ liệu mẫu thành công.

### Các vấn đề đã giải quyết:
- [x] Fix lỗi SQLite driver bằng cách chuyển sang MySQL.
- [x] Fix lỗi 404/500 do logic load module phân biệt hoa thường.
- [x] Fix lỗi Route not defined trong layout Portfolio.
- [x] Cập nhật toàn bộ Page Objects trong Playwright để khớp với giao diện mới.

---

## 5. Danh sách các Trang Frontend (Cho khách hàng)
Dựa trên kiến trúc Multi-project, mỗi dự án sẽ có các trang:
- **Trang chủ (Landing Page):** Giới thiệu dịch vụ.
- **Trang danh sách phòng:** Cho phép khách tìm phòng trống.
- **Trang Blog/Tin tức:** Hiển thị bài viết.
- **Trang Portfolio:** Hiển thị các sản phẩm/dự án đã làm.
- **Cổng thông tin khách thuê (Tenant Portal):** Nơi khách vào xem hóa đơn và gửi yêu cầu sửa chữa.

---
**Ghi chú:** Bạn có thể sử dụng "Master Prompt" tôi đã cung cấp để yêu cầu AI thực hiện từng mục trong danh sách này một cách chuẩn xác.
