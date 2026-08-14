<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class I18nService
{
    protected string $defaultLocale = 'vi';

    protected array $supportedLocales = ['vi', 'en'];

    protected array $translations = [];

    /**
     * Get current locale
     */
    public function getCurrentLocale(): string
    {
        return Session::get('locale', $this->defaultLocale);
    }

    /**
     * Set locale
     */
    public function setLocale(string $locale): bool
    {
        if (! in_array($locale, $this->supportedLocales)) {
            return false;
        }

        Session::put('locale', $locale);
        app()->setLocale($locale);

        return true;
    }

    /**
     * Get supported locales
     */
    public function getSupportedLocales(): array
    {
        return [
            'vi' => [
                'name' => 'Tiếng Việt',
                'flag' => '🇻🇳',
                'dir' => 'ltr',
            ],
            'en' => [
                'name' => 'English',
                'flag' => '🇬🇧',
                'dir' => 'ltr',
            ],
        ];
    }

    /**
     * Translate a key
     */
    public function trans(string $key, array $replace = []): string
    {
        $locale = $this->getCurrentLocale();
        $translations = $this->loadTranslations($locale);

        $keys = explode('.', $key);
        $value = $translations;

        foreach ($keys as $k) {
            if (! isset($value[$k])) {
                return $key;
            }
            $value = $value[$k];
        }

        if (is_string($value)) {
            foreach ($replace as $search => $replacement) {
                $value = str_replace(":{$search}", $replacement, $value);
            }
        }

        return $value;
    }

    /**
     * Load translations from file or cache
     */
    protected function loadTranslations(string $locale): array
    {
        if (isset($this->translations[$locale])) {
            return $this->translations[$locale];
        }

        $path = resource_path("lang/{$locale}.json");

        if (File::exists($path)) {
            $this->translations[$locale] = json_decode(File::get($path), true) ?? [];
        } else {
            $this->translations[$locale] = $this->getDefaultTranslations($locale);
        }

        return $this->translations[$locale];
    }

    /**
     * Get default translations
     */
    protected function getDefaultTranslations(string $locale): array
    {
        $translations = [
            'vi' => [
                'common' => [
                    'home' => 'Trang chủ',
                    'products' => 'Sản phẩm',
                    'categories' => 'Danh mục',
                    'cart' => 'Giỏ hàng',
                    'checkout' => 'Thanh toán',
                    'orders' => 'Đơn hàng',
                    'profile' => 'Hồ sơ',
                    'settings' => 'Cài đặt',
                    'logout' => 'Đăng xuất',
                    'login' => 'Đăng nhập',
                    'register' => 'Đăng ký',
                    'search' => 'Tìm kiếm',
                    'filter' => 'Lọc',
                    'sort' => 'Sắp xếp',
                    'save' => 'Lưu',
                    'cancel' => 'Hủy',
                    'delete' => 'Xóa',
                    'edit' => 'Chỉnh sửa',
                    'view' => 'Xem',
                    'add' => 'Thêm',
                    'close' => 'Đóng',
                    'confirm' => 'Xác nhận',
                    'loading' => 'Đang tải...',
                    'no_results' => 'Không tìm thấy kết quả',
                    'error' => 'Đã xảy ra lỗi',
                    'success' => 'Thành công',
                ],
                'product' => [
                    'name' => 'Tên sản phẩm',
                    'price' => 'Giá',
                    'stock' => 'Tồn kho',
                    'description' => 'Mô tả',
                    'add_to_cart' => 'Thêm vào giỏ',
                    'buy_now' => 'Mua ngay',
                    'in_stock' => 'Còn hàng',
                    'out_of_stock' => 'Hết hàng',
                    'reviews' => 'Đánh giá',
                    'related_products' => 'Sản phẩm liên quan',
                ],
                'order' => [
                    'pending' => 'Chờ xử lý',
                    'confirmed' => 'Đã xác nhận',
                    'shipping' => 'Đang giao',
                    'delivered' => 'Đã giao',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                    'total' => 'Tổng cộng',
                    'shipping_fee' => 'Phí vận chuyển',
                ],
                'payment' => [
                    'method' => 'Phương thức thanh toán',
                    'cod' => 'Thanh toán khi nhận hàng',
                    'banking' => 'Chuyển khoản ngân hàng',
                    'vnpay' => 'VNPay',
                    'momo' => 'Ví MoMo',
                ],
            ],
            'en' => [
                'common' => [
                    'home' => 'Home',
                    'products' => 'Products',
                    'categories' => 'Categories',
                    'cart' => 'Cart',
                    'checkout' => 'Checkout',
                    'orders' => 'Orders',
                    'profile' => 'Profile',
                    'settings' => 'Settings',
                    'logout' => 'Logout',
                    'login' => 'Login',
                    'register' => 'Register',
                    'search' => 'Search',
                    'filter' => 'Filter',
                    'sort' => 'Sort',
                    'save' => 'Save',
                    'cancel' => 'Cancel',
                    'delete' => 'Delete',
                    'edit' => 'Edit',
                    'view' => 'View',
                    'add' => 'Add',
                    'close' => 'Close',
                    'confirm' => 'Confirm',
                    'loading' => 'Loading...',
                    'no_results' => 'No results found',
                    'error' => 'An error occurred',
                    'success' => 'Success',
                ],
                'product' => [
                    'name' => 'Product name',
                    'price' => 'Price',
                    'stock' => 'Stock',
                    'description' => 'Description',
                    'add_to_cart' => 'Add to cart',
                    'buy_now' => 'Buy now',
                    'in_stock' => 'In stock',
                    'out_of_stock' => 'Out of stock',
                    'reviews' => 'Reviews',
                    'related_products' => 'Related products',
                ],
                'order' => [
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'shipping' => 'Shipping',
                    'delivered' => 'Delivered',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'total' => 'Total',
                    'shipping_fee' => 'Shipping fee',
                ],
                'payment' => [
                    'method' => 'Payment method',
                    'cod' => 'Cash on Delivery',
                    'banking' => 'Bank Transfer',
                    'vnpay' => 'VNPay',
                    'momo' => 'MoMo Wallet',
                ],
            ],
        ];

        return $translations[$locale] ?? $translations[$this->defaultLocale];
    }

    /**
     * Format currency based on locale
     */
    public function formatCurrency(float $amount, string $currency = 'VND'): string
    {
        $locale = $this->getCurrentLocale();

        if ($locale === 'vi') {
            return number_format($amount, 0, ',', '.').' ₫';
        }

        return '$'.number_format($amount, 2, '.', ',');
    }

    /**
     * Format date based on locale
     */
    public function formatDate($date, string $format = 'd/m/Y'): string
    {
        $locale = $this->getCurrentLocale();

        if (is_string($date)) {
            $date = strtotime($date);
        }

        if ($locale === 'en') {
            $format = str_replace(['d', 'm', 'Y'], ['m', 'd', 'Y'], $format);
        }

        return date($format, $date);
    }
}
