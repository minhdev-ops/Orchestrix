import { Locator, expect } from '@playwright/test';
import { BasePage } from './BasePage';

export class PortfolioPage extends BasePage {
    readonly reactRoot: Locator;
    readonly heroCanvas: Locator;
    readonly welcomeText: Locator;

    constructor(page: any) {
        super(page);
        this.reactRoot = page.locator('#portfolio-root');
        this.heroCanvas = page.locator('#portfolio-root canvas');
        this.welcomeText = page.locator('h1');
    }

    async navigate() {
        await this.page.goto('/portfolio');
    }

    async verifyReactMounted() {
        await expect(this.reactRoot).toBeVisible();
        await expect(this.heroCanvas).toBeVisible();
    }
}
