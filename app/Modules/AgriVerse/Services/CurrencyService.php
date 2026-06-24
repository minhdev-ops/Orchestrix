<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected string $defaultCurrency = 'VND';
    protected array $supportedCurrencies = ['VND', 'USD'];

    /**
     * Exchange rates (VND as base)
     */
    protected array $rates = [
        'VND' => 1,
        'USD' => 0.000040, // 1 VND = 0.000040 USD (1 USD ≈ 25,000 VND)
    ];

    /**
     * Currency symbols
     */
    protected array $symbols = [
        'VND' => '₫',
        'USD' => '$',
    ];

    /**
     * Currency names
     */
    protected array $names = [
        'VND' => 'Đồng Việt Nam',
        'USD' => 'US Dollar',
    ];

    /**
     * Convert amount between currencies
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rates = $this->getRates();

        if (!isset($rates[$from]) || !isset($rates[$to])) {
            return $amount;
        }

        // Convert to VND first, then to target
        $inVnd = $amount / $rates[$from];
        return $inVnd * $rates[$to];
    }

    /**
     * Get exchange rates (from cache or API)
     */
    public function getRates(): array
    {
        $cached = Cache::get('currency_rates');

        if ($cached) {
            return $cached;
        }

        // Try to fetch from API
        $apiRates = $this->fetchRatesFromApi();

        if ($apiRates) {
            Cache::put('currency_rates', $apiRates, now()->addHours(6));
            return $apiRates;
        }

        // Fallback to default rates
        return $this->rates;
    }

    /**
     * Fetch rates from external API
     */
    protected function fetchRatesFromApi(): ?array
    {
        try {
            $response = Http::timeout(5)->get('https://api.exchangerate-api.com/v4/latest/VND');

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? [];

                return [
                    'VND' => 1,
                    'USD' => $rates['USD'] ?? $this->rates['USD'],
                ];
            }
        } catch (\Exception $e) {
            // Log error and return null
        }

        return null;
    }

    /**
     * Format amount with currency
     */
    public function format(float $amount, string $currency = null): string
    {
        $currency = $currency ?? $this->defaultCurrency;
        $symbol = $this->symbols[$currency] ?? $currency;

        if ($currency === 'VND') {
            return number_format($amount, 0, ',', '.') . ' ' . $symbol;
        }

        return $symbol . number_format($amount, 2, '.', ',');
    }

    /**
     * Get user's preferred currency
     */
    public function getUserCurrency(): string
    {
        return session('currency', $this->defaultCurrency);
    }

    /**
     * Set user's preferred currency
     */
    public function setUserCurrency(string $currency): bool
    {
        if (!in_array($currency, $this->supportedCurrencies)) {
            return false;
        }

        session(['currency' => $currency]);
        return true;
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return array_map(fn ($code) => [
            'code' => $code,
            'name' => $this->names[$code] ?? $code,
            'symbol' => $this->symbols[$code] ?? $code,
            'rate' => $this->rates[$code] ?? 1,
        ], $this->supportedCurrencies);
    }

    /**
     * Get all currency codes
     */
    public function getCurrencyCodes(): array
    {
        return $this->supportedCurrencies;
    }

    /**
     * Check if currency is supported
     */
    public function isSupported(string $currency): bool
    {
        return in_array($currency, $this->supportedCurrencies);
    }

    /**
     * Convert price for display
     */
    public function displayPrice(float $priceVnd, string $targetCurrency = null): string
    {
        $targetCurrency = $targetCurrency ?? $this->getUserCurrency();

        if ($targetCurrency === 'VND') {
            return $this->format($priceVnd, 'VND');
        }

        $converted = $this->convert($priceVnd, 'VND', $targetCurrency);
        return $this->format($converted, $targetCurrency);
    }

    /**
     * Get currency info
     */
    public function getCurrencyInfo(string $currency): ?array
    {
        if (!$this->isSupported($currency)) {
            return null;
        }

        return [
            'code' => $currency,
            'name' => $this->names[$currency],
            'symbol' => $this->symbols[$currency],
            'rate' => $this->rates[$currency],
        ];
    }
}
