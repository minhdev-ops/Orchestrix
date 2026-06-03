import { Page, Locator, expect } from '@playwright/test';
import { AdminBasePage } from './AdminBasePage';

export class DashboardPage extends AdminBasePage {
    readonly commandHeader: Locator;
    readonly statsBlog: Locator;
    readonly statsSystem: Locator;
    readonly manageModulesLink: Locator;
    readonly moduleRegistryCard: Locator;

    constructor(page: Page) {
        super(page);
        // Using a more relaxed text matcher for the header
        this.commandHeader = page.locator('h2, h1').filter({ hasText: /Quantum Dashboard/i }).first();

        this.statsBlog = page.locator('div, section').filter({ hasText: /Blog Posts/i }).first();
        this.statsSystem = page.locator('div, section').filter({ hasText: /Core Version/i }).first();

        this.moduleRegistryCard = page.locator('div, section').filter({ hasText: /Active Neural Modules/i }).first();
        this.manageModulesLink = page.locator('a[href*="/admin/modules"]').first();
    }

    async verifyTelemetry() {
        await this.page.waitForLoadState('domcontentloaded');
        await expect(this.commandHeader).toBeVisible({ timeout: 10000 });
    }

    async clickManageModules() {
        await expect(this.manageModulesLink).toBeVisible();
        await this.manageModulesLink.click();
        await expect(this.page).toHaveURL(/\/admin\/modules/);
    }
}
