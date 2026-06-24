import { test, expect } from '@playwright/test';

test.describe('2. Seller - Đăng sản phẩm', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[type="email"]', 'seller@orchestrix.com');
    await page.fill('input[type="password"]', '12345678');
    await page.click('button[type="submit"]');
    await page.waitForURL(/agriverse/, { timeout: 10000 });
  });

  test('TC-01: Vào trang thêm sản phẩm - đầy đủ fields', async ({ page }) => {
    await page.goto('http://localhost:8000/agriverse/seller/products/create');
    await expect(page.locator('h1:has-text("Thêm sản phẩm mới")')).toBeVisible();
    await expect(page.locator('.form-label:has-text("Tên sản phẩm")')).toBeVisible();
    await expect(page.locator('.form-label:has-text("Giá bán")')).toBeVisible();
    await expect(page.locator('.form-label:has-text("Danh mục")')).toBeVisible();
    await expect(page.locator('.form-label:has-text("Tồn kho")')).toBeVisible();
    await expect(page.locator('.form-label:has-text("Loại sản phẩm")')).toBeVisible();
    await expect(page.locator('.form-label:has-text("Nhà sản xuất")')).toBeVisible();
    await expect(page.locator('.form-label:has-text("Mô hình 3D")')).toBeVisible();
    await expect(page.locator('.form-label:has-text("Trạng thái")')).toBeVisible();
    await expect(page.locator('text=Sản phẩm nổi bật')).toBeVisible();
  });

  test('TC-02: Thêm sản phẩm thành công', async ({ page }) => {
    await page.goto('http://localhost:8000/agriverse/seller/products/create');
    const uniqueName = 'Bonsai Test ' + Date.now();

    await page.locator('.form-label:has-text("Tên sản phẩm")').locator('..').locator('input').fill(uniqueName);
    await page.locator('textarea').fill('Mô tả test sản phẩm cây cảnh 3D');
    await page.locator('.form-label:has-text("Giá bán")').locator('..').locator('input').fill('750000');
    await page.selectOption('select', { label: 'Bonsai cổ thụ' });
    await page.locator('.form-label:has-text("Tồn kho")').locator('..').locator('input').fill('12');
    await page.fill('input[placeholder*="phong"]', 'test, bonsai');
    await page.fill('input[placeholder*="URL"]', 'https://placehold.co/400x400/2d5016/ffffff?text=Test');

    await Promise.all([
      page.waitForURL(/seller\/products/, { timeout: 15000 }),
      page.click('button:has-text("Đăng sản phẩm")'),
    ]);

    expect(page.url()).toContain('seller/products');
  });

  test('TC-03: Bỏ trống tên - không submit', async ({ page }) => {
    await page.goto('http://localhost:8000/agriverse/seller/products/create');

    await page.locator('.form-label:has-text("Giá bán")').locator('..').locator('input').fill('100000');
    await page.selectOption('select', { label: 'Bonsai cổ thụ' });
    await page.locator('.form-label:has-text("Tồn kho")').locator('..').locator('input').fill('5');

    await page.click('button:has-text("Đăng sản phẩm")');
    await page.waitForTimeout(2000);
    expect(page.url()).toContain('products/create');
  });

  test('TC-04: Sửa sản phẩm', async ({ page }) => {
    await page.goto('http://localhost:8000/agriverse/seller/products');
    await page.locator('a:has-text("Sửa")').first().click();
    await expect(page.locator('h1:has-text("Sửa sản phẩm")')).toBeVisible();

    const nameInput = page.locator('.form-label:has-text("Tên sản phẩm")').locator('..').locator('input');
    await nameInput.clear();
    await nameInput.fill('Tên đã sửa ' + Date.now());

    await Promise.all([
      page.waitForURL(/seller\/products/, { timeout: 15000 }),
      page.click('button:has-text("Cập nhật")'),
    ]);

    expect(page.url()).toContain('seller/products');
  });

  test('TC-05: Dropdowns có dữ liệu', async ({ page }) => {
    await page.goto('http://localhost:8000/agriverse/seller/products/create');

    const catOpts = await page.locator('select').first().locator('option').count();
    expect(catOpts).toBeGreaterThan(1);

    const typeOpts = await page.locator('select').nth(1).locator('option').count();
    expect(typeOpts).toBeGreaterThan(1);

    const mfgOpts = await page.locator('select').nth(2).locator('option').count();
    expect(mfgOpts).toBeGreaterThan(1);
  });

});
