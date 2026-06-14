# AgriVerse Hub: Lộ trình Triển khai MVP (Roadmap)

Dựa trên nguyên tắc **"Bottom-up & Core-first" (Xây dựng từ lõi lên)** được đề cập trong tài liệu phân tích kiến trúc, dưới đây là lộ trình triển khai chi tiết cho sản phẩm khả thi tối thiểu (MVP). Lộ trình này ưu tiên giải quyết các bài toán khó nhất về công nghệ trước khi hoàn thiện giao diện người dùng.

---

## Giai đoạn 1: Xây dựng Xương sống Kỹ thuật (Tuần 1 - Tuần 3)
*Mục tiêu: Xử lý bài toán khó nhất - Tự động hóa việc nén và hiển thị file 3D.*

1. **Thiết lập Hạ tầng (DevOps & Database):**
   - Khởi tạo repo dự án với kiến trúc Docker (Laravel + MySQL/PostgreSQL).
   - Thiết kế và migrate các bảng cơ sở dữ liệu cốt lõi (`users`, `products`, `asset_3d`).
2. **Xây dựng 3D Asset Pipeline (Core Backend):**
   - Viết Script (bằng Node.js hoặc Python) chạy ngầm (Background Job) để tiếp nhận file `.obj`/`.fbx`.
   - Tích hợp thư viện để tự động giảm polygon (Mesh Simplification) và nén bằng **Draco**.
   - Xuất ra file định dạng `.glb` với dung lượng nhỏ hơn 5MB.
3. **Kiểm thử Pipeline:**
   - Upload 1 file máy cày nặng >50MB và kiểm tra xem script có tự động nén thành `.glb` dưới 5MB mà vẫn giữ được chi tiết hay không.

## Giai đoạn 2: Phát triển Core API & Nghiệp vụ cơ bản (Tuần 4 - Tuần 6)
*Mục tiêu: Quản lý luồng dữ liệu và phân quyền.*

1. **Hệ thống Quản trị & Xác thực (Auth/RBAC):**
   - Xây dựng hệ thống đăng nhập/đăng ký bằng JWT.
   - Thiết lập phân quyền 4 lớp (Admin, Seller, Staff, Buyer).
2. **Quản lý Sản phẩm & Hồ sơ số (Digital Passport):**
   - Xây dựng các API CRUD cho bảng `products`.
   - Thiết kế cấu trúc JSON cho trường `technical_specs` (thông số kỹ thuật nông nghiệp).
3. **Tích hợp Cloud Storage:**
   - Kết nối hệ thống với AWS S3 (hoặc giải pháp tương đương) để lưu trữ các file `.glb` sau khi nén.

## Giai đoạn 3: Phát triển Trải nghiệm Frontend 3D/AR (Tuần 7 - Tuần 9)
*Mục tiêu: Đưa mô hình 3D lên trình duyệt web mượt mà dưới 3 giây.*

1. **Tích hợp Three.js / Babylon.js:**
   - Xây dựng component 3D Viewer trên Frontend (React/Next.js).
   - Gọi API lấy link file `.glb` từ S3 và render lên trình duyệt.
   - Xử lý cử chỉ tương tác (Xoay 360 độ, Zoom).
2. **Kích hoạt WebXR (WebAR):**
   - Tích hợp WebXR API để thêm nút "Ướm thử AR" trên giao diện Mobile.
   - Viết logic nhận diện mặt phẳng sàn và hiển thị mô hình với tỉ lệ 1:1.
3. **Phát triển cơ chế Fallback (Phương án dự phòng):**
   - Viết logic kiểm tra thiết bị: Nếu trình duyệt không hỗ trợ WebGL/WebXR, tự động chuyển sang hiển thị Ảnh 2D chất lượng cao hoặc Video 360.

## Giai đoạn 4: Nghiệp vụ Thương mại & E-Contract (Tuần 10 - Tuần 11)
*Mục tiêu: Hoàn thiện tính năng bán hàng và bảo vệ giao dịch.*

1. **Hợp đồng điện tử (E-Contract):**
   - Xây dựng chức năng tự động tạo file PDF Hợp đồng từ thông tin người mua, người bán và sản phẩm.
   - Tích hợp chữ ký số cơ bản và lưu trữ `content_hash` để đảm bảo tính pháp lý.
2. **Tính toán Hoa hồng & Thanh toán:**
   - Xây dựng logic tự động trích xuất phí hoa hồng (Commission) cho hệ thống sau khi hợp đồng được ký.
3. **Hoàn thiện UI/UX:**
   - Thiết kế giao diện Dashboard cho Nhà bán hàng (Seller) và Người mua (Buyer).

## Giai đoạn 5: Tích hợp AI cơ bản & Nghiệm thu (Tuần 12)
*Mục tiêu: Sẵn sàng ra mắt MVP.*

1. **Trợ lý ảo RAG (Tùy chọn cho MVP):**
   - Khởi tạo một RAG Chatbot cơ bản (có thể dùng OpenAI API + LangChain) đọc dữ liệu từ bảng `technical_specs` để trả lời câu hỏi của người mua.
2. **Kiểm thử Hiệu năng (Performance Test):**
   - Chạy tải (Load testing) và kiểm tra tốc độ load trang trên mạng 4G. Đảm bảo quy tắc **< 3 giây**.
3. **Triển khai Production:**
   - Đóng gói toàn bộ frontend và backend, cấu hình CI/CD và đẩy lên Ubuntu Server.
   - Thiết lập CDN để phân phối file 3D tốc độ cao.

---
**Lời khuyên:** Hãy coi việc nén file (Giai đoạn 1) là **yếu tố sống còn**. Nếu phần này thất bại, các tính năng AR và Web 3D phía sau sẽ không thể hoạt động mượt mà.
