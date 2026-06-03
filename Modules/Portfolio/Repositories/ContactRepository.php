<?php

namespace Modules\Portfolio\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Portfolio\Models\Contact;

/**
 * ContactRepository - Data Access Layer cho Contacts.
 */
class ContactRepository extends BaseRepository
{
    public function __construct(Contact $model)
    {
        parent::__construct($model);
    }

    /**
     * Lấy tất cả contacts, mới nhất trước.
     */
    public function getAllLatest(): Collection
    {
        return $this->model->latest()->get();
    }

    /**
     * Phân trang cho admin.
     */
    public function paginateAll(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->active()->latest()->paginate($perPage);
    }

    /**
     * Đếm tin nhắn chưa đọc.
     */
    public function countUnread(): int
    {
        return $this->model->unread()->count();
    }

    /**
     * Lấy tất cả chưa đọc.
     */
    public function getUnread(): Collection
    {
        return $this->model->unread()->latest()->get();
    }

    /**
     * Đánh dấu đã đọc và trả về model đã cập nhật.
     */
    public function markAsRead(Contact $contact): Contact
    {
        $contact->markAsRead();
        return $contact->fresh();
    }

    /**
     * Lấy contacts theo status.
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->latest()->get();
    }
}
