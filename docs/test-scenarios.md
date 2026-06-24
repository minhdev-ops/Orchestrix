# TÀI LIỆU KỊCH BẢN KIỂM THỬ (TEST SCENARIOS) — ORCHESTRIX (BONSAI EDITION)

> **Dự án:** Hệ thống TMĐT Bonsai tích hợp 3D, AI và Blockchain.
> **Kiến trúc:** Laravel (Core) + JakartaEE (Real-time Messaging) + Vue 3 (Frontend).

---

## 1. QUẢN LÝ TÀI KHOẢN & PHÂN QUYỀN (AUTH)

| ID | Chức năng | Kịch bản kiểm thử | Kết quả mong đợi |
|---|---|---|---|
| TC-01 | Đăng ký | Đăng ký tài khoản mới với các vai trò (Buyer, Seller). | Tài khoản được tạo, gửi email kích hoạt, phân đúng Role. |
| TC-02 | Xác thực 2 lớp | Đăng ký Seller yêu cầu xác thực mã OTP qua Email/SMS. | Chỉ cho phép tiếp tục khi nhập đúng mã OTP 6 số. |
| TC-03 | Phân quyền (RBAC) | User thường cố gắng truy cập `/admin` hoặc `Seller Dashboard`. | Hệ thống chặn truy cập và trả về lỗi 403 Forbidden. |
| TC-04 | Đăng nhập tập trung | Đăng nhập bên Laravel và kiểm tra kết nối WebSocket JakartaEE. | Token JWT được chia sẻ, hệ thống Chat tự động nhận diện User. |

---

## 2. TRẢI NGHIỆM ĐỒ HỌA 3D & THỰC TẾ ẢO (3D/AR)

| ID | Chức năng | Kịch bản kiểm thử | Kết quả mong đợi |
|---|---|---|---|
| TC-05 | Hiển thị 3D | Truy cập trang chi tiết sản phẩm Bonsai có mô hình 3D. | Model viewer load thành công, cho phép xoay/thu phóng mượt mà. |
| TC-06 | Tối ưu hóa Asset | Seller upload file .GLB dung lượng lớn (50MB). | Hệ thống tự động nén Draco, dung lượng giảm xuống < 5MB mà vẫn giữ nét. |
| TC-07 | Chế độ AR | Nhấn "View in AR" trên thiết bị di động (Android/iOS). | Camera kích hoạt, cây Bonsai ảo hiển thị đúng tỉ lệ 1:1 trong phòng. |
| TC-08 | Cắt tỉa ảo (Vòng 2) | Sử dụng công cụ kéo ảo cắt một cành trên mô hình 3D. | Mô hình thay đổi hình dạng ngay lập tức, lưu lại trạng thái cắt tỉa. |

---

## 3. HỘ CHIẾU THỰC VẬT SỐ & BLOCKCHAIN (PASSPORT/NFT)

| ID | Chức năng | Kịch bản kiểm thử | Kết quả mong đợi |
|---|---|---|---|
| TC-09 | Nhật ký Timeline | Seller thêm một sự kiện "Thay chậu & Uốn cành" kèm ảnh. | Dòng thời gian cập nhật đúng, người mua có thể xem lịch sử này. |
| TC-10 | Đúc NFT (Vòng 2) | Nhấn "Mint NFT" cho một cây Bonsai cổ thụ có giá trị cao. | Metadata được đẩy lên IPFS, gọi Smart Contract tạo Token trên mạng. |
| TC-11 | Xác thực nguồn gốc | Quét mã QR trên thân cây thật để truy xuất Hộ chiếu số. | Hiển thị thông tin chính xác từ Database và bằng chứng trên Blockchain. |

---

## 4. TRỢ LÝ AI & CHẨN ĐOÁN BỆNH (AI PLANT DOCTOR)

| ID | Chức năng | Kịch bản kiểm thử | Kết quả mong đợi |
|---|---|---|---|
| TC-12 | Chẩn đoán hình ảnh | Upload ảnh lá cây Bonsai bị đốm trắng/vàng. | AI (Gemini Vision) trả về tên bệnh (ví dụ: Nấm trắng) và cách chữa. |
| TC-13 | Phân luồng chuyên gia | AI đánh giá bệnh phức tạp vượt quá khả năng xử lý tự động. | Hệ thống tự động mở cửa sổ Chat kết nối User với Chuyên gia thực thụ. |
| TC-14 | Thư viện tri thức AI | Hỏi AI cách chăm sóc cây Tùng La Hán vào mùa đông. | AI trích xuất dữ liệu từ "Knowledge Base" của hệ thống để trả lời chính xác. |

---

## 5. ĐẤU GIÁ & CHAT THỜI GIAN THỰC (JAKARTAEE REAL-TIME)

| ID | Chức năng | Kịch bản kiểm thử | Kết quả mong đợi |
|---|---|---|---|
| TC-15 | Chat nhị phân | Gửi tin nhắn chứa emoji và ảnh giữa hai người dùng. | Dữ liệu được nén Protobuf, truyền qua JakartaEE, hiển thị tức thì (<100ms). |
| TC-16 | Đấu giá trực tiếp | Nhiều người cùng trả giá (Bid) trong 5 giây cuối cùng. | Giá cao nhất được cập nhật theo thời gian thực, không bị xung đột dữ liệu. |
| TC-17 | Đếm ngược đồng bộ | Kiểm tra đồng hồ đếm ngược phiên đấu giá trên nhiều thiết bị. | Thời gian đếm ngược hoàn toàn khớp nhau nhờ đồng bộ qua Redis Pub/Sub. |
| TC-18 | Chịu tải (Scalability) | Giả lập 1000 người dùng cùng tham gia một phiên đấu giá. | Server JakartaEE vẫn vận hành ổn định, tin nhắn không bị nghẽn. |

---

## 6. LUỒNG TMĐT CỐT LÕI (ECOMMERCE CORE)

| ID | Chức năng | Kịch bản kiểm thử | Kết quả mong đợi |
|---|---|---|---|
| TC-19 | Tính phí vận chuyển | Chọn địa chỉ giao hàng từ Hà Nội đi Cà Mau cho cây 20kg. | Gọi API GHN trả về đúng phí ship dựa trên cân nặng và kích thước đóng kiện gỗ. |
| TC-20 | Thanh toán Escrow | Thực hiện thanh toán cho đơn hàng Bonsai tiền tỷ. | Tiền được cổng thanh toán giữ lại, hệ thống ghi nhận trạng thái "Đã thanh toán - Chờ giao hàng". |
| TC-21 | Quản lý Giỏ hàng | Thêm nhiều linh kiện (chậu, kéo) từ các nhà vườn khác nhau. | Giỏ hàng tự động phân nhóm theo nhà vườn để tính ship riêng biệt. |

---

## 7. QUẢN TRỊ & HỆ THỐNG (ADMIN/SELLER)

| ID | Chức năng | Kịch bản kiểm thử | Kết quả mong đợi |
|---|---|---|---|
| TC-22 | Kiểm duyệt Seller | Admin xem ảnh CCCD và đơn đăng ký để duyệt nhà vườn. | Seller được cấp quyền đăng bán sau khi Admin nhấn "Approve". |
| TC-23 | Thống kê doanh thu | Seller xem biểu đồ tăng trưởng doanh thu theo tháng. | Số liệu khớp với tổng các đơn hàng đã hoàn thành trừ đi phí sàn. |
| TC-24 | Quản lý Module | Admin bật/tắt module "Đấu giá" hoặc "AI Doctor". | Giao diện người dùng tự động ẩn/hiện các tính năng tương ứng. |

---

## 8. KIỂM THỬ PHI CHỨC NĂNG (NON-FUNCTIONAL)

| ID | Chức năng | Kịch bản kiểm thử | Kết quả mong đợi |
|---|---|---|---|
| TC-25 | Hiệu năng 3D | Mở trang sản phẩm 3D trên thiết bị cấu hình yếu. | FPS duy trì mức ổn định nhờ kỹ thuật Level of Detail (LOD). |
| TC-26 | Bảo mật dữ liệu | Cố tình SQL Injection hoặc XSS vào ô Search/Comment. | Hệ thống lọc sạch các ký tự nguy hiểm, không bị lỗi bảo mật. |
| TC-27 | Khả năng phục hồi | Tắt đột ngột một Node JakartaEE khi đang đấu giá. | Node khác tự động tiếp quản session (Failover) nhờ dữ liệu lưu trong Redis. |
