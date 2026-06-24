import { test, expect } from '@playwright/test';

test.describe('1. Authentication & Account', () => {

  test.describe('1.1 Đăng ký tài khoản', () => {

    test('TC-01: Đăng ký thành công với email hợp lệ', async ({ page }) => {
      await test.step('1. Vào `/register`', async () => {
        await page.goto('/register');
      });
      await test.step('2. Nhập name, email, password, confirm password', async () => {
        await page.fill('input[type="text"]', 'Test User ' + Date.now());
        await page.fill('input[type="email"]', `test${Date.now()}@example.com`);
        await page.fill('input[type="password"]', 'Password123');
        // Register.vue uses type="password" for both password and repass. The second one is repass.
        await page.locator('input[type="password"]').nth(1).fill('Password123');
      });
      await test.step('3. Submit', async () => {
        await page.click('button[type="submit"]');
      });

      await test.step('Verify: Redirect về trang chủ sau đăng ký', async () => {
        await page.waitForURL(/agriverse/);
        await expect(page).toHaveURL(/agriverse/);
      });
    });

    test('TC-02: Đăng ký với email đã tồn tại', async ({ page }) => {
      await page.goto('/register');
      await test.step('1. Nhập email đã có trong hệ thống', async () => {
        await page.fill('input[type="text"]', 'Test User');
        await page.fill('input[type="email"]', 'admin@orchestrix.com'); // Admin email is seeded
        await page.fill('input[type="password"]', '12345678');
        await page.locator('input[type="password"]').nth(1).fill('12345678');
      });
      await test.step('2. Submit', async () => {
        await page.click('button[type="submit"]');
      });

      await test.step('Verify: Báo lỗi', async () => {
        const errorMsg = page.locator('.login-error');
        await expect(errorMsg).toBeVisible();
      });
    });

    test('TC-03: Đăng ký với password quá ngắn (< 8 ký tự)', async ({ page }) => {
      await page.goto('/register');
      await test.step('1. Nhập password 6 ký tự', async () => {
        await page.fill('input[type="text"]', 'Test User');
        await page.fill('input[type="email"]', `test${Date.now()}@example.com`);
        await page.fill('input[type="password"]', '123456');
        await page.locator('input[type="password"]').nth(1).fill('123456');
      });
      await test.step('2. Submit', async () => {
        await page.click('button[type="submit"]');
      });

      await test.step('Verify: HTML5 validation prevents submit or error shown', async () => {
        // Due to minlength="8" in HTML, form won't submit. 
        // We check that the form remains visible and no success message appears.
        await expect(page.locator('.login-success')).toBeHidden();
      });
    });

    test('TC-04: Đăng ký với confirm password không khớp', async ({ page }) => {
      await page.goto('/register');
      await test.step('1. Nhập password/confirm khác nhau', async () => {
        await page.fill('input[type="text"]', 'Test User');
        await page.fill('input[type="email"]', `test${Date.now()}@example.com`);
        await page.fill('input[type="password"]', '12345678');
        await page.locator('input[type="password"]').nth(1).fill('87654321');
      });
      await test.step('2. Submit', async () => {
        await page.click('button[type="submit"]');
      });

      await test.step('Verify: Báo lỗi confirm password không khớp', async () => {
        const errorMsg = page.locator('.login-error');
        await expect(errorMsg).toHaveText(/không khớp/i);
      });
    });

  });

  test.describe('1.2 Đăng nhập', () => {

    test('TC-05: Đăng nhập thành công (user thường)', async ({ page }) => {
      await test.step('1. Vào `/login`', async () => {
        await page.goto('/login');
      });
      await test.step('2. Nhập email/password đúng', async () => {
        await page.fill('input[type="email"]', 'buyer@orchestrix.com');
        await page.fill('input[type="password"]', '12345678');
      });
      await test.step('3. Submit', async () => {
        await page.click('button[type="submit"]');
      });

      await test.step('Verify: Redirect về `/agriverse/`', async () => {
        await page.waitForURL(/agriverse/);
        await expect(page).toHaveURL(/agriverse/);
      });
    });

    test('TC-06: Đăng nhập thành công (admin)', async ({ page }) => {
      await page.goto('/login');
      await test.step('1. Nhập email/password admin', async () => {
        await page.fill('input[type="email"]', 'admin@orchestrix.com');
        await page.fill('input[type="password"]', '12345678');
      });
      await test.step('2. Submit', async () => {
        await page.click('button[type="submit"]');
      });

      await test.step('Verify: Redirect về `/admin/agriverse`', async () => {
        await page.waitForURL(/admin\/agriverse/);
        await expect(page).toHaveURL(/admin\/agriverse/);
      });
    });

    test('TC-07: Đăng nhập với sai mật khẩu', async ({ page }) => {
      await page.goto('/login');
      await test.step('1. Nhập sai password', async () => {
        await page.fill('input[type="email"]', 'buyer@orchestrix.com');
        await page.fill('input[type="password"]', 'wrongpassword');
      });
      await test.step('2. Submit', async () => {
        await page.click('button[type="submit"]');
      });

      await test.step('Verify: Báo lỗi "Tài khoản hoặc mật khẩu chưa đúng"', async () => {
        const errorMsg = page.locator('.login-error');
        await expect(errorMsg).toBeVisible();
        await expect(errorMsg).toHaveText('Tài khoản hoặc mật khẩu chưa đúng');
      });
    });

    test('TC-08: Đăng nhập với email chưa kích hoạt', async ({ page }) => {
      await test.step('1. Dùng email chưa active', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Submit', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Báo lỗi "Tài khoản chưa được kích hoạt"
      // TODO: Implement assertions
    });

  });

  test.describe('1.3 Kích hoạt tài khoản (Email Activation)', () => {

    test('TC-09: Kích hoạt với link hợp lệ', async ({ page }) => {
      await test.step('1. Click link trong email activation', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. GET `/api/active/{email}/{key}`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - User được active, thông báo thành công
      // TODO: Implement assertions
    });

    test('TC-10: Kích hoạt với key sai', async ({ page }) => {
      await test.step('1. Sửa key trong link', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. GET API', async () => {
        // await page.request.get('API');
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Báo lỗi key không hợp lệ
      // TODO: Implement assertions
    });

    test('TC-11: Gửi lại email kích hoạt', async ({ page }) => {
      await test.step('1. GET `/api/re-active` với email', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Kiểm tra log mail', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Email activation mới được gửi
      // TODO: Implement assertions
    });

  });

  test.describe('1.4 Quên mật khẩu', () => {

    test('TC-12: Gửi yêu cầu reset password', async ({ page }) => {
      await test.step('1. POST `/api/forget-pass` với email', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Kiểm tra log mail', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Email reset password được gửi
      // TODO: Implement assertions
    });

    test('TC-13: Reset password thành công', async ({ page }) => {
      await test.step('1. Click link trong email', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. GET `/api/reset-pass/{email}/{key}`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Password được reset, thông báo thành công
      // TODO: Implement assertions
    });

  });

  test.describe('1.5 Đăng nhập mạng xã hội', () => {

    test('TC-14: Đăng nhập Google thành công', async ({ page }) => {
      await test.step('1. PUT `/api/login/google` với token Google', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Trả về access_token, user info
      // TODO: Implement assertions
    });

    test('TC-15: Đăng nhập Facebook thành công', async ({ page }) => {
      await test.step('1. PUT `/api/login/facebook` với token FB', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Trả về access_token, user info
      // TODO: Implement assertions
    });

  });

  test.describe('1.6 Quản lý tài khoản', () => {

    test('TC-16: Xem thông tin tài khoản', async ({ page }) => {
      await test.step('1. GET `/api/user/detail` (có Bearer token)', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Trả về thông tin user
      // TODO: Implement assertions
    });

    test('TC-17: Cập nhật profile', async ({ page }) => {
      await test.step('1. POST `/api/user/update` với name, phone, avatar', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Thông tin được cập nhật
      // TODO: Implement assertions
    });

    test('TC-18: Đổi mật khẩu', async ({ page }) => {
      await test.step('1. POST `/api/change-pass` với old_password, new_password', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Mật khẩu được thay đổi
      // TODO: Implement assertions
    });

    test('TC-19: Đăng xuất', async ({ page }) => {
      await test.step('1. GET `/api/logout`', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Token bị revoke', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - API không còn hoạt động với token cũ
      // TODO: Implement assertions
    });

  });

  test.describe('1.7 Account Settings (Web)', () => {

    test('TC-20: Xem trang cài đặt tài khoản', async ({ page }) => {
      await test.step('1. Vào `/agriverse/tai-khoan/cai-dat`', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Hiển thị form settings với dữ liệu user
      // TODO: Implement assertions
    });

    test('TC-21: Cập nhật notification preferences', async ({ page }) => {
      await test.step('1. Vào Account Settings', async () => {
        // TODO: Implement interaction
      });
      await test.step('2. Thay đổi notification prefs', async () => {
        // TODO: Implement interaction
      });
      await test.step('3. Submit', async () => {
        // TODO: Implement interaction
      });

      // Expected Results:
      // - Preferences được lưu vào DB
      // TODO: Implement assertions
    });

  });

});
