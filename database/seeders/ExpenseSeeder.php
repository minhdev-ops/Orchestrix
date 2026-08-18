<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'expense@orchestrix.com'],
            [
                'name'      => 'Expense Tester',
                'password'  => bcrypt('12345678'),
                'role'      => User::ROLE_BUYER,
                'is_active' => 1,
            ]
        );
        $user->is_active = 1;
        $user->save();

        $categories = [
            ['name' => 'Food',    'icon' => '🍜', 'color' => '#FF7043'],
            ['name' => 'Transport', 'icon' => '🚌', 'color' => '#29B6F6'],
            ['name' => 'Shopping', 'icon' => '🛍️', 'color' => '#26A69A'],
            ['name' => 'Rent',    'icon' => '🏠', 'color' => '#8D6E63'],
            ['name' => 'Health',  'icon' => '💊', 'color' => '#EF5350'],
            ['name' => 'Other',   'icon' => '📦', 'color' => '#9E9E9E'],
        ];

        $created = [];
        foreach ($categories as $category) {
            $created[] = ExpenseCategory::firstOrCreate(
                ['user_id' => $user->id, 'name' => $category['name']],
                $category
            );
        }

        if (!Expense::where('user_id', $user->id)->exists()) {
            Expense::create([
                'user_id'             => $user->id,
                'expense_category_id' => $created[0]->id,
                'title'               => 'Tiền ăn trưa',
                'amount'              => 50000,
                'expense_date'        => now()->toDateString(),
                'payment_method'      => 'cash',
                'note'                => 'Ăn trưa với team',
                'currency'            => 'VND',
            ]);
        }
    }
}