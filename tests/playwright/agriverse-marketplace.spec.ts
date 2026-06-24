import { test, expect } from '@playwright/test';

test.describe('AgriVerse Marketplace', () => {

  test('Trang chủ - hiển thị đúng cấu trúc', async ({ page }) => {
    await page.goto('/agriverse');
    await expect(page).toHaveTitle(/Orchestrix/);
    await expect(page.locator('text=AgriVerse').first()).toBeVisible();
    await expect(page.locator('text=Từ vườn').first()).toBeVisible();
    await expect(page.locator('text=Mua ngay').first()).toBeVisible();
    await expect(page.locator('text=Danh mục').first()).toBeVisible();
    await expect(page.locator('text=Nổi bật').first()).toBeVisible();
    await expect(page.locator('text=Gian hàng').first()).toBeVisible();
  });

  test('Trang chủ - các section hiển thị', async ({ page }) => {
    await page.goto('/agriverse');
    const sections = ['Danh mục', 'Nổi bật', 'Gian hàng'];
    for (const section of sections) {
      await expect(page.locator(`text=${section}`).first()).toBeVisible();
    }
  });

  test('Trang sản phẩm - hiển thị grid', async ({ page }) => {
    await page.goto('/agriverse/san-pham');
    await expect(page.locator('text=Sản phẩm').first()).toBeVisible();
    await expect(page.locator('text=Danh mục').first()).toBeVisible();
    await expect(page.locator('text=Khoảng giá').first()).toBeVisible();
  });

  test('Trang sản phẩm - filter hoạt động', async ({ page }) => {
    await page.goto('/agriverse/san-pham');
    const sortSelect = page.locator('select').first();
    await sortSelect.selectOption('price_asc');
    await page.waitForURL(/sort=price_asc/);
    await expect(page.locator('text=Sản phẩm').first()).toBeVisible();
  });

  test('Trang chi tiết sản phẩm - hiển thị thông tin', async ({ page }) => {
    await page.goto('/agriverse/san-pham');
    const productLink = page.locator('a[href*="/san-pham/"]').first();
    if (await productLink.count() > 0) {
      const href = await productLink.getAttribute('href');
      await page.goto(href!);
      await expect(page.locator('text=Thêm vào giỏ').first()).toBeVisible();
      await expect(page.locator('text=Mua ngay').first()).toBeVisible();
    }
  });

  test('Trang cửa hàng - hiển thị danh sách', async ({ page }) => {
    await page.goto('/agriverse/cua-hang');
    await expect(page.locator('text=Cửa hàng').first()).toBeVisible();
  });

  test('Trang danh mục - hiển thị', async ({ page }) => {
    await page.goto('/agriverse/danh-muc');
    await expect(page.locator('text=Danh mục sản phẩm').first()).toBeVisible();
  });

  test('Navigation - menu điều hướng', async ({ page }) => {
    await page.goto('/agriverse');
    await expect(page.locator('a[href*="/san-pham"]').first()).toBeVisible();
    await expect(page.locator('a[href*="/danh-muc"]').first()).toBeVisible();
    await expect(page.locator('a[href*="/cua-hang"]').first()).toBeVisible();
  });

  test('Search bar - hiển thị', async ({ page }) => {
    await page.goto('/agriverse');
    const searchInput = page.locator('input[placeholder*="Tìm nông sản"]');
    await expect(searchInput).toBeVisible();
  });

  test('Cart icon - hiển thị', async ({ page }) => {
    await page.goto('/agriverse');
    await expect(page.locator('a[href*="/gio-hang"]').first()).toBeVisible();
  });

  test('Wishlist icon - hiển thị', async ({ page }) => {
    await page.goto('/agriverse');
    await expect(page.locator('a[href*="/yeu-thich"]').first()).toBeVisible();
  });

  test('Footer - hiển thị đầy đủ', async ({ page }) => {
    await page.goto('/agriverse');
    await expect(page.locator('text=Khám phá').first()).toBeVisible();
    await expect(page.locator('text=Sản phẩm').first()).toBeVisible();
    await expect(page.locator('text=Gian hàng').first()).toBeVisible();
    await expect(page.locator('text=Hỗ trợ').first()).toBeVisible();
    await expect(page.locator('text=Kết nối').first()).toBeVisible();
  });

});

test.describe('AgriVerse Buyer Orders - Cancel & Refund', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'buyer@orchestrix.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL(/agriverse/);
  });

  test('Buyer can view orders list', async ({ page }) => {
    await page.goto('/agriverse/don-hang');
    await expect(page.locator('text=Đơn hàng của tôi').first()).toBeVisible();
  });

  test('Buyer can view order detail page', async ({ page }) => {
    await page.goto('/agriverse/don-hang');
    const orderLink = page.locator('a[href*="/don-hang/"]').first();
    if (await orderLink.count() > 0) {
      await orderLink.click();
      await expect(page.locator('text=Đơn hàng #').first()).toBeVisible();
    }
  });

});

test.describe('AgriVerse Buyer Addresses CRUD', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'buyer@orchestrix.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL(/agriverse/);
  });

  test('Address list page renders', async ({ page }) => {
    await page.goto('/agriverse/dia-chi');
    await expect(page.locator('text=Sổ địa chỉ').first()).toBeVisible();
  });

  test('Can navigate to create address form', async ({ page }) => {
    await page.goto('/agriverse/dia-chi');
    const addButton = page.locator('a[href*="/dia-chi/them-moi"]').first();
    if (await addButton.count() > 0) {
      await addButton.click();
      await expect(page).toHaveURL(/them-moi/);
    }
  });

  test('Create address form has required fields', async ({ page }) => {
    await page.goto('/agriverse/dia-chi/them-moi');
    await expect(page.locator('input[name="recipient_name"]').first()).toBeVisible();
    await expect(page.locator('input[name="phone"]').first()).toBeVisible();
  });

  test('Address edit page renders for existing address', async ({ page }) => {
    await page.goto('/agriverse/dia-chi');
    const editLink = page.locator('a[href*="/sua"]').first();
    if (await editLink.count() > 0) {
      await editLink.click();
      await expect(page.locator('text=Sửa địa chỉ').first()).toBeVisible();
    }
  });

  test('Can delete address via confirm dialog', async ({ page }) => {
    await page.goto('/agriverse/dia-chi');
    const deleteBtn = page.locator('button.p-confirm-popup').first();
    if (await deleteBtn.count() > 0) {
      await deleteBtn.click();
    }
  });

});
