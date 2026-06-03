import { test, expect } from '@playwright/test';
import { DashboardPage } from './pages/DashboardPage';
import { ModuleRegistryPage } from './pages/ModuleRegistryPage';

test.describe('Orchestrix Admin UI Skill Test', () => {
    let dashboardPage: DashboardPage;
    let modulePage: ModuleRegistryPage;

    test.beforeEach(async ({ page }) => {
        dashboardPage = new DashboardPage(page);
        modulePage = new ModuleRegistryPage(page);
        await dashboardPage.login();
    });

    test('TS_01: Dashboard should display premium telemetry and module cards', async ({ page }) => {
        await dashboardPage.verifyTelemetry();
        await expect(dashboardPage.manageModulesLink).toBeVisible();
    });

    test('TS_02: Should be able to toggle module status in registry', async ({ page }) => {
        await dashboardPage.clickManageModules();
        await expect(modulePage.registryHeader).toBeVisible();

        // Get initial status of Portfolio
        const initialStatus = await modulePage.getModuleStatus('Portfolio');
        console.log(`Initial Portfolio Status: ${initialStatus}`);

        // Toggle it
        await modulePage.toggleModule('Portfolio');

        // Verify status change
        const newStatus = await modulePage.getModuleStatus('Portfolio');
        console.log(`New Portfolio Status: ${newStatus}`);
        expect(newStatus.toLowerCase()).not.toBe(initialStatus.toLowerCase());
    });

    test('TS_03: Sidebar navigation should lead to correct sections', async ({ page }) => {
        await dashboardPage.sidebarPortfolio.click();
        await expect(page).toHaveURL(/\/admin\/portfolio\/projects/);

        await dashboardPage.sidebarBlog.click();
        await expect(page).toHaveURL(/\/admin\/blog/);

        await dashboardPage.sidebarDashboard.click();
        await expect(page).toHaveURL(/\/admin$/);
    });
});
