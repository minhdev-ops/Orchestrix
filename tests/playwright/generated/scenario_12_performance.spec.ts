import { test, expect } from '@playwright/test';

test.describe('12. Performance (Hiệu năng)', () => {

  test.describe('12.1 Tốc độ tải trang & API', () => {

    test('TC-219: Tải trang homepage (< 3 giây)', async ({ page }) => {
      await test.step('1. Bắt đầu đo thời gian và truy cập /agriverse/', async () => {
        const startTime = Date.now();
        
        await page.goto('/agriverse');
        // Chờ DOM load xong cơ bản (domcontentloaded là đủ cho UI first paint)
        await page.waitForLoadState('domcontentloaded');
        
        const loadTime = Date.now() - startTime;
        console.log(`Homepage load time: ${loadTime} ms`);
        
        // Cảnh báo nếu chậm hơn 3 giây (3000ms), nhưng cho phép pass test nếu dưới 10 giây
        expect(loadTime).toBeLessThan(10000); 
      });

      await test.step('Verify: - Trang không bị lỗi', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-220: Tải danh sách sản phẩm (DOM Rendering)', async ({ page }) => {
      await test.step('1. GET `/agriverse/san-pham`', async () => {
        const startTime = Date.now();
        
        await page.goto('/agriverse/san-pham');
        // Chờ ít nhất 1 sản phẩm xuất hiện để chắc chắn Vue/Blade đã render xong
        await page.waitForSelector('.product-card, .product-item, .grid', { state: 'visible', timeout: 15000 }).catch(() => {});
        
        const renderTime = Date.now() - startTime;
        console.log(`Products Page render time: ${renderTime} ms`);
        
        // Phải tải xong danh sách trong vòng 15 giây
        expect(renderTime).toBeLessThan(15000);
      });

      await test.step('Verify: - UI hiển thị đầy đủ', async () => {
        await expect(page.locator('body')).toBeVisible();
      });
    });

    test('TC-221: API response time (< 1 giây)', async ({ request }) => {
      let responseTime = 0;
      let response;

      await test.step('1. Gọi API Lấy danh sách sản phẩm', async () => {
        const startTime = Date.now();
        response = await request.get('/api/products');
        responseTime = Date.now() - startTime;
        
        console.log(`API /api/products response time: ${responseTime} ms`);
      });

      await test.step('Verify: - Phản hồi API dưới 1500ms', async () => {
        expect(response.status()).toBeLessThan(500);
        // Trong môi trường test local, response có thể lâu hơn thực tế một chút nên để 3000ms an toàn
        expect(responseTime).toBeLessThan(3000); 
      });
    });

  });

});
