<?php

namespace App\Models;

use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Subscription;
use App\Modules\AgriVerse\Models\ThreeDAsset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Passport\Contracts\OAuthenticatable as OAuthenticatableContract;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements OAuthenticatableContract
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SELLER = 'seller';

    public const ROLE_EMPLOYEE = 'employee';

    public const ROLE_BUYER = 'buyer';

    public static array $roles = [
        self::ROLE_ADMIN,
        self::ROLE_SELLER,
        self::ROLE_EMPLOYEE,
        self::ROLE_BUYER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * These include core authentication fields plus commonly used
     * profile and status fields that can be utilized across projects.
     * Project-specific fields can be added in extended models.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'avatar',
        'bio',
        'phone',
        'birthday',
        'gender',
        'provider',
        'provider_id',
        'settings',
        'metadata',
        'profile_info',
        'key',
        'keyTime',
        'user_permissions',
        'last_login_at',
        'ip_address',
        'fb',
        'seller_type',
        'seller_verified_at',
        'notification_preferences',
        'two_factor_enabled_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'string',
            'two_factor_recovery_codes' => 'array',
            'two_factor_enabled_at' => 'datetime',
            'is_active' => 'boolean',
            'role' => 'string',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'failed_login_attempts' => 'integer',
            'birthday' => 'date',
            'settings' => 'array',
            'metadata' => 'array',
            'profile_info' => 'array',
            'notification_preferences' => 'array',
            'user_permissions' => 'array',
            'keyTime' => 'datetime',
        ];
    }

    /**
     * Check if the user has verified their email address.
     */
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Check if the user is an administrator.
     *
     * This method provides a hook for role-based authorization
     * that can be overridden in project-specific implementations.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user has a specific role, checking both the Spatie roles
     * relationship and the legacy string role column.
     *
     * @param  string|array|Role|Collection  $role
     */
    public function hasRole($role, ?string $guard = null): bool
    {
        $this->loadMissing('roles');

        // Check Spatie roles relationship
        if ($role instanceof Role) {
            return $this->roles->contains($role->getKeyName(), $role->getKey());
        }

        if ($role instanceof Collection) {
            return $role->intersect($guard ? $this->roles->where('guard_name', $guard) : $this->roles)->isNotEmpty();
        }

        if (is_string($role)) {
            $hasSpatieRole = $guard
                ? $this->roles->where('guard_name', $guard)->contains('name', $role)
                : $this->roles->contains('name', $role);

            if ($hasSpatieRole) {
                return true;
            }

            return $this->role === $role;
        }

        if (is_array($role)) {
            $userRoles = is_array($this->role) ? $this->role : [$this->role];

            return (bool) array_intersect($userRoles, $role);
        }

        return false;
    }

    /**
     * Check if the user account is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Record a successful login for the user.
     */
    public function recordLogin(?string $ipAddress = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'ip_address' => $ipAddress,
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Record a failed login attempt for the user.
     */
    public function recordFailedLogin(int $maxAttempts = 5, int $lockoutMinutes = 15): void
    {
        $attempts = $this->failed_login_attempts + 1;

        $updateData = ['failed_login_attempts' => $attempts];

        if ($attempts >= $maxAttempts) {
            $updateData['locked_until'] = now()->addMinutes($lockoutMinutes);
        }

        $this->update($updateData);
    }

    /**
     * Check if the user account is locked due to too many failed login attempts.
     */
    public function isLocked(): bool
    {
        return ! is_null($this->locked_until) && $this->locked_until->isFuture();
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include locked users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeLocked($query)
    {
        return $query->whereNotNull('locked_until')
            ->where('locked_until', '>', now());
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function assets()
    {
        return $this->hasMany(ThreeDAsset::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'owner_id');
    }

    public function ordersAsBuyer()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function ordersAsSeller()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function isSeller(): bool
    {
        return $this->hasRole(self::ROLE_SELLER);
    }

    public function isEmployee(): bool
    {
        return $this->hasRole(self::ROLE_EMPLOYEE);
    }

    public function isBuyer(): bool
    {
        return $this->hasRole(self::ROLE_BUYER);
    }
}
