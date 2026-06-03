import { Locator, expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class ProjectPage extends BasePage {
    readonly title: Locator;
    readonly projectCards: Locator;

    constructor(page: any) {
        super(page);
        this.title = page.locator('h1').filter({ hasText: /Kho lưu trữ dự án/i });
        this.projectCards = page.locator('article');
    }

    async navigate() {
        await this.page.goto('/portfolio/projects');
        await this.page.waitForLoadState('networkidle');
    }

    async openProjectDetail(index: number = 0) {
        const card = this.projectCards.nth(index);
        const detailLink = card.locator('a').filter({ hasText: /Xem chi tiết/i });
        await detailLink.click();
        await this.page.waitForLoadState('networkidle');
        await expect(this.page.locator('h3').filter({ hasText: /Tóm tắt dự án/i }).first()).toBeVisible();
    }
}
