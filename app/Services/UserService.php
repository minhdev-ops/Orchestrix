<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    protected function modelClass(): string
    {
        return User::class;
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return $this->create($data);
    }

    public function updateUser(User $user, array $data): User
    {
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->update($user, $data);
    }

    public function toggleActive(User $user): User
    {
        $user->update(['is_active' => ! $user->is_active]);

        return $user->fresh();
    }

    public function getByGroup(array $group)
    {
        return User::select('id', 'name', 'email', 'phone', 'role', 'birthday')
            ->where('is_active', 1)
            ->whereIn('role', $group)
            ->get();
    }
}
