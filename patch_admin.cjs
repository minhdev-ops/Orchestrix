const fs = require('fs');
const file = 'tests/playwright/generated/scenario_7_admin_panel.spec.ts';
let content = fs.readFileSync(file, 'utf-8');

const beforeEachHook = `
  test.beforeEach(async ({ page }) => {
    // Tự động đăng nhập bằng tài khoản Admin
    await page.goto('/login');
    await page.fill('input[type="email"]', 'admin@orchestrix.com');
    await page.fill('input[type="password"]', '12345678');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
  });
`;

if (!content.includes('test.beforeEach')) {
  content = content.replace("test.describe('7. Admin Panel', () => {", "test.describe('7. Admin Panel', () => {\n" + beforeEachHook);
}

// Replace the fallback scrollTo with generic button clicks
content = content.replace(/await page\.evaluate\(\(\) => window\.scrollTo\(0, document\.body\.scrollHeight\)\);/g, 
  `// Auto-fallback: Click nút xác nhận hoặc lưu
        const actionBtn = page.locator('button[type="submit"], button:has-text("Duyệt"), button:has-text("Xóa"), button:has-text("Lưu")').first();
        if (await actionBtn.isVisible()) await actionBtn.click();`);

fs.writeFileSync(file, content, 'utf-8');
console.log('Admin test patched successfully!');
