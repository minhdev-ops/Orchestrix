<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use Laravel\Passport\HasApiTokens;

/**
 * Generic User model that can be extended for project-specific needs.
 * 
 * This model provides a flexible foundation for user authentication
 * while allowing easy extension through inheritance or traits.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'key',
        'keyTime',
        'last_login_at',
        'ip_address',
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
            'is_active' => 'boolean',
            'role' => 'string',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'failed_login_attempts' => 'integer',
            'birthday' => 'date',
            'settings' => 'array',
            'metadata' => 'array',
            'keyTime' => 'datetime',
        ];
    }

    /**
     * Check if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if the user is an administrator.
     * 
     * This method provides a hook for role-based authorization
     * that can be overridden in project-specific implementations.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        // Default implementation - override in child classes
        // or use a trait/spatie-laravel-permission for complex roles
        return $this->role === 'admin';
    }

    /**
     * Check if the user has a specific role.
     *
     * This method provides a flexible role-checking mechanism
     * that can be adapted to different role storage strategies.
     *
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if (is_array($role)) {
            // Convert string to array for comparison if needed
            $userRoles = is_array($this->role) ? $this->role : [$this->role];
            return !! array_intersect($userRoles, $role);
        }
        
        return $this->role === $role;
    }

    /**
     * Check if the user account is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Record a successful login for the user.
     *
     * @param string $ipAddress
     * @return void
     */
    public function recordLogin(string $ipAddress = null): void
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
     *
     * @param int $maxAttempts
     * @param int $lockoutMinutes
     * @return void
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
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return !is_null($this->locked_until) && $this->locked_until->isFuture();
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include locked users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
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
                $user->uuid = (string) \Illuminate\Support\Str::uuid();
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
        return 'uuid';
    }
}
