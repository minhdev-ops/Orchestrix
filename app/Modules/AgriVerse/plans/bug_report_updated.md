# 📊 Báo cáo Kiểm thử Tự động (Cập nhật mới nhất)
*Tiến trình thực thi: Đã quét lại toàn bộ luồng mua hàng (End-to-End Buyer Flow).*

Tuyệt vời! Sau khi chạy lại bộ automation test, mình thấy **rất nhiều lỗi CRITICAL đã được khắc phục**. Hệ thống hiện tại đã ổn định hơn rất nhiều. Dưới đây là báo cáo cập nhật:

---

## ✅ Các lỗi ĐÃ ĐƯỢC FIX (Thành công)
1. **[Backend] Lỗi Crash API Cart (Add trùng sản phẩm):** Đã sửa thành công! Khi ấn thêm sản phẩm đã có trong giỏ, hệ thống đã tự động cộng dồn số lượng (Quantity: 2) thay vì văng lỗi 500 `Integrity constraint violation` như trước.
2. **[Backend] Crash trang Chi tiết Sản phẩm:** Đã sửa! Trang chi tiết sản phẩm (VD: *Bonsai đa lộc dáng huyền*) load thành công, hiển thị giá và nút bấm trơn tru, không còn văng lỗi 500.
3. **[Frontend] Lỗi hiển thị ngày tháng Đơn hàng:** Đã sửa! Format ISO xấu xí đã được chuyển thành định dạng chuẩn `01:31 10/06/2026`.
4. **[Frontend] Lỗi Đè Empty State Đơn hàng:** Chữ "Chưa có đơn hàng nào" không còn bị đè lên danh sách đơn hàng hiện tại nữa. Chức năng "Hủy đơn hàng" và điền lý do hoạt động tốt.

---

## ⚠️ Các lỗi VẪN CÒN TỒN TẠI (Cần xử lý)

### 1. Trang Sản phẩm & Chi tiết (Products)
- 🟡 **Lỗi UI (Ảnh Sản phẩm):** Cả ở danh sách lẫn trang chi tiết, sản phẩm vẫn **không load được hình ảnh thực tế**. Giao diện chỉ hiển thị chữ cái đầu tiên khổng lồ làm placeholder (Ví dụ: Chữ 'B' xám to đùng cho Bonsai, chữ 'X' cho Xương rồng).

### 2. Giỏ hàng & Đăng nhập (Cart Sync)
- 🟠 **Lỗi Logic (Mất giỏ hàng khi Login):** Nếu người dùng (Guest) thêm sản phẩm vào giỏ (VD: *Xương rồng sa mạc vàng*), sau đó thực hiện đăng nhập, thì giỏ hàng cũ bị **xóa sạch / không được merge** vào giỏ hàng của user. Sản phẩm biến mất hoàn toàn.

### 3. Thanh toán (Checkout)
- 🔴 **CRITICAL (Dữ liệu Hành chính rỗng):** Trong Form `+ Địa chỉ mới`, các Dropdown `Tỉnh/Thành`, `Quận/Huyện`, `Phường/Xã` vẫn chưa load được danh sách (chỉ hiện mỗi dòng "Chọn"). Điều này chặn đứng quá trình thanh toán của người mua.

### 4. Dữ liệu rác (Seed Data)
- 🟡 **Lỗi Data (Đơn hàng):** Trong danh sách "Đơn hàng của tôi", có một số đơn hàng cũ (từ Seeder) bị lỗi: Không có tên sản phẩm, không có ảnh, và tổng tiền hiện `0₫`.

---

### 💡 Đề xuất Action tiếp theo:
1. **Fix API Tỉnh/Thành:** Cần ưu tiên số 1 để user có thể nhập địa chỉ và Đặt hàng. Bạn muốn mình kiểm tra file `AddressController` hoặc component `Checkout/Index.vue` không?
2. **Kiểm tra Cart Sync:** Thêm logic merge giỏ hàng (từ session sang database) sau khi user Login thành công.
3. **Bổ sung Ảnh sản phẩm:** Cập nhật lại factory/seeder hoặc sửa component hiển thị ảnh (thay thế khối placeholder hiện tại).
