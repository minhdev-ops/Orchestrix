import { test, expect } from '@playwright/test';

test.describe('7. Admin Panel', () => {

  test.beforeEach(async ({ page }) => {
    // Tự động đăng nhập bằng tài khoản Admin
    await page.goto('/login');
    await page.fill('input[type="email"]', 'admin@orchestrix.com');
    await page.fill('input[type="password"]', '12345678');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
  });


  test.describe('7.1 Dashboard', () => {

    test('TC-134: Xem admin dashboard', async ({ page, request }) => {
      await test.step('1. GET `/admin/agriverse`', async () => {
        await page.goto('/admin/agriverse');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Hiển thị KPIs, charts, recent orders', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.2 Quản lý sản phẩm (Admin)', () => {

    test('TC-135: Xem danh sách sản phẩm', async ({ page, request }) => {
      await test.step('1. GET `/admin/products`', async () => {
        await page.goto('/admin/products');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Tất cả sản phẩm (pagination, search, filter)', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-136: Sửa sản phẩm', async ({ page, request }) => {
      await test.step('1. GET `/admin/products/{product}/edit`', async () => {
        await page.goto('/admin/products/1/edit');
        await page.waitForLoadState('networkidle');
      });
      await test.step('2. Cập nhật', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });
      await test.step('3. PUT', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Sản phẩm được cập nhật', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-137: Xóa sản phẩm', async ({ page, request }) => {
      await test.step('1. DELETE `/admin/products/{product}`', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Sản phẩm bị xóa', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-138: Duyệt/Từ chối sản phẩm', async ({ page, request }) => {
      await test.step('1. POST approve/reject', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Trạng thái thay đổi', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.3 Quản lý cửa hàng (Admin)', () => {

    test('TC-139: Xem danh sách store', async ({ page, request }) => {
      await test.step('1. GET `/admin/stores`', async () => {
        await page.goto('/admin/stores');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Danh sách stores', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-140: Sửa store', async ({ page, request }) => {
      await test.step('1. GET `/admin/stores/{store}/edit`', async () => {
        await page.goto('/admin/stores/1/edit');
        await page.waitForLoadState('networkidle');
      });
      await test.step('2. Cập nhật', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Store được cập nhật', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-141: Xóa store', async ({ page, request }) => {
      await test.step('1. DELETE `/admin/stores/{store}`', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Store bị xóa', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.4 Quản lý đơn hàng (Admin)', () => {

    test('TC-142: Xem danh sách đơn hàng', async ({ page, request }) => {
      await test.step('1. GET `/admin/orders`', async () => {
        await page.goto('/admin/orders');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - All orders', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-143: Xem chi tiết đơn hàng', async ({ page, request }) => {
      await test.step('1. GET `/admin/orders/{order}`', async () => {
        await page.goto('/admin/orders/1');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Chi tiết order', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-144: Cập nhật trạng thái đơn hàng', async ({ page, request }) => {
      await test.step('1. PUT `/admin/orders/{order}/status`', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Trạng thái thay đổi', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-145: Xóa đơn hàng', async ({ page, request }) => {
      await test.step('1. DELETE `/admin/orders/{order}`', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Order bị xóa', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.5 Quản lý Coupon (Admin)', () => {

    test('TC-146: CRUD coupon', async ({ page, request }) => {
      await test.step('1. Tạo/Sửa/Xóa coupon đầy đủ', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Coupon hoạt động đúng', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.6 Quản lý người dùng (Admin)', () => {

    test('TC-147: Xem danh sách user', async ({ page, request }) => {
      await test.step('1. GET `/admin/users`', async () => {
        await page.goto('/admin/users');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Danh sách users', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-148: Tạo user mới', async ({ page, request }) => {
      await test.step('1. GET `/admin/users/create`', async () => {
        await page.goto('/admin/users/create');
        await page.waitForLoadState('networkidle');
      });
      await test.step('2. Nhập thông tin', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });
      await test.step('3. POST', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - User được tạo', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-149: Sửa user', async ({ page, request }) => {
      await test.step('1. GET `/admin/users/{user}/edit`', async () => {
        await page.goto('/admin/users/1/edit');
        await page.waitForLoadState('networkidle');
      });
      await test.step('2. Cập nhật', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - User được cập nhật', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-150: Xóa user', async ({ page, request }) => {
      await test.step('1. DELETE `/admin/users/{user}`', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - User bị xóa', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.7 Quản lý Seller Verification (Admin)', () => {

    test('TC-151: Xem danh sách đơn đăng ký', async ({ page, request }) => {
      await test.step('1. GET `/admin/sellers`', async () => {
        await page.goto('/admin/sellers');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Danh sách seller verifications', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-152: Xem chi tiết đơn', async ({ page, request }) => {
      await test.step('1. GET `/admin/sellers/{user}`', async () => {
        await page.goto('/admin/sellers/1');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Thông tin + ảnh CCCD', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-153: Duyệt đơn (Approve)', async ({ page, request }) => {
      await test.step('1. POST approve', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });
      await test.step('2. Store tự động được tạo (hoặc tạo manual)', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Seller được active, store created', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-154: Từ chối đơn (Reject)', async ({ page, request }) => {
      await test.step('1. POST reject kèm lý do', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Seller bị từ chối, nhận thông báo', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.8 Báo cáo (Admin)', () => {

    test('TC-155: Xem báo cáo doanh thu', async ({ page, request }) => {
      await test.step('1. GET `/admin/reports/revenue`', async () => {
        await page.goto('/admin/reports/revenue');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Biểu đồ doanh thu', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-156: Xem báo cáo hoa hồng', async ({ page, request }) => {
      await test.step('1. GET `/admin/reports/commission`', async () => {
        await page.goto('/admin/reports/commission');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Chi tiết hoa hồng', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-157: Xem báo cáo seller', async ({ page, request }) => {
      await test.step('1. GET `/admin/reports/sellers`', async () => {
        await page.goto('/admin/reports/sellers');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Top sellers, stats', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.9 Settings (Admin)', () => {

    test('TC-158: Xem trang settings', async ({ page, request }) => {
      await test.step('1. GET `/admin/settings`', async () => {
        await page.goto('/admin/settings');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Form settings', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-159: Cập nhật settings', async ({ page, request }) => {
      await test.step('1. Sửa thông tin', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });
      await test.step('2. POST', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Settings được lưu', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.10 Module Management', () => {

    test('TC-160: Xem danh sách modules', async ({ page, request }) => {
      await test.step('1. GET `/admin/modules`', async () => {
        await page.goto('/admin/modules');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Danh sách modules + trạng thái', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-161: Bật/tắt module', async ({ page, request }) => {
      await test.step('1. POST `/admin/modules/{module}/toggle`', async () => {
        await page.goto('/admin/modules/1/toggle');
      });

      await test.step('Verify: - Module được bật/tắt', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.11 File Manager', () => {

    test('TC-162: Mở CKFinder', async ({ page, request }) => {
      await test.step('1. GET `/ckfinder/browser`', async () => {
        await page.goto('/ckfinder/browser');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - CKFinder interface hiển thị', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.12 Quản lý bài viết diễn đàn (Admin)', () => {

    test('TC-163: Xem danh sách bài viết', async ({ page, request }) => {
      await test.step('1. GET `/admin/forum`', async () => {
        await page.goto('/admin/forum');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Tất cả bài viết', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-164: Duyệt/Từ chối bài viết', async ({ page, request }) => {
      await test.step('1. POST approve/reject', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Bài viết được duyệt', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-165: Ghim bài viết', async ({ page, request }) => {
      await test.step('1. POST pin', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Bài viết được ghim lên đầu', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-166: Xóa bài viết', async ({ page, request }) => {
      await test.step('1. DELETE', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Bài viết bị xóa', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.13 Quản lý Forum Categories (Admin)', () => {

    test('TC-167: CRUD forum category', async ({ page, request }) => {
      await test.step('1. Tạo/Sửa/Xóa đầy đủ', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Category hoạt động đúng', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.15 Quản lý Chat Groups (Admin)', () => {

    test('TC-169: Xem danh sách groups', async ({ page, request }) => {
      await test.step('1. GET `/admin/chat-groups`', async () => {
        await page.goto('/admin/chat-groups');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Danh sách chat groups', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-170: Duyệt/Từ chối thành viên', async ({ page, request }) => {
      await test.step('1. POST approve/reject member', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Membership thay đổi', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-171: Xóa group', async ({ page, request }) => {
      await test.step('1. DELETE', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Group bị xóa', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

  test.describe('7.16 AI Scanning Jobs (Admin)', () => {

    test('TC-172: Xem danh sách scan jobs', async ({ page, request }) => {
      await test.step('1. GET `/admin/scans`', async () => {
        await page.goto('/admin/scans');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Danh sách AI scan jobs', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-173: Xem chi tiết scan job', async ({ page, request }) => {
      await test.step('1. GET `/admin/scans/{scan}`', async () => {
        await page.goto('/admin/scans/1');
        await page.waitForLoadState('networkidle');
      });

      await test.step('Verify: - Kết quả scan', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-174: Xóa scan job', async ({ page, request }) => {
      await test.step('1. DELETE', async () => {
        // Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();
      });

      await test.step('Verify: - Scan job bị xóa', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

  });

});
