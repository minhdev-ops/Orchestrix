<?php

namespace App\Modules\AgriVerse\Services;

use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;

class SeoService
{
    /**
     * Generate meta tags for product
     */
    public function productMeta(Product $product): array
    {
        $title = $product->seo_title ?: $product->name.' - AgriVerse';
        $description = $product->seo_description ?: $this->cleanHtml($product->description, 160);
        $image = $product->image_url ?? config('app.url').'/images/og-default.jpg';
        $url = route('agriverse.shop.products.show', $product->id);

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $product->seo_keywords ?: $product->name,
            'image' => $image,
            'url' => $url,
            'type' => 'product',
            'schema' => $this->productSchema($product),
        ];
    }

    /**
     * Generate meta tags for category
     */
    public function categoryMeta(Category $category): array
    {
        $title = $category->seo_title ?: $category->name.' - AgriVerse';
        $description = $category->seo_description ?: $this->cleanHtml($category->description, 160);
        $image = $category->image ?? config('app.url').'/images/og-default.jpg';
        $url = route('agriverse.shop.products.index', ['category' => $category->slug]);

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $category->seo_keywords ?: $category->name,
            'image' => $image,
            'url' => $url,
            'type' => 'website',
            'schema' => $this->categorySchema($category),
        ];
    }

    /**
     * Generate meta tags for store
     */
    public function storeMeta(Store $store): array
    {
        $title = $store->seo_title ?: $store->name.' - AgriVerse';
        $description = $store->seo_description ?: $this->cleanHtml($store->description, 160);
        $image = $store->logo ?? config('app.url').'/images/og-default.jpg';
        $url = route('agriverse.shop.stores.show', $store->id);

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $store->seo_keywords ?: $store->name,
            'image' => $image,
            'url' => $url,
            'type' => 'profile',
            'schema' => $this->storeSchema($store),
        ];
    }

    /**
     * Generate homepage meta
     */
    public function homeMeta(): array
    {
        return [
            'title' => 'AgriVerse - Sàn thương mại điện tử nông nghiệp',
            'description' => 'AgriVerse - Nền tảng thương mại điện tử nông nghiệp hàng đầu Việt Nam. Mua bán cây cảnh, vật tư nông nghiệp, chia sẻ kiến thức trồng trọt.',
            'keywords' => 'nông nghiệp, cây cảnh, bonsai, vật tư nông nghiệp, thị trường nông sản',
            'image' => config('app.url').'/images/og-home.jpg',
            'url' => config('app.url'),
            'type' => 'website',
            'schema' => $this->organizationSchema(),
        ];
    }

    /**
     * Generate JSON-LD schema for product
     */
    protected function productSchema(Product $product): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->description,
            'image' => $product->image_url,
            'sku' => $product->sku ?? $product->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->store->name ?? 'AgriVerse',
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $product->price,
                'priceCurrency' => 'VND',
                'availability' => $product->stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $product->store->name ?? 'AgriVerse',
                ],
            ],
            'aggregateRating' => $product->reviews_count > 0 ? [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->reviews_avg_rating ?? 5,
                'reviewCount' => $product->reviews_count,
            ] : null,
        ];
    }

    /**
     * Generate JSON-LD schema for category
     */
    protected function categorySchema(Category $category): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => $category->description,
            'url' => route('agriverse.shop.products.index', ['category' => $category->slug]),
        ];
    }

    /**
     * Generate JSON-LD schema for store
     */
    protected function storeSchema(Store $store): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $store->name,
            'description' => $store->description,
            'image' => $store->logo,
            'telephone' => $store->phone,
            'address' => $store->address ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $store->address,
            ] : null,
        ];
    }

    /**
     * Generate JSON-LD schema for organization
     */
    protected function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'AgriVerse',
            'url' => config('app.url'),
            'logo' => config('app.url').'/images/logo.png',
            'description' => 'Sàn thương mại điện tử nông nghiệp hàng đầu Việt Nam',
            'sameAs' => [
                'https://facebook.com/agriverse',
                'https://instagram.com/agriverse',
            ],
        ];
    }

    /**
     * Generate sitemap data
     */
    public function generateSitemap(): array
    {
        $sitemap = [];

        // Homepage
        $sitemap[] = [
            'url' => config('app.url'),
            'lastmod' => now()->toIso8601String(),
            'priority' => 1.0,
            'changefreq' => 'daily',
        ];

        // Products
        Product::published()
            ->latest('updated_at')
            ->chunk(100, function ($products) use (&$sitemap) {
                foreach ($products as $product) {
                    $sitemap[] = [
                        'url' => route('agriverse.shop.products.show', $product->id),
                        'lastmod' => $product->updated_at->toIso8601String(),
                        'priority' => 0.8,
                        'changefreq' => 'weekly',
                    ];
                }
            });

        // Categories
        Category::where('is_active', true)->each(function ($category) use (&$sitemap) {
            $sitemap[] = [
                'url' => route('agriverse.shop.products.index', ['category' => $category->slug]),
                'lastmod' => $category->updated_at->toIso8601String(),
                'priority' => 0.7,
                'changefreq' => 'weekly',
            ];
        });

        // Stores
        Store::where('is_active', true)->each(function ($store) use (&$sitemap) {
            $sitemap[] = [
                'url' => route('agriverse.shop.stores.show', $store->id),
                'lastmod' => $store->updated_at->toIso8601String(),
                'priority' => 0.6,
                'changefreq' => 'weekly',
            ];
        });

        return $sitemap;
    }

    /**
     * Generate robots.txt content
     */
    public function generateRobotsTxt(): string
    {
        $appUrl = config('app.url');

        return <<<TXT
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /auth/
Disallow: /seller/
Disallow: /thanh-toan/
Disallow: /gio-hang/

Sitemap: {$appUrl}/sitemap.xml
TXT;
    }

    /**
     * Clean HTML tags and truncate
     */
    protected function cleanHtml(?string $html, int $length = 160): string
    {
        if ($html === null || $html === '') {
            return '';
        }
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length).'...';
        }

        return $text;
    }
}
