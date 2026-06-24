<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use App\Modules\AgriVerse\Services\SeoService;

Route::get('/sitemap.xml', function (SeoService $seoService) {
    $sitemap = $seoService->generateSitemap();

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ($sitemap as $item) {
        $xml .= '<url>';
        $xml .= '<loc>' . e($item['url']) . '</loc>';
        $xml .= '<lastmod>' . e($item['lastmod']) . '</lastmod>';
        $xml .= '<changefreq>' . e($item['changefreq']) . '</changefreq>';
        $xml .= '<priority>' . e($item['priority']) . '</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200)
        ->header('Content-Type', 'application/xml');
})->middleware(['web']);

Route::get('/robots.txt', function (SeoService $seoService) {
    $content = $seoService->generateRobotsTxt();

    return response($content, 200)
        ->header('Content-Type', 'text/plain');
})->middleware(['web']);
