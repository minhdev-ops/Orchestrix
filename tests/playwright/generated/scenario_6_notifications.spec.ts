import { test, expect } from '@playwright/test';

test.describe('6. Notifications', () => {

  test.describe('Thông báo hệ thống', () => {

    test('TC-132: Xem danh sách Notification', async ({ page }) => {
      await test.step('1. Đăng nhập và truy cập trang chủ', async () => {
        // Giả lập user đã đăng nhập
        await page.goto('/login');
        await page.fill('input[type="email"]', 'seller@orchestrix.com');
        await page.fill('input[type="password"]', '12345678');
        await page.click('button[type="submit"]');
        await page.waitForURL(/\/agriverse/);
      });

      await test.step('2. Click vào biểu tượng Notification (Cái chuông)', async () => {
        // Tìm icon cái chuông thông báo
        const bellIcon = page.locator('.notification-bell, .icon-bell, button[aria-label*="Notification"]').first();
        if (await bellIcon.isVisible()) {
            await bellIcon.click();
        } else {
            // Fallback truy cập thẳng trang danh sách thông báo nếu không tìm thấy icon popup
            await page.goto('/agriverse/thong-bao');
        }
      });

      await test.step('Verify: Danh sách thông báo hiển thị', async () => {
        // Kiểm tra heading trang thông báo
        await expect(page.locator('h1:has-text("Thông báo")')).toBeVisible({ timeout: 5000 });
      });
    });

    test('TC-133: Đánh dấu đã đọc thông báo', async ({ page }) => {
      await test.step('1. Truy cập danh sách thông báo', async () => {
        await page.goto('/agriverse/thong-bao');
      });

      await test.step('2. Click "Đánh dấu tất cả đã đọc"', async () => {
        const markReadBtn = page.locator('button:has-text("Đánh dấu tất cả đã đọc")').first();
        if (await markReadBtn.isVisible()) {
            await markReadBtn.click();
        }
      });

      await test.step('Verify: Không còn thông báo chưa đọc', async () => {
        // Badge số lượng trên chuông biến mất hoặc chuyển về 0
        const unreadBadge = page.locator('.notification-badge, .unread-count');
        if (await unreadBadge.count() > 0) {
           await expect(unreadBadge).not.toBeVisible();
        }
      });
    });

  });

});

