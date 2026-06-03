<?php

namespace Modules\Portfolio\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Portfolio\Models\Contact;
use Modules\Portfolio\Repositories\ContactRepository;

/**
 * ContactService - Business Logic Layer cho Contacts.
 */
class ContactService
{
    public function __construct(
        private readonly ContactRepository $contactRepository
    ) {}

    // =========================================================================
    // Read Operations
    // =========================================================================

    public function paginateAllForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->contactRepository->paginateAll($perPage);
    }

    public function countUnread(): int
    {
        return $this->contactRepository->countUnread();
    }

    public function getUnreadContacts(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->contactRepository->getUnread();
    }

    public function countAll(): int
    {
        return $this->contactRepository->count();
    }

    // =========================================================================
    // Write Operations
    // =========================================================================

    /**
     * Khách hàng gửi tin nhắn mới.
     */
    public function submitContact(array $data): Contact
    {
        // Có thể thêm logic: Gửi email thông báo cho Admin, check spam IP, v.v.
        $data['status'] = 'unread';
        return $this->contactRepository->create($data);
    }

    /**
     * Admin xem tin nhắn.
     */
    public function markAsRead(Contact $contact): Contact
    {
        if ($contact->isUnread()) {
            return $this->contactRepository->markAsRead($contact);
        }
        return $contact;
    }

    /**
     * Xóa tin nhắn (Soft delete theo Model).
     */
    public function deleteContact(Contact $contact): bool
    {
        return $this->contactRepository->delete($contact);
    }
}
