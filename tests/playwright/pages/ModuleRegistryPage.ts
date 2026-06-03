import { Page, Locator, expect } from '@playwright/test';
import { AdminBasePage } from './AdminBasePage';

export class ModuleRegistryPage extends AdminBasePage {
    readonly registryHeader: Locator;

    constructor(page: Page) {
        super(page);
        this.registryHeader = page.locator('p, h2, h3').filter({ hasText: /Active Neural Modules|Registry/i }).first();
    }

    async toggleModule(moduleName: 'Portfolio' | 'Blog') {
        const displayName = moduleName;
        // In the dashboard grid, modules are listed
        const moduleItem = this.page.locator('div').filter({ hasText: new RegExp(displayName, 'i') }).last();
        await moduleItem.click();
        // Since it's a toggle in modules.json, we might need to go to modules index
        if (await this.page.url().includes('modules')) {
             const row = this.page.locator('tr, div.flex').filter({ hasText: new RegExp(displayName, 'i') }).last();
             const toggleBtn = row.locator('button, input[type="checkbox"]').first();
             await toggleBtn.click();
        }
        await this.page.waitForLoadState('networkidle');
    }

    async getModuleStatus(moduleName: 'Portfolio' | 'Blog'): Promise<string> {
        const moduleItem = this.page.locator('div').filter({ hasText: new RegExp(moduleName, 'i') }).last();
        return await moduleItem.locator('span').filter({ hasText: /Active|Operational|Disabled/i }).innerText();
    }
}
