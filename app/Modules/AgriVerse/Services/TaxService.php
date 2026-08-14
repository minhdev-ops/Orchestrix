<?php

namespace App\Modules\AgriVerse\Services;

use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;

class TaxService
{
    /**
     * Default VAT rate (10% Vietnam)
     */
    protected float $defaultRate = 0.10;

    /**
     * Tax rates by category
     */
    protected array $categoryRates = [
        'nong-san' => 0.05,      // Agricultural products: 5%
        'vat-tu' => 0.08,        // Agricultural supplies: 8%
        'cong-nghe' => 0.10,     // Technology: 10%
        'default' => 0.10,       // Default: 10%
    ];

    /**
     * Exempt categories (no tax)
     */
    protected array $exemptCategories = [
        'thu-y',                 // Veterinary
    ];

    /**
     * Calculate tax for an order
     */
    public function calculateOrderTax(Order $order): array
    {
        $items = [];

        if ($order->product) {
            $itemTax = $this->calculateItemTax($order->product, $order->quantity);
            $items[] = $itemTax;
        }

        $subtotal = $order->total_price;
        $totalTax = array_sum(array_column($items, 'tax_amount'));

        return [
            'subtotal' => $subtotal,
            'tax_items' => $items,
            'total_tax' => $totalTax,
            'total_with_tax' => $subtotal + $totalTax,
        ];
    }

    /**
     * Calculate tax for a single item
     */
    public function calculateItemTax(Product $product, int $quantity): array
    {
        $rate = $this->getProductTaxRate($product);
        $price = $product->price * $quantity;
        $taxAmount = round($price * $rate, 2);

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'subtotal' => $price,
            'tax_rate' => $rate,
            'tax_percentage' => $rate * 100,
            'tax_amount' => $taxAmount,
            'total_with_tax' => $price + $taxAmount,
        ];
    }

    /**
     * Get tax rate for a product
     */
    public function getProductTaxRate(Product $product): float
    {
        // Check if exempt
        $categories = $product->categories->pluck('slug')->toArray();
        foreach ($categories as $category) {
            if (in_array($category, $this->exemptCategories)) {
                return 0;
            }
        }

        // Find matching category rate
        foreach ($categories as $category) {
            if (isset($this->categoryRates[$category])) {
                return $this->categoryRates[$category];
            }
        }

        return $this->defaultRate;
    }

    /**
     * Generate tax invoice data
     */
    public function generateInvoiceData(Order $order): array
    {
        $taxData = $this->calculateOrderTax($order);

        return [
            'invoice_number' => 'VAT-'.$order->id.'-'.now()->format('Ymd'),
            'invoice_date' => now()->format('d/m/Y'),
            'seller' => [
                'name' => $order->store?->name ?? config('app.name'),
                'tax_id' => config('app.seller_tax_id', ''),
                'address' => $order->store?->address ?? config('app.seller_address', ''),
            ],
            'buyer' => [
                'name' => $order->buyer?->name ?? '',
                'address' => $order->shipping_address ?? '',
            ],
            'items' => $taxData['tax_items'],
            'subtotal' => $taxData['subtotal'],
            'total_tax' => $taxData['total_tax'],
            'total' => $taxData['total_with_tax'],
            'amount_in_words' => $this->numberToWords($taxData['total_with_tax']).' đồng',
        ];
    }

    /**
     * Get tax report
     */
    public function getTaxReport(string $startDate, string $endDate): array
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->with('product')
            ->get();

        $report = [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_orders' => $orders->count(),
            'total_revenue' => 0,
            'total_tax' => 0,
            'by_category' => [],
        ];

        foreach ($orders as $order) {
            if (! $order->product) {
                continue;
            }

            $taxData = $this->calculateItemTax($order->product, $order->quantity);
            $report['total_revenue'] += $taxData['subtotal'];
            $report['total_tax'] += $taxData['tax_amount'];

            $categories = $order->product->categories->pluck('name', 'slug')->toArray();
            foreach ($categories as $slug => $name) {
                if (! isset($report['by_category'][$slug])) {
                    $report['by_category'][$slug] = [
                        'name' => $name,
                        'revenue' => 0,
                        'tax' => 0,
                        'orders' => 0,
                    ];
                }
                $report['by_category'][$slug]['revenue'] += $taxData['subtotal'];
                $report['by_category'][$slug]['tax'] += $taxData['tax_amount'];
                $report['by_category'][$slug]['orders']++;
            }
        }

        return $report;
    }

    /**
     * Convert number to Vietnamese words
     */
    protected function numberToWords(float $number): string
    {
        $ones = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        $tens = ['', 'mười', 'hai mươi', 'ba mươi', 'bốn mươi', 'năm mươi', 'sáu mươi', 'bảy mươi', 'tám mươi', 'chín mươi'];

        if ($number === 0) {
            return 'không';
        }

        $result = '';
        $number = (int) $number;

        if ($number >= 1000000) {
            $result .= $ones[intval($number / 1000000)].' triệu ';
            $number %= 1000000;
        }

        if ($number >= 1000) {
            $result .= $ones[intval($number / 1000)].' nghìn ';
            $number %= 1000;
        }

        if ($number >= 100) {
            $result .= $ones[intval($number / 100)].' trăm ';
            $number %= 100;
        }

        if ($number >= 10) {
            $result .= $tens[intval($number / 10)].' ';
            $number %= 10;
        }

        if ($number > 0) {
            $result .= $ones[$number];
        }

        return trim($result);
    }

    /**
     * Get all tax rates
     */
    public function getTaxRates(): array
    {
        return [
            'default' => $this->defaultRate * 100 .'%',
            'categories' => array_map(fn ($rate) => $rate * 100 .'%', $this->categoryRates),
            'exempt' => $this->exemptCategories,
        ];
    }
}
