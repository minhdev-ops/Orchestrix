import { test, expect } from '@playwright/test';
import { PortfolioPage } from './pages/PortfolioPage';
import { ProjectPage } from './pages/ProjectPage';

test.describe('Orchestrix Full UI/UX Verification', () => {

    test('OX_PORT_TC_001: Xác thực trang chủ React mount thành công', async ({ page }) => {
        const portfolio = new PortfolioPage(page);
        await portfolio.navigate();
        await portfolio.verifyReactMounted();
        await expect(portfolio.welcomeText.first()).toContainText('Xây dựng');
    });

    test('OX_PORT_TC_007: Xác thực lưới dự án và Dossier chi tiết', async ({ page }) => {
        const projects = new ProjectPage(page);
        await projects.navigate();

        await expect(projects.title).toBeVisible();
        await expect(projects.projectCards.first()).toBeVisible();

        // Open Detail
        await projects.openProjectDetail();

        // Verify 3D on project detail
        await projects.verify3DBackground();
    });

    test('OX_PORT_TC_008: Kiểm tra Console errors (No Three.js issues)', async ({ page }) => {
        const errors: string[] = [];
        page.on('console', msg => {
            if (msg.type() === 'error') errors.push(msg.text());
        });

        await page.goto('/portfolio');
        await page.goto('/portfolio/projects');

        const threeJsErrors = errors.filter(e => e.toLowerCase().includes('three') || e.toLowerCase().includes('canvas'));
        expect(threeJsErrors).toHaveLength(0);
    });
});
