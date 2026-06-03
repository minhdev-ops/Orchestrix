<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\SubscriptionPlan;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@orchestrix.com',
            'role' => User::ROLE_ADMIN,
        ]);
        $admin->assignRole(User::ROLE_ADMIN);

        $seller = User::factory()->create([
            'name' => 'Seller',
            'email' => 'seller@orchestrix.com',
            'role' => User::ROLE_SELLER,
        ]);
        $seller->assignRole(User::ROLE_SELLER);

        $employee = User::factory()->create([
            'name' => 'Employee',
            'email' => 'employee@orchestrix.com',
            'role' => User::ROLE_EMPLOYEE,
        ]);
        $employee->assignRole(User::ROLE_EMPLOYEE);

        $buyer = User::factory()->create([
            'name' => 'Buyer',
            'email' => 'buyer@orchestrix.com',
            'role' => User::ROLE_BUYER,
        ]);
        $buyer->assignRole(User::ROLE_BUYER);

        $store = Store::create([
            'owner_id' => $seller->id,
            'name' => 'Seller\'s 3D Store',
            'description' => 'High-quality 3D models for agriculture and machinery.',
            'status' => 'active',
        ]);

        $plans = [
            ['name' => 'Basic', 'limit_3d_models' => 10, 'price_per_month' => 29.99, 'features' => ['products_limit' => 10, 'assets_limit' => 50, 'storage_gb' => 5]],
            ['name' => 'Pro', 'limit_3d_models' => 100, 'price_per_month' => 99.99, 'features' => ['products_limit' => 100, 'assets_limit' => 500, 'storage_gb' => 50]],
            ['name' => 'Advanced', 'limit_3d_models' => 500, 'price_per_month' => 299.99, 'features' => ['products_limit' => -1, 'assets_limit' => -1, 'storage_gb' => 500]],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }

        Product::create([
            'user_id' => $seller->id,
            'store_id' => $store->id,
            'name' => 'Sofa 3D Model',
            'description' => 'A modern sofa 3D model for virtual showrooms.',
            'price' => 29.99,
            'category' => 'furniture',
            'status' => 'published',
        ]);
    }
}
