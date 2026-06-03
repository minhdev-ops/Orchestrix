import { test, expect } from '@playwright/test';

test.describe('Orchestrix Hybrid Portfolio & 3D Integration', () => {

    test('nên hiển thị trang chủ React với canvas Hero', async ({ page }) => {
        await page.goto('/portfolio');
        // Chờ React mount
        await expect(page.locator('#portfolio-root')).toBeVisible();
        // Kiểm tra canvas của HeroCanvas
        await expect(page.locator('canvas')).toBeVisible();
        // Kiểm tra tiêu đề Tiếng Việt
        await expect(page.locator('h1')).toContainText('Xây dựng tương lai');
    });

    test('nên hiển thị trang Dự án (Blade) với giao diện dark và localization', async ({ page }) => {
        await page.goto('/portfolio/projects');

        await expect(page.locator('#three-canvas')).toBeVisible();
        await expect(page.locator('h1')).toContainText('Kho lưu trữ');

        // Kiểm tra card dự án
        const projectCard = page.locator('article').first();
        await expect(projectCard).toBeVisible();
        await expect(projectCard).toContainText('Xem chi tiết');
    });

    test('nên hiển thị trang chi tiết dự án với Tech Stack và Dossier style', async ({ page }) => {
        await page.goto('/portfolio/projects');

        // Click vào dự án đầu tiên
        const firstProject = page.locator('article a').first();
        await firstProject.click();

        await expect(page.locator('#three-canvas, canvas').first()).toBeVisible();
        await expect(page.locator('h3').filter({ hasText: /Tóm tắt dự án/i }).first()).toBeVisible();

        // Kiểm tra nút quay lại
        await expect(page.locator('a').filter({ hasText: /Quay lại danh mục|Danh mục dự án/i }).first()).toBeVisible();
    });
});
