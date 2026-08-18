<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $guards = ['web', 'api'];

        $permissions = [
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'product.publish',
            'asset.upload',
            'asset.view',
            'asset.edit',
            'asset.delete',
            'asset.compress',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'subscription.view',
            'subscription.create',
            'subscription.edit',
            'subscription.delete',
            'settings.view',
            'settings.edit',
            'dashboard.view',
            'report.view',
            'store.view',
            'plan.view',
            'order.view',
            'order.create',
            'order.edit',
            'contract.view',
            'contract.create',
            'contract.edit',
            'admin.access',
        ];

        foreach ($guards as $guard) {
            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission, $guard);
            }
        }

        foreach ($guards as $guard) {
            $admin = Role::findOrCreate(User::ROLE_ADMIN, $guard);
            $admin->syncPermissions(Permission::where('guard_name', $guard)->get());

            $seller = Role::findOrCreate(User::ROLE_SELLER, $guard);
            $seller->syncPermissions(Permission::where('guard_name', $guard)
                ->whereIn('name', [
                    'product.view', 'product.create', 'product.edit', 'product.delete', 'product.publish',
                    'asset.upload', 'asset.view', 'asset.edit', 'asset.delete', 'asset.compress',
                    'subscription.view',
                    'dashboard.view',
                    'store.view',
                    'plan.view',
                    'order.view', 'order.create', 'order.edit',
                    'contract.view', 'contract.create', 'contract.edit',
                ])->get());

            $employee = Role::findOrCreate(User::ROLE_EMPLOYEE, $guard);
            $employee->syncPermissions(Permission::where('guard_name', $guard)
                ->whereIn('name', [
                    'product.view', 'product.create', 'product.edit',
                    'asset.upload', 'asset.view', 'asset.edit',
                    'subscription.view',
                    'dashboard.view',
                    'store.view',
                    'plan.view',
                ])->get());

            $buyer = Role::findOrCreate(User::ROLE_BUYER, $guard);
            $buyer->syncPermissions(Permission::where('guard_name', $guard)
                ->whereIn('name', [
                    'product.view',
                    'asset.view',
                    'store.view',
                    'plan.view',
                    'order.view',
                    'order.create',
                    'order.edit',
                    'contract.view',
                    'contract.edit',
                ])->get());
        }
    }
}
