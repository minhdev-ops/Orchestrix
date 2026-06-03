import { test, expect } from '@playwright/test';
import { PortfolioPage } from './pages/PortfolioPage';
import { BlogPage } from './pages/BlogPage';
import { ProjectPage } from './pages/ProjectPage';

test.describe('Orchestrix Full UI/UX Verification', () => {

    test('OX_PORT_TC_001: Xác thực trang chủ React mount thành công', async ({ page }) => {
        const portfolio = new PortfolioPage(page);
        await portfolio.navigate();
        await portfolio.verifyReactMounted();
        await expect(portfolio.welcomeText.first()).toContainText('Xây dựng');
    });

    test('OX_PORT_TC:003 & 004: Xác thực trang Blog (Blade) có 3D Canvas và Navbar Tiếng Việt', async ({ page }) => {
        const blog = new BlogPage(page);
        await blog.navigate();

        // Verify 3D Canvas
        await blog.verify3DBackground();

        // Verify Navbar - Relaxed expectations for logo/home
        await expect(blog.navBlog.first()).toBeVisible();
        await expect(blog.navHome.first()).toBeVisible();

        // Verify Title
        await expect(blog.title.first()).toContainText('kỹ thuật');
    });

    test('OX_PORT_TC_005 & 006: Xác thực bộ lọc Blog và chi tiết bài viết với 3D Persistence', async ({ page }) => {
        const blog = new BlogPage(page);
        await blog.navigate();

        // Test Filter
        await blog.filterBy('Laravel');

        // Open Detail
        await blog.openFirstPost();

        // Verify 3D still present on detail page
        await blog.verify3DBackground();
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
        await page.goto('/blog');
        await page.goto('/portfolio/projects');

        const threeJsErrors = errors.filter(e => e.toLowerCase().includes('three') || e.toLowerCase().includes('canvas'));
        expect(threeJsErrors).toHaveLength(0);
    });
});
