# Đánh giá mức độ hoàn thiện chức năng — AgriVerse

> Ngày đánh giá: 2026-08-13
> Phạm vi: Đối chiếu **18 chức năng yêu cầu** với hiện trạng codebase (`app/Modules/AgriVerse`, `resources/js`).

## Tổng kết nhanh

| # | Chức năng | Trạng thái | Ghi chú |
|---|-----------|-----------|---------|
| 1 | Trình diễn Bonsai 3D & AR | ✅ Có | WebGL + WebXR (AR) |
| 2 | Hộ chiếu thực vật số | ✅ Có | Timeline + sang tên đổi chủ |
| 3 | Diễn đàn cộng đồng | ✅ Có | Đa phương tiện, comment, like |
| 4 | Chat thời gian thực | ✅ Có | WebSocket (Echo/Reverb) |
| 5 | Knowledge Base | ✅ Có | Bài viết + chăm sóc theo loại |
| 6 | Đặt hàng & vận chuyển GHTK | ✅ Có | Tự tính cước, call API |
| 7 | Multi-vendor Marketplace | ✅ Có | Đa nhà vườn, seller dashboard |
| 8 | Vườn cá nhân (My Garden) | ✅ Có | Vùng, zone, nhật ký chăm sóc |
| 9 | Interactive Quiz | ⚠️ Bộ phận | Gợi ý hiện còn mang tính tĩnh |
| 10 | So sánh sản phẩm | ✅ Có | Side-by-side, localStorage |
| 11 | Affiliate | ✅ Có | Referral link, hoa hồng |
| 12 | Thông báo thời gian thực | ✅ Có | WebSocket + Push notification |
| 13 | Xác thực 2FA | ✅ Có | TOTP + recovery codes |
| 14 | Theo dõi vận chuyển | ✅ Có | Mã vận đơn, hành trình |
| 15 | Hợp đồng điện tử | ✅ Có | Tự tạo từ đơn hàng, ký số |
| 16 | Hoàn tiền | ✅ Có | Yêu cầu + phê duyệt |
| 17 | Đánh giá sản phẩm | ✅ Có | Sao + text/hình ảnh |
| 18 | Banner & SEO | ✅ Có | Quản trị banner + meta tự sinh |

**Kết luận:** 16/18 chức năng hoàn chỉnh, 1 bộ phận (Quiz), 18/18 hiện diện trong codebase.

---

## Chi tiết từng chức năng

### 1. Trình diễn Bonsai 3D & AR — ✅ Có
- **Trình xem 3D:** `Product3DViewer.vue`, `ThreeScene.vue`, composables `useGLTFLoader.js` / `useThreeScene.js` (WebGL).
- **Định dạng .glb/.gltf:** hỗ trợ tải model GLTF.
- **AR:** `ARViewer.vue` dùng `camera-controls` (WebXR), có kiểm tra hỗ trợ thiết bị (`checkARSupport`), cảnh báo nếu trình duyệt không hỗ trợ.
- **Backend:** `ThreeDAsset` model + `Api/ThreeDAssetController`, field `model_3d_url` trong `Product` (append).
- **Góc trống:** chỉ chạy tốt trên thiết bị hỗ trợ WebXR (Android Chrome / iOS Safari); không có fallback model.

### 2. Hộ chiếu thực vật số — ✅ Có
- `DigitalPassportLog` model + `Api/DigitalPassportController` & `DigitalPassportLogController` (update/destroy/ phân quyền policy).
- **Timeline lịch sử:** log ươm hạt, tạo dáng, ra hoa/quả, giải thưởng.
- **Sang tên đổi chủ:** `OwnershipHistory` model; `Specimen` model gắn cây thực tế.
- **Mã định danh:** dữ liệu theo từng specimen/product.

### 3. Diễn đàn cộng đồng (Green Community Forum) — ✅ Có
- `ForumPost`, `ForumComment`, `ForumLike`, `ForumCategory` models.
- Trang: `Marketplace/Forum/{Index,Create,Show}.vue`; admin quản lý `Admin/Forum` + `ForumCategories`.
- **Đăng bài đa phương tiện:** upload ảnh gallery (`ForumImageBrowser.vue`, `ForumUploadController@listImages`), cột `images` JSON.
- **Upvote:** `ForumLike` (like) có sẵn.
- Seeder `ForumCategorySeeder` tạo các chuyên mục (Kỹ thuật, Khoe cây, Hỏi đáp bệnh lý...).

### 4. Hệ thống Chat thời gian thực — ✅ Có
- `ChatConversation`, `ChatMessage`, `ChatGroup`, `ChatGroupMember` models.
- `ChatService` + các Event (`OrderCreated`, `OrderConfirmed`, `OrderCancelled`, ...) dùng **WebSocket/Echo**.
- UI: `ChatBox.vue`, `ChatPanel.vue`, `MessageArea.vue` — hỗ trợ text, hình ảnh.
- `Api/ChatController` quản lý hội thoại.

### 5. Thư viện Kỹ thuật & Chăm sóc — ✅ Có
- `JournalArticle`, `CareGuide`, `TreeSpecies` models.
- Trang: `Marketplace/Journal/{Index,Show}.vue` (`/bai-viet`).
- Command `ScrapeBonsaiArticles` đã seed 20 bài viết (8 species + 11 basics).
- Hướng dẫn chăm sóc theo từng chủng loại cây.

### 6. Đặt hàng & Vận chuyển tự động (GHTK) — ✅ Có
- Giỏ hàng & checkout: `CartController`, `CheckoutController`, `Cart/index.vue`, `Checkout/{Index,Success}.vue`.
- **Tính cước tự động:** `GHTKService@calculateFee` — gọi API real, có fallback tự tính (fee, insurance, estimated delivery, phí phức tạp theo vùng).
- `GHTKAddressController`, `GHNAddressController`, `SellerShippingController` (shipper seller).
- Modules GHTK: `giao_hang_tiet_kiem` — `ShipmentTracking` liên quan.

### 7. Sàn giao dịch đa người bán — ✅ Có
- `Store` model + `Shop/StoreController`, `SellerStoreController`, `SellerController`, `Admin/StoreController`.
- **Seller dashboard:** `Seller/Dashboard.vue`, quản lý `Seller/Products`, `Seller/Orders`, `Seller/Reviews`, `Seller/Store/Edit`.
- **Đăng ký nhà vườn:** `Seller/Register.vue`, `SellerVerification` model.
- **Admin trung tâm:** duyệt seller/store, quản kho.

### 8. Vườn cá nhân (My Garden) — ✅ Có
- `Garden`, `GardenZone`, `GardenPlant` models + `GardenController`, `GardenService`.
- `Marketplace/Garden/Index.vue` — tạo vườn ảo, chia vùng địa lý, gắn cây vào khu vực, nhật ký chăm sóc, theo dõi phát triển.

### 9. Tìm mẫu cây phù hợp (Interactive Quiz) — ⚠️ Bộ phận
- `QuizQuestion` model; route `cay-tim-nguoi` → `PageController@quiz`; `Marketplace/Quiz/Index.vue` (nhiều bước, grid/image choices).
- **Hạn chế:** dữ liệu gợi ý kết quả trong template vẫn còn **tĩnh/hardcode** (VD: "Monstera Thai Constellation"); chưa có thuật toán scoring động mapping câu trả lời → chủng loại Bonsai.
- **Đề xuất:** nối kết quả quiz với `TreeSpecies`/`Product` bằng cơ chế chấm điểm phía client hoặc API.

### 10. So sánh sản phẩm — ✅ Có
- `Compare/Index.vue` (bảng side-by-side), `CompareBar.vue`; composable `useCompare.js` (localStorage).
- Backend: `ProductController@compare` map đầy đủ thuộc tính (giá, độ tuổi, kích thước, thông số từ Hộ chiếu, đánh giá, thương hiệu, 6 nhóm thông tin).
- Route `/so-sanh`.

### 11. Affiliate — ✅ Có
- `AffiliateController`, `AffiliateService`, `Referral`, `Commission` models.
- `Affiliate/Register.vue` + `Affiliate/Dashboard.vue` — referral link copy, thống kê lượt truy cập, tỷ lệ chuyển đổi, hoa hồng.

### 12. Thông báo thời gian thực — ✅ Có
- `Api/NotificationController`, `Notifications/Index.vue` (click-to-navigate đến đơn hàng).
- `PushNotificationService`, `DeviceToken` model, `Api/PushNotificationController` — push trên thiết bị; Events WebSocket cho sự kiện đơn hàng.

### 13. Xác thực 2FA — ✅ Có
- `TwoFactorService`, `TwoFactorController`.
- `Settings/TwoFactor.vue` — TOTP Google Authenticator, recovery codes, regenerate codes (đã sửa 3 bug: showRecoveryCodes, recoveryCodesList, inline regen).

### 14. Theo dõi vận chuyển — ✅ Có
- `OrderController@tracking` + `Marketplace/Tracking/Index.vue`.
- Hiển thị hành trình, mã vận đơn, ngày giao dự kiến; dropdown đơn hàng gần đây (`recentOrders`).

### 15. Hợp đồng điện tử — ✅ Có
- `Contract` model, `Shop/ContractController`, `Admin/ContractController`.
- `Contracts/Show.vue` — timeline status, ký số, thông tin đơn hàng liên quan, in, terms, badge trạng thái (active/signed/pending/cancelled/expired).

### 16. Hoàn tiền — ✅ Có
- `Refund` model, `Admin/RefundController` + `Refunds/{Index,Show}.vue`; luồng yêu cầu → người bán/admin xem xét → phê duyệt minh bạch.

### 17. Đánh giá sản phẩm — ✅ Có
- `Review` model, `Api/ReviewController`, `Shop/SellerReviewController`.
- `Seller/Reviews/Index.vue` — đánh giá bằng sao + văn bản/hình ảnh sau khi đơn hoàn tất.

### 18. Banner & SEO — ✅ Có
- **Banner:** `Banner` model + `Admin/BannerController` + `Admin/Banners/Index.vue` (quản trị banner trang chủ).
- **SEO:** `SeoService` (`productMeta`, `categoryMeta`, `storeMeta`...) tự sinh title/description/meta; route `seo.php`.

---

## Khuyến nghị ưu tiên
1. **Quiz (Chức năng 9):** hoàn thiện thuật toán gợi ý động thay cho kết quả hardcode.
2. **3D/AR (Chức năng 1):** thêm fallback cho thiết bị không hỗ trợ WebXR.
3. **Diễn đàn (Chức năng 3):** nếu yêu cầu đúng "upvote/điểm" thay cho "like" thì bổ sung cơ chế đếm upvote.
4. **GHTK (Chức năng 6):** rà soát cấu hình API token production trước khi launch.