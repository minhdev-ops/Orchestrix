# KHÓA LUẬN TỐT NGHIỆP: NGHIÊN CỨU VÀ XÂY DỰNG HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ CÂY CẢNH NGHỆ THUẬT TÍCH HỢP CÔNG NGHỆ 3D VÀ TRUY XUẤT NGUỒN GỐC SỐ

## 1. Tóm tắt sản phẩm

### 1.1. Tổng quan sản phẩm
**Orchestrix - Bonsai Edition** là một nền tảng thương mại điện tử (Ecommerce) thế hệ mới, chuyên biệt cho thị trường cây cảnh nghệ thuật và bonsai cao cấp. Hệ thống giải quyết bài toán minh bạch về hình dáng qua công nghệ 3D/AR, xác thực nguồn gốc qua Hộ chiếu số Blockchain, và tối ưu hóa tương tác thời gian thực thông qua kiến trúc phân tán JakartaEE kết hợp giao thức nhị phân Protobuf.

### 1.2. Mô tả ngắn gọn
Sản phẩm là một sàn giao dịch đa người bán (Multi-vendor Marketplace) tích hợp hệ sinh thái tri thức. Người dùng có thể tương tác trực quan với mô hình 3D của cây, truy xuất lịch sử tạo tác qua Digital Passport, thảo luận trên diễn đàn nghệ nhân và nhận hỗ trợ chẩn đoán bệnh cây từ Trợ lý AI thông minh.

### 1.3. Đối tượng sử dụng
*   **Người sưu tầm (Buyers):** Những người yêu thích Bonsai, đòi hỏi sự chính xác tuyệt đối về hình thái và nguồn gốc tác phẩm.
*   **Nghệ nhân & Nhà vườn (Sellers):** Đơn vị cung cấp sản phẩm, cần công cụ chuyên nghiệp để số hóa và thương mại hóa tác phẩm nghệ thuật sống.
*   **Chuyên gia nông nghiệp (Experts):** Đội ngũ hỗ trợ chuyên sâu về kỹ thuật chăm sóc và thẩm định giá trị.

### 1.4. Lợi ích nổi bật
*   **Xóa bỏ giới hạn 2D:** Trải nghiệm 3D/AR chân thực giúp đánh giá đúng dáng thế (đế, thân, chi dăm) của cây từ xa.
*   **Minh bạch hóa giá trị:** Hộ chiếu thực vật số lưu trữ toàn bộ vòng đời sản phẩm, bảo tồn giá trị nghệ thuật qua nhiều đời chủ.
*   **Hiệu năng vượt trội:** Công nghệ JakartaEE + Protobuf đảm bảo luồng dữ liệu đấu giá và chat luôn tức thì với băng thông tối thiểu.
*   **Hỗ trợ kỹ thuật 24/7:** Trợ lý AI chẩn đoán bệnh lý thực vật qua hình ảnh, giúp người chơi bảo tồn cây tốt hơn.

## 2. Lý do chọn đề tài
Thị trường cây cảnh nghệ thuật có giá trị kinh tế rất lớn nhưng đang gặp rào cản về niềm tin khi giao dịch trực tuyến. Các hình ảnh 2D truyền thống không thể hiện được hết vẻ đẹp hình khối và thường gây ra tranh chấp sau khi nhận hàng. Bên cạnh đó, việc thiếu hụt dữ liệu về lịch sử chăm sóc và nguồn gốc khiến việc định giá các siêu phẩm Bonsai trở nên cảm tính. Việc ứng dụng 3D, Blockchain và AI là giải pháp tất yếu để chuẩn hóa và nâng tầm ngành thương mại sinh vật cảnh nghệ thuật.

## 3. Chức năng sản phẩm

### 3.1. Chức năng hiện tại (Giai đoạn 1)
#### Chức năng 1: Trình diễn tác phẩm Bonsai 3D & AR
*   **Mô tả:** Tích hợp WebGL render mô hình 3D (.glb/.gltf) của cây cảnh. Hỗ trợ xem AR trên mobile để ướm thử cây vào không gian thực tế.
*   **Hướng dẫn:** Tại trang chi tiết, sử dụng chuột/tay để xoay 360 độ hoặc nhấn "View in AR" để đặt cây vào vườn thật.

#### Chức năng 2: Hộ chiếu thực vật số (Digital Plant Passport)
*   **Mô tả:** Cấp UUID định danh duy nhất cho mỗi cây, lưu trữ dòng thời gian (timeline) từ lúc phôi đến khi thành phẩm.
*   **Hướng dẫn:** Xem mục "Passport" để tra cứu nhật ký cắt tỉa, thay chậu và các giải thưởng của cây.

#### Chức năng 3: Diễn đàn nghệ nhân & Thư viện số
*   **Mô tả:** Không gian thảo luận kỹ thuật Bonsai và kho tài liệu hướng dẫn chăm sóc chuyên sâu cho từng chủng loại cây.
*   **Hướng dẫn:** Truy cập "Cộng đồng" để đăng bài thảo luận hoặc "Thư viện" để tra cứu cách bón phân, tưới nước.

#### Chức năng 4: Hệ thống Chat nhị phân thời gian thực
*   **Mô tả:** Nhắn tin trực tiếp giữa người mua và nhà vườn, sử dụng Protobuf để nén dữ liệu tin nhắn siêu nhẹ.
*   **Hướng dẫn:** Nhấn "Chat với nhà vườn" để nhận tư vấn trực tiếp về tình trạng cây.

### 3.2. Chức năng phát triển vòng 2 (Nâng cao)
*   **Trợ lý AI chuyên gia (AI Doctor):** Phân tích ảnh lá cây bị bệnh qua Gemini Vision để chẩn đoán và đề xuất thuốc chữa.
*   **Sàn đấu giá hiệu năng cao:** Tổ chức các phiên đấu giá siêu phẩm Bonsai với thời gian thực chính xác đến mili-giây qua JakartaEE.
*   **Thanh toán Escrow & Blockchain:** Giữ tiền đảm bảo và đúc NFT (NFT Passport) để bảo chứng quyền sở hữu tác phẩm tiền tỷ.
*   **IoT Integration:** Kết nối cảm biến độ ẩm/ánh sáng tại chậu cây để gửi cảnh báo chăm sóc về điện thoại người dùng.

## 4. Công nghệ áp dụng

### 4.1. Công nghệ phần mềm (Software Stack)
*   **Core Backend:** **Laravel 12 (PHP 8.2)** quản lý Ecommerce nghiệp vụ và bảo mật API.
*   **Real-time Engine:** **JakartaEE (Java)** xử lý luồng tin nhắn và đấu giá hiệu năng cao.
*   **Data Protocol:** **Google Protocol Buffers (Protobuf)** mã hóa dữ liệu nhị phân siêu nhanh.
*   **Frontend:** **Vue 3 (Composition API)** kết hợp **Inertia.js** và **TailwindCSS 4**.
*   **Đồ họa 3D:** **Three.js** và **Draco Compression** tối ưu hóa mesh mô hình.

### 4.2. Công nghệ tích hợp & Hệ hạ tầng
*   **Database:** **MySQL** (Dữ liệu quan hệ) và **Redis Pub/Sub** (Phân phối tin nhắn real-time).
*   **Vận chuyển:** API **Giao Hàng Nhanh (GHN)** tính toán cước vận tải sinh vật sống đặc thù.
*   **AI Engine:** **Gemini Pro Vision** hỗ trợ chẩn đoán bệnh lý cây trồng.

### 4.3. Công nghệ 3D chuyên sâu (3D Tech Stack)
Hệ thống ứng dụng các công nghệ đồ họa tiên tiến nhất để đảm bảo trải nghiệm trực quan hóa Bonsai mượt mà trên nền tảng Web:
*   **Three.js & TresJS:** Sử dụng Core Engine **Three.js** (WebGL) để render vật liệu, ánh sáng và đổ bóng chân thực. **TresJS** đóng vai trò là cầu nối giúp quản lý các đối tượng 3D dưới dạng Vue Components, đảm bảo sự đồng bộ dữ liệu giữa UI và mô hình 3D.
*   **Chuẩn dữ liệu GLTF/GLB:** Sử dụng định dạng nhị phân tối ưu cho Web, giúp lưu trữ trọn vẹn lưới (mesh), vật liệu và cấu trúc xương của cây cảnh trong một tệp duy nhất.
*   **Google Draco Compression:** Áp dụng thuật toán nén lưới hình học mạnh mẽ. Các mô hình 3D được script Node.js nén Draco giúp giảm dung lượng từ **70% - 90%** mà không làm suy giảm chất lượng hiển thị.
*   **WebXR & @google/model-viewer:** Hỗ trợ công nghệ Thực tế tăng cường (AR). Người dùng có thể sử dụng camera điện thoại để đặt thử cây Bonsai ảo vào không gian thật với tỉ lệ kích thước 1:1.
*   **Photogrammetry Integration:** Quy trình số hóa cho phép chuyển đổi hàng trăm ảnh chụp thực tế của cây Bonsai thật thành mô hình 3D kỹ thuật số, đảm bảo tính duy nhất và xác thực cho từng tác phẩm trên sàn.

### 4.4. Ưu điểm vượt trội
1.  **Tốc độ:** Protobuf giúp giảm 70% dung lượng gói tin so với JSON, đảm bảo chat không trễ.
2.  **Trực quan:** Draco nén file 3D giúp tải các cây cảnh phức tạp cực nhanh trên mạng di động.
3.  **Bền vững:** Kiến trúc Modular giúp hệ thống dễ dàng mở rộng thêm các module AI hoặc IoT sau này.

## 5. Kiến trúc hệ thống

### 5.1. Sơ đồ kiến trúc tổng quan
Sử dụng mô hình **Hybrid Architecture**:
*   **Laravel Modular Monolith:** Xử lý luồng mua bán, định danh và tài liệu.
*   **JakartaEE Distributed Service:** Xử lý luồng Chat/Auction qua Websocket và Redis.

### 5.2. Các luồng dữ liệu (Data Flow)
*   **Luồng đơn hàng:** User -> Laravel -> GHN API -> MySQL -> Notification.
*   **Luồng Real-time:** Client -> JakartaEE (Protobuf) -> Redis Pub/Sub -> Toàn bộ cụm Server.

### 5.3. Bảo mật và hiệu suất
*   **Xác thực:** JWT dùng chung giữa Laravel và JakartaEE để đảm bảo tính nhất quán của User Session.
*   **Hiệu suất:** Lazy loading cho asset 3D và Caching tầng dữ liệu qua Redis.

## 6. Phương hướng phát triển
*   **Ngắn hạn:** Hoàn thiện tích hợp AI Doctor và hệ thống đấu giá.
*   **Dài hạn:** Triển khai NFT Passport trên mạng lưới Blockchain chính thức và tích hợp Mobile App AR Native.

## 7. Công cụ hỗ trợ
*   **GitHub:** Quản lý mã nguồn.
*   **Docker:** Môi trường phát triển nhất quán.
*   **Postman:** Kiểm thử và tài liệu hóa hệ thống API nhị phân.

## 8. Mô tả giao diện & Demo
*   **Giao diện:** Thiết kế theo phong cách tối giản (Zen), làm nổi bật các mô hình 3D Bonsai.
*   **Demo:** [Link GitHub của dự án]

## 9. Tài liệu tham khảo
*   Tài liệu Laravel 12.x, JakartaEE 10, Protobuf Guide, Three.js Documentation.
*   Các giáo trình chuyên ngành về kỹ thuật Bonsai và thương mại điện tử hiện đại.

## 10. Lời cảm ơn
Lời tri ân sâu sắc nhất tới Giảng viên hướng dẫn và các thầy cô khoa Công nghệ thông tin đã hỗ trợ em hoàn thành khóa luận này. Xin cảm ơn gia đình và bạn bè đã đồng hành trong suốt quá trình nghiên cứu.

---
*Hà Nội, Ngày 09 tháng 06 năm 2026*
