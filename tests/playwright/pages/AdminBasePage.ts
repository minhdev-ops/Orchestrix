import { Page, Locator, expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class AdminBasePage extends BasePage {
    readonly sidebarDashboard: Locator;
    readonly sidebarPortfolio: Locator;
    readonly sidebarBlog: Locator;
    readonly topNavBrand: Locator;

    constructor(page: Page) {
        super(page);
        // Matching labels from layout/admin.blade.php
        this.sidebarDashboard = page.locator('a').filter({ hasText: /Bảng điều khiển/i }).first();
        this.sidebarPortfolio = page.locator('a').filter({ hasText: /Dự án Đã làm/i }).first();
        this.sidebarBlog = page.locator('a').filter({ hasText: /Blog Tin tức/i }).first();
        this.topNavBrand = page.getByText('ORCHESTRIX', { exact: true });
    }

    async login() {
        const user = process.env.PLAYWRIGHT_TEST_USER || 'testuser@example.com';
        const password = process.env.PLAYWRIGHT_TEST_PASSWORD || 'password';

        console.log(`Attempting login for ${user} at ${this.page.url()}...`);
        await this.page.goto('/login', { waitUntil: 'networkidle' });
        console.log(`Current URL after goto: ${this.page.url()}`);
        
        // Wait for form to be visible
        await expect(this.page.locator('form')).toBeVisible({ timeout: 15000 });
        
        await this.page.locator('#email').fill(user);
        await this.page.locator('#password').fill(password);
        await this.page.locator('button[type="submit"]').first().click();
        
        await this.page.waitForURL(/\/admin/, { timeout: 30000 });
        console.log('Login successful, navigated to /admin');
        await this.page.waitForLoadState('networkidle');
    }
}
