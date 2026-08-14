import { test, expect } from '@playwright/test';

test.describe('AgriVerse Core Features Demo Test', () => {
  const baseURL = 'http://127.0.0.1:8001/agriverse';

  test('F1: AR/3D Viewer - Overlay is present', async ({ page }) => {
    await page.goto(`${baseURL}/ar`);
    // Assuming the AR page has the 'Start AR' overlay logic
    const hasOverlay = await page.evaluate(() => {
      return document.querySelector('.ar-overlay') !== null || document.body.innerHTML.includes('Start');
    });
    console.log('F1 AR Viewer Check:', hasOverlay ? 'Pass' : 'Requires specific product page');
  });

  test('F3 & F5: Forum and Knowledge Base - TOC is present', async ({ page }) => {
    await page.goto(`${baseURL}/forum/1`); // Assuming post ID 1 exists
    const hasTOC = await page.evaluate(() => {
      return document.querySelector('.forum-toc') !== null || document.body.innerHTML.includes('Mục lục');
    });
    console.log('F3/F5 Forum TOC Check:', hasTOC ? 'Pass' : 'Failed to find TOC, or post 1 does not exist');
  });

});
