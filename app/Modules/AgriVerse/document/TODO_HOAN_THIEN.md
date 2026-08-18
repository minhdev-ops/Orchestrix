# Danh sách việc cần làm để hoàn thiện — theo Bản thuyết minh IT-Challenge (Bảng B)

> Nguồn: `NguyenQuangMinh_23130193_ IT_Challenge Bang B.docx` (mục 3.1, 3.2, 6)
> Đối chiếu: hiện trạng codebase AgriVerse (2026-08-13)

## Tóm tắt trạng thái

| Nhóm | Số mục | Hoàn thành | Chưa có |
|------|-------|-----------|---------|
| Chức năng hiện tại (3.1) | 6 | 6 ✅ | 0 |
| Chức năng vòng 2 (3.2) | 4 | 1 ✅ | 3 ❌ |
| Định hướng GĐ2 (Payment/Escrow) | 2 | 1 ✅ | 1 ❌ |
| Định hướng GĐ3 (Mobile/NFT) | 2 | 0 | 2 ❌ |

**Kết luận:** Chức năng "hiện tại" đã đủ 6/6. Trong 4 chức năng vòng 2, đã có **1 (AI Plant Doctor)**; còn **3 chưa làm**: Đấu giá, Mô phỏng cắt tỉa 3D, NFT/Blockchain. Kèm theo còn thiếu **Escrow** và **Mobile App (Flutter)** theo định hướng phát triển.

---

## PHẦN A — CHỨC NĂNG HIỆN TẠI (§3.1): đã đủ 6/6

| # | Chức năng (theo thuyết minh) | Hiện trạng code | Ghi chú bổ sung cần làm |
|---|------------------------------|-----------------|--------------------------|
| 1 | Trình diễn Bonsai 3D & AR | ✅ Có | `Product3DViewer.vue`, `ThreeScene.vue`, `ARViewer.vue` (WebXR), `useGLTFLoader.js`, `ThreeDAsset` model. |
| 2 | Hộ chiếu thực vật số | ✅ Có | `DigitalPassportLog`, `OwnershipHistory`, `Specimen`, API `DigitalPassportController`. |
| 3 | Diễn đàn cộng đồng | ✅ Có | `ForumPost/Comment/Like/Category`, upload ảnh đa phương tiện, `ForumImageBrowser`. |
| 4 | Chat thời gian thực | ✅ Có | WebSocket (Reverb), gửi text/ảnh + **chia sẻ thẻ sản phẩm** (`ChatController` có `product_id`). |
| 5 | Knowledge Base | ✅ Có | `JournalArticle`, `CareGuide`, `TreeSpecies`, `/bai-viet`, command `ScrapeBonsaiArticles`. |
| 6 | Đặt hàng & Vận chuyển | ✅ Có | Cart/Checkout/Order; `GHTKService` + `GHNService` (cả 2 đơn vị), tự tính cước. |

> Việc còn lại của vòng này chỉ là **tinh chỉnh/chạy thật**, không cần phát triển mới.

---

## PHẦN B — CHỨC NĂNG VÒNG 2 (§3.2): cần làm 3/4

### 1. Trợ lý AI chuyên gia (AI-Powered Plant Doctor) — ✅ ĐÃ CÓ
- `AIPlantDoctorService` (Gemini `gemini-2.0-flash` / OpenAI `gpt-4o`), chẩn đoán ảnh + triệu chứng, đề xuất thuốc.
- UI: `/chan-doan` (`Diagnostic/Index.vue`), API `plant-doctor.*`, admin quản lý scan (`AiScanningController`).
- **Còn thiếu để đạt 100% thuyết minh:**
  - Auto-routing "tới Chuyên gia" khi AI không tự tin (chưa thấy luồng chuyển tiếp).
  - Cấu hình API key production (đang dùng config thử nghiệm).

### 2. Sàn đấu giá trực tuyến (Bonsai Auction) — ❌ CHƯA CÓ
Không tìm thấy model/controller/route nào về đấu giá.
**Cần làm:**
- [ ] Model `Auction` + `AuctionBid` (+ migration), quan hệ với `Product`.
- [ ] Controller + routes: tạo phiên đấu giá (seller), đặt giá (buyer), đếm ngược.
- [ ] Real-time đấu giá kiểu Anh qua WebSocket/Reverb (đã có sẵn hạ tầng chat).
- [ ] UI: trang đấu giá, danh sách đang diễn ra/sắp tới/kết thúc.
- [ ] Xử lý kết thúc: người thắng → tạo đơn hàng tự động.
- [ ] Admin: quản lý/duyệt phiên đấu giá (thêm vào `Admin`).

### 3. Mô phỏng Tạo tác 3D (Virtual Pruning Simulator) — ❌ CHƯA CÓ
Chưa có tương tác cắt tỉa trên mô hình 3D.
**Cần làm:**
- [ ] Trong `ThreeScene.vue`: thêm chế độ "pruning" (chọn nhánh → ẩn/cắt submesh).
- [ ] Bộ công cụ: chọn nhánh, undo/redo, xem trước trước-sau.
- [ ] Điểm nhánh phục vụ cắt: cần cấu trúc GLB có named nodes (hoặc tách sẵn nhánh từ pipeline Photogrammetry).
- [ ] Lưu trạng thái "bản tạo dáng đề xuất" gắn vào Hộ chiếu thực vật (timeline tạo dáng).

### 4. NFT Passport (Blockchain) — ❌ CHƯA CÓ
Chỉ xuất hiện trong tài liệu thiết kế, chưa có code.
**Cần làm:**
- [ ] Chọn chain/nền tảng (VD: Ethereum/Polygon, NFT.Storage/Pinata IPFS).
- [ ] Phát hành NFT từ `DigitalPassport`/`Specimen` (metadata JSON: UUID, ảnh 360°, timeline, sang tên đổi chủ).
- [ ] Ghi giao dịch sang tên (ownership transfer) lên chain; UI hiển thị hash giao dịch.
- [ ] Xác minh tính độc bản (chống làm giả) trên trang Hộ chiếu.

---

## PHẦN C — THEO ĐỊNH HƯỚNG PHÁT TRIỂN (§6) còn thiếu

### Giai đoạn 2: Payment Gateway + Escrow
- **Payment Gateway — ✅ CÓ SẴN:** `PaymentService` đã tích hợp **VNPay + MoMo** (create/verify), `Payment/Index.vue` + `Payment/Banking.vue`.
- **Escrow (Giữ tiền) — ❌ CHƯA CÓ:** `PaymentService` không có luồng giữ tiền.
  - [ ] Thêm trạng thái `escrow_hold` cho `Transaction`/`Order`.
  - [ ] Luồng: buyer thanh toán → tiền giữ → buyer xác nhận nhận hàng → giải phóng cho seller.
  - [ ] Tự động hoàn tiền nếu hết hạn/không xác nhận; kết hợp với module Refund.

### Giai đoạn 3: Mobile App + Blockchain
- **Mobile App (Flutter + camera LiDAR quét 3D) — ❌ CHƯA CÓ:** repo chỉ có web. Cần tạo app Flutter riêng hoặc PWA.
- **Blockchain NFT — ❌ (trùng mục 4 phần B).**

---

## PHẦN D — VIỆC PHI CHỨC NĂNG cần hoàn thiện

1. **GHTK/GHN production:** cấu hình token/key thật (đang fallback tự tính cước).
2. **VNPay/MoMo production:** điền `tmn_code`, `hash_secret`, `partner_code`, `secret_key` thật.
3. **AI Plant Doctor:** API key production (Gemini/OpenAI).
4. **reCAPTCHA:** restore key thật (đang dùng test keys).
5. **WildFly Redis:** sửa source JakartaEE `localhost:6379` → `redis:6379`.
6. **`.gitignore`:** bỏ `docker-compose.yml`, `Dockerfile`, `docker/nginx/conf.d/app.conf` để CI/CD deploy.
7. **WebXR AR:** thêm fallback cho thiết bị không hỗ trợ AR.
8. **Quiz (`cay-tim-nguoi`):** thay kết quả hardcode bằng thuật toán gợi ý động.

---

## ƯU TIÊN ĐỀ XUẤT (theo thuyết minh 3.2)

| Ưu tiên | Việc | Lý do |
|--------|------|-------|
| 🔴 Cao | Sàn đấu giá (Auction) | Là 1/4 chức năng vòng 2 bắt buộc, dùng được sẵn hạ tầng WebSocket + JakartaEE. |
| 🔴 Cao | Mô phỏng cắt tỉa 3D | 1/4 chức năng vòng 2, tận dụng sẵn `ThreeScene.vue`. |
| 🟠 Trung | Escrow | Bắt buộc trong định hướng GĐ2. |
| 🟠 Trung | NFT Passport | 1/4 chức năng vòng 2 (khó nhất, đánh giá cao nhất). |
| 🟡 Thấp | Mobile App Flutter | Định hướng GĐ3 (dài hạn). |
| 🟢 Luôn | Hoàn thiện key production | Để demo thật sự chạy được. |
