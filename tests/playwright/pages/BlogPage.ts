import { Locator, expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class BlogPage extends BasePage {
    readonly title: Locator;
    readonly categoryFilters: Locator;
    readonly blogCards: Locator;

    constructor(page: any) {
        super(page);
        this.title = page.locator('h1').filter({ hasText: /Góc nhìn kỹ thuật/i });
        this.categoryFilters = page.locator('a.rounded-full');
        this.blogCards = page.locator('article');
    }

    async navigate() {
        await this.page.goto('/blog');
        await this.page.waitForLoadState('networkidle');
    }

    async filterBy(category: string) {
        const filter = this.categoryFilters.filter({ hasText: category }).first();
        await filter.click();
        await this.page.waitForLoadState('networkidle');
        // URL mới là /blog/{slug}
        await expect(this.page).toHaveURL(new RegExp(`/blog/${category.toLowerCase()}`));
    }

    async openFirstPost() {
        const firstPost = this.page.locator('a:has-text("Đọc chi tiết")').first();
        await firstPost.click();
        await expect(this.page.locator('article.prose')).toBeVisible();
    }
}
