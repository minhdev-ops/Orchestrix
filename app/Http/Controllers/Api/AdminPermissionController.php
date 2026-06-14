<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminPermissionController extends Controller
{
    public function roles(): AnonymousResourceCollection
    {
        $roles = Role::with('permissions')->get();
        return JsonResource::collection($roles);
    }

    public function permissions(): AnonymousResourceCollection
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'other';
        });

        return JsonResource::collection($permissions);
    }

    public function assignRoleToUser(Request $request): JsonResource
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|string|exists:roles,name',
            'guard' => 'nullable|in:web,api',
        ]);

        $user = User::findOrFail($data['user_id']);
        $guard = $data['guard'] ?? 'web';
        $role = Role::where('name', $data['role'])->where('guard_name', $guard)->first();

        if (!$role) {
            $role = Role::findOrCreate($data['role'], $guard);
        }

        $user->assignRole($role);

        // Also assign API guard role
        if ($guard === 'web') {
            $apiRole = Role::where('name', $data['role'])->where('guard_name', 'api')->first();
            if ($apiRole) {
                $user->assignRole($apiRole);
            }
        }

        $user->load('roles');

        return JsonResource::make([
            'message' => "Role '{$data['role']}' assigned to user {$user->email}",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    public function removeRoleFromUser(Request $request): JsonResource
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($data['user_id']);
        $user->removeRole($data['role']);

        return JsonResource::make([
            'message' => "Role '{$data['role']}' removed from user {$user->email}",
        ]);
    }

    public function assignPermissionToRole(Request $request): JsonResource
    {
        $data = $request->validate([
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
            'guard' => 'nullable|in:web,api',
        ]);

        $guard = $data['guard'] ?? 'web';
        $role = Role::where('name', $data['role'])->where('guard_name', $guard)->firstOrFail();

        $role->syncPermissions($data['permissions']);

        // Also sync for API guard
        if ($guard === 'web') {
            $apiRole = Role::where('name', $data['role'])->where('guard_name', 'api')->first();
            if ($apiRole) {
                $apiRole->syncPermissions($data['permissions']);
            }
        }

        return JsonResource::make([
            'message' => 'Permissions updated for role: ' . $data['role'],
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }

    public function userPermissions(Request $request, User $user): JsonResource
    {
        $user->load('roles.permissions');

        return JsonResource::make([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }
}
