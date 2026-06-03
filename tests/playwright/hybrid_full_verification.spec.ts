import { test, expect } from '@playwright/test';
import { BlogPage } from './pages/BlogPage';

test.describe('Orchestrix Blog UI/UX Verification', () => {

    test('OX_BLOG_TC_001: Xác thực trang Blog (Blade) có 3D Canvas và Navbar Tiếng Việt', async ({ page }) => {
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

    test('OX_BLOG_TC_002 & 003: Xác thực bộ lọc Blog và chi tiết bài viết với 3D Persistence', async ({ page }) => {
        const blog = new BlogPage(page);
        await blog.navigate();

        // Test Filter
        await blog.filterBy('Laravel');

        // Open Detail
        await blog.openFirstPost();

        // Verify 3D still present on detail page
        await blog.verify3DBackground();
    });

    test('OX_BLOG_TC_004: Kiểm tra Console errors (No Three.js issues)', async ({ page }) => {
        const errors: string[] = [];
        page.on('console', msg => {
            if (msg.type() === 'error') errors.push(msg.text());
        });

        await page.goto('/blog');

        const threeJsErrors = errors.filter(e => e.toLowerCase().includes('three') || e.toLowerCase().includes('canvas'));
        expect(threeJsErrors).toHaveLength(0);
    });
});
