import { Page, Locator, expect } from '@playwright/test';

export class BasePage {
    readonly page: Page;
    readonly navHome: Locator;
    readonly navAbout: Locator;
    readonly navTech: Locator;
    readonly navProjects: Locator;
    readonly navBlog: Locator;
    readonly navContact: Locator;
    readonly standaloneCanvas: Locator;

    constructor(page: Page) {
        this.page = page;
        // Sử dụng Regex cực kỳ linh hoạt để xử lý ngắt dòng và khoảng trắng
        this.navHome = page.locator('nav a').filter({ hasText: /Trang/i }).first();
        this.navAbout = page.locator('nav a').filter({ hasText: /Hồ/i });
        this.navTech = page.locator('nav a').filter({ hasText: /Công\s*nghệ/i });
        this.navProjects = page.locator('nav a').filter({ hasText: /Dự\s*án/i });
        this.navBlog = page.locator('nav a').filter({ hasText: /Bài|Blog/i });
        this.navContact = page.locator('nav a').filter({ hasText: /Liên\s*hệ/i });
        this.standaloneCanvas = page.locator('#three-canvas, #quantum-canvas, canvas').first();
    }

    async verify3DBackground() {
        await expect(this.standaloneCanvas).toBeVisible();
    }

    async navigateToBlog() {
        await this.navBlog.click();
        await this.page.waitForURL('**/blog');
    }

    async navigateToProjects() {
        await this.navProjects.click();
    }
}
