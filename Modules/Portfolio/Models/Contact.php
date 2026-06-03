<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Contact - Tin nhắn liên hệ từ khách thăm Portfolio.
 *
 * Statuses: unread | read | replied | archived | spam
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string|null $phone
 * @property string|null $company
 * @property string      $subject
 * @property string      $message
 * @property string      $status
 * @property string|null $ip_address
 * @property string|null $admin_notes
 * @property \Carbon\Carbon|null $replied_at
 */
class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'portfolio_contacts';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'source',
        'admin_notes',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'unread',
    ];

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['archived', 'spam']);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Đánh dấu tin nhắn là đã đọc.
     */
    public function markAsRead(): bool
    {
        return $this->update(['status' => 'read']);
    }

    /**
     * Đánh dấu tin nhắn là đã trả lời.
     */
    public function markAsReplied(): bool
    {
        return $this->update([
            'status'     => 'replied',
            'replied_at' => now(),
        ]);
    }

    /**
     * Kiểm tra tin nhắn chưa đọc.
     */
    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }
}
