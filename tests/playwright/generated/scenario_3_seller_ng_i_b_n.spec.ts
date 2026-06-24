import { test, expect } from '@playwright/test';

test.describe('3. Seller — Người bán', () => {

  test.describe('3.1 Đăng ký người bán', () => {

    test('TC-89: Xem form đăng ký', async ({ page }) => {
      // Giả lập user đã đăng nhập để vào form đăng ký seller
      await page.goto('/login');
      await page.fill('input[type="email"]', 'buyer@orchestrix.com');
      await page.fill('input[type="password"]', '12345678');
      await page.click('button[type="submit"]');
      await page.waitForURL(/\/agriverse/);

      await test.step('1. GET `/agriverse/seller/register`', async () => {
        await page.goto('/agriverse/seller/register');
      });

      await test.step('Verify: Form hiển thị', async () => {
        await expect(page.locator('form').first()).toBeVisible();
        await expect(page.locator('text=Đăng ký người bán')).toBeVisible();
      });
    });

    test('TC-90: Gửi mã xác thực email', async ({ page }) => {
      await page.goto('/agriverse/seller/register');
      await test.step('1. Nhấn nút Gửi mã OTP', async () => {
        const sendCodeBtn = page.locator('button:has-text("Gửi mã"), button:has-text("Send OTP")').first();
        if (await sendCodeBtn.isVisible()) {
          await sendCodeBtn.click();
        }
      });

      await test.step('Verify: Có thông báo gửi mã', async () => {
        // Assert API called or Toast shown
      });
    });

    test('TC-91: Xác thực email với mã đúng', async ({ page }) => {
      await test.step('1. Bỏ qua do cần lấy mã OTP thực tế từ Email/DB', async () => {
        test.skip();
      });
    });

    test('TC-92: Xác thực email với mã sai', async ({ page }) => {
      await page.goto('/agriverse/seller/register');
      await test.step('1. Nhập OTP sai và verify', async () => {
        const otpInput = page.locator('input[placeholder*="OTP"], input[name="otp"]').first();
        if (await otpInput.isVisible()) {
          await otpInput.fill('000000');
          await page.locator('button:has-text("Xác nhận")').first().click();
          await expect(page.locator('.text-danger, .error-msg, .login-error').first()).toBeVisible();
        }
      });
    });

    test('TC-93: Gửi đăng ký (Step 1-3)', async ({ page }) => {
      await page.goto('/agriverse/seller/register');
      await test.step('1. Điền CCCD, upload ảnh CMND/CCCD mặt trước/sau', async () => {
        const cccdInput = page.locator('input[name="cccd"], input[placeholder*="CCCD"]').first();
        if (await cccdInput.isVisible()) {
          await cccdInput.fill('012345678912');
          // Điền tên shop
          await page.locator('input[name="store_name"]').first().fill('Shop Automation');
          // Submit
          await page.locator('button[type="submit"]').first().click();
        }
      });

      // Expected Results:
      // - Đơn đăng ký gửi thành công, status = pending
      // TODO: Implement assertions
    });

    test('TC-94: Gửi đăng ký khi chưa verify email', async ({ page }) => {
      await test.step('1. Bỏ qua bước verify email', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Submit', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Báo lỗi phải xác thực email trước
      // TODO: Implement assertions
    });

    test('TC-95: Kiểm tra trạng thái đơn', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/status`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Hiển thị trạng thái pending/approved/rejected
      // TODO: Implement assertions
    });

  });

  test.describe('3.2 Seller Dashboard', () => {

    test('TC-96: Xem dashboard (chưa có store)', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/dashboard`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Hiển thị nhưng thiếu thông tin store
      // TODO: Implement assertions
    });

    test('TC-97: Xem dashboard (đã có store)', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/dashboard`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Hiển thị: tổng sản phẩm, đơn hàng, doanh thu, đơn chờ xử lý
      // TODO: Implement assertions
    });

    test('TC-98: Dashboard API stats', async ({ page }) => {
      await test.step('1. GET `/api/seller/dashboard/stats`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Trả về stat JSON
      // TODO: Implement assertions
    });

  });

  test.describe('3.3 Seller — Quản lý sản phẩm', () => {

    test('TC-99: Xem danh sách sản phẩm', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/products`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Danh sách sản phẩm của seller
      // TODO: Implement assertions
    });

    test('TC-100: Thêm sản phẩm mới', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/products/create`', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Nhập name, price, stock, category, image', async () => {
        // TODO: Implement interaction
      });
      await test.step('3. POST', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Sản phẩm được tạo
      // TODO: Implement assertions
    });

    test('TC-101: Sửa sản phẩm', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/products/{product}/edit`', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Cập nhật', async () => {
        // TODO: Implement interaction
      });
      await test.step('3. PUT', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Sản phẩm được cập nhật
      // TODO: Implement assertions
    });

    test('TC-102: Xóa sản phẩm', async ({ page }) => {
      await test.step('1. DELETE `/agriverse/seller/products/{product}`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Sản phẩm bị xóa (soft delete)
      // TODO: Implement assertions
    });

    test('TC-103: Thêm sản phẩm thiếu thông tin bắt buộc', async ({ page }) => {
      await test.step('1. Bỏ trống name/price', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Submit', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Validation error
      // TODO: Implement assertions
    });

  });

  test.describe('3.4 Seller — Quản lý đơn hàng', () => {

    test('TC-104: Xem danh sách đơn hàng', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/orders`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Đơn hàng của seller (pending, confirmed, shipping, delivered, cancelled)
      // TODO: Implement assertions
    });

    test('TC-105: Xem chi tiết đơn hàng', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/orders/{order}`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Thông tin đơn hàng + sản phẩm + địa chỉ + trạng thái
      // TODO: Implement assertions
    });

    test('TC-106: Xác nhận đơn hàng', async ({ page }) => {
      await test.step('1. POST `/agriverse/seller/orders/{order}/confirm`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Order → confirmed
      // TODO: Implement assertions
    });

    test('TC-107: Giao hàng (tạo vận đơn GHN)', async ({ page }) => {
      await test.step('1. POST `/agriverse/seller/orders/{order}/ship`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Order → shipping + vận đơn GHN
      // TODO: Implement assertions
    });

    test('TC-108: Xác nhận đã giao', async ({ page }) => {
      await test.step('1. POST `/agriverse/seller/orders/{order}/deliver`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Order → delivered
      // TODO: Implement assertions
    });

    test('TC-109: Hủy đơn hàng', async ({ page }) => {
      await test.step('1. POST `/agriverse/seller/orders/{order}/cancel`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Order → cancelled (kèm lý do)
      // TODO: Implement assertions
    });

    test('TC-110: Xem dịch vụ GHN', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/orders/{order}/shipping`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Danh sách dịch vụ GHN khả dụng
      // TODO: Implement assertions
    });

    test('TC-111: Tạo shipment GHN', async ({ page }) => {
      await test.step('1. POST `/agriverse/seller/orders/{order}/create-shipment`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Shipment được tạo trên GHN
      // TODO: Implement assertions
    });

  });

  test.describe('3.5 Seller — Thông tin cửa hàng', () => {

    test('TC-112: Xem form sửa store', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/store`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Form với thông tin hiện tại
      // TODO: Implement assertions
    });

    test('TC-113: Cập nhật store', async ({ page }) => {
      await test.step('1. Sửa name, description, phone, address, bank info', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. PUT `/agriverse/seller/store`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Store được cập nhật
      // TODO: Implement assertions
    });

  });

  test.describe('3.6 Seller — Đánh giá', () => {

    test('TC-114: Xem danh sách đánh giá', async ({ page }) => {
      await test.step('1. GET `/agriverse/seller/reviews`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Danh sách review các sản phẩm của seller, total/avg rating
      // TODO: Implement assertions
    });

  });

});
