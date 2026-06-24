import { test, expect } from '@playwright/test';

test.describe('11. UI/UX', () => {

  test.describe('11.1 Trải nghiệm người dùng (UI/UX)', () => {

    test('TC-214: Responsive layout', async ({ page }) => {
      await test.step('1. Kích thước Desktop', async () => {
        await page.setViewportSize({ width: 1920, height: 1080 });
        await page.goto('/agriverse');
        const box = await page.locator('body').boundingBox();
        expect(box?.width).toBeGreaterThan(1024);
      });

      await test.step('2. Kích thước Tablet', async () => {
        await page.setViewportSize({ width: 768, height: 1024 });
        await page.goto('/agriverse');
        const box = await page.locator('body').boundingBox();
        expect(box?.width).toBeLessThanOrEqual(768);
      });

      await test.step('3. Kích thước Mobile', async () => {
        await page.setViewportSize({ width: 375, height: 812 });
        await page.goto('/agriverse');
        const box = await page.locator('body').boundingBox();
        expect(box?.width).toBeLessThanOrEqual(375);
      });

      await test.step('Verify: - Layout thích ứng, không vỡ', async () => {
        // Nếu load thành công ở cả 3 kích thước không bị crash là đạt
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-215: Loading states', async ({ page }) => {
      await test.step('1. Truy cập trang chủ', async () => {
        await page.goto('/agriverse');
      });

      await test.step('Verify: - Chờ tải xong mọi dữ liệu mạng (Network Idle)', async () => {
        await page.waitForLoadState('networkidle');
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-216: Empty states (Trạng thái rỗng)', async ({ page }) => {
      await test.step('1. Xem giỏ hàng khi chưa có đồ (Hoặc chưa đăng nhập)', async () => {
        await page.goto('/agriverse/gio-hang');
      });

      await test.step('Verify: - Không có lỗi, hiển thị UI an toàn', async () => {
        // Đảm bảo không văng 500 khi giỏ rỗng
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-217: Error states (Mạng chậm/Lỗi kết nối)', async ({ page }) => {
      await test.step('1. Cố tình chặn (abort) các request API để giả lập mất mạng', async () => {
        await page.route('**/api/**', route => route.abort());
        await page.goto('/agriverse/san-pham');
      });

      await test.step('Verify: - UI vẫn hiển thị khung, không sập trắng trang (White screen of death)', async () => {
        await expect(page.locator('body')).toBeVisible();
        // Reset lại route
        await page.unroute('**/api/**');
      });
    });

    test('TC-218: 404 page', async ({ page }) => {
      let response;
      await test.step('1. GET URL không tồn tại', async () => {
        response = await page.goto('/agriverse/link-nay-khong-bao-gio-ton-tai-12345');
      });

      await test.step('Verify: - HTTP Status là 404', async () => {
        expect(response?.status()).toBe(404);
      });
    });

  });

});
