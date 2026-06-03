import { test, expect } from '@playwright/test';
import { DashboardPage } from './pages/DashboardPage';

test.describe('Orchestrix Admin UI Skill Test', () => {
    let dashboardPage: DashboardPage;

    test.beforeEach(async ({ page }) => {
        dashboardPage = new DashboardPage(page);
        await dashboardPage.login();
    });

    test('TS_01: Dashboard should display premium telemetry and module cards', async ({ page }) => {
        await dashboardPage.verifyTelemetry();
        await expect(dashboardPage.manageModulesLink).toBeVisible();
    });

    test('TS_02: Sidebar navigation should lead to correct sections', async ({ page }) => {
        await dashboardPage.sidebarBlog.click();
        await expect(page).toHaveURL(/\/admin\/blog/);

        await dashboardPage.sidebarDashboard.click();
        await expect(page).toHaveURL(/\/admin$/);
    });
});
