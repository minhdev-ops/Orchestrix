import { test, expect } from '@playwright/test';

test.describe('AgriVerse Admin', () => {

  test('Admin Dashboard - redirect to login khi chưa auth', async ({ page }) => {
    await page.goto('/admin/agriverse');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin products - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/products');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin orders - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/orders');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin stores - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/stores');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin categories - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/categories');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin coupons - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/coupons');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin contracts - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/contracts');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin plans - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/plans');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin scans - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/scans');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin transactions - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/transactions');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin reports - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/reports');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin refunds - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/refunds');
    await expect(page).toHaveURL(/login/);
  });

  test('Admin orders create - redirect to login', async ({ page }) => {
    await page.goto('/admin/agriverse/orders/create');
    await expect(page).toHaveURL(/login/);
  });

});

test.describe('AgriVerse Admin - Authenticated Order Management', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@orchestrix.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL(/admin/);
  });

  test('Admin can see orders list', async ({ page }) => {
    await page.goto('/admin/agriverse/orders');
    await expect(page.locator('text=Đơn hàng').first()).toBeVisible();
  });

  test('Admin can see refunds list', async ({ page }) => {
    await page.goto('/admin/agriverse/refunds');
    await expect(page.locator('text=Hoàn tiền').or(page.locator('text=Refund'))).toBeVisible();
  });

  test('Admin can create manual order page', async ({ page }) => {
    await page.goto('/admin/agriverse/orders/create');
    await expect(page.locator('text=Tạo đơn hàng').or(page.locator('text=Thêm đơn hàng')).first()).toBeVisible();
  });

});
