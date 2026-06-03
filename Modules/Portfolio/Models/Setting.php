<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Model Setting - Key-value store cho cấu hình Portfolio.
 * Hỗ trợ cache để giảm query database.
 *
 * Groups: general | home | seo | social | contact | appearance
 *
 * @property int         $id
 * @property string      $key
 * @property string|null $value
 * @property string      $group
 * @property string      $type
 * @property string|null $label
 */
class Setting extends Model
{
    protected $table = 'portfolio_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /** Cache TTL in seconds */
    private const CACHE_TTL = 3600;

    // =========================================================================
    // Static helpers (with cache)
    // =========================================================================

    /**
     * Lấy giá trị setting theo key, có hỗ trợ cache.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("portfolio.setting.{$key}", self::CACHE_TTL, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }

    /**
     * Set hoặc update giá trị setting, tự động xóa cache.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): static
    {
        Cache::forget("portfolio.setting.{$key}");

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * Lấy nhiều settings theo group, trả về dạng key => value.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("portfolio.settings.group.{$group}", self::CACHE_TTL, function () use ($group) {
            return static::where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Xóa toàn bộ cache settings.
     */
    public static function clearCache(): void
    {
        // Xóa cache thủ công theo key cụ thể nếu cần
        Cache::flush(); // Hoặc dùng tag nếu driver hỗ trợ
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeInGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
