<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_expense(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = ExpenseCategory::create([
            'user_id' => $user->id,
            'name'    => 'Food',
            'icon'    => '🍜',
            'color'   => '#FF7043',
        ]);

        Passport::actingAs($user);

        $response = $this->postJson('/api/expenses/create', [
            'title'               => 'Lunch',
            'amount'              => 50000,
            'expense_category_id' => $category->id,
            'expense_date'        => '2026-08-16',
            'payment_method'      => 'cash',
            'note'                => 'Team lunch',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('mes', 'Expense created successfully.')
            ->assertJsonPath('data.title', 'Lunch')
            ->assertJsonPath('data.amount', '50000.00')
            ->assertJsonPath('data.category.name', 'Food');

        $this->assertDatabaseHas('expenses', [
            'user_id'             => $user->id,
            'expense_category_id' => $category->id,
            'title'               => 'Lunch',
            'amount'              => 50000,
        ]);
    }

    public function test_user_can_create_expense_with_receipt_image(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = ExpenseCategory::create([
            'user_id' => $user->id,
            'name'    => 'Transport',
        ]);

        Passport::actingAs($user);

        $response = $this->post('/api/expenses/create', [
            'title'               => 'Taxi',
            'amount'              => 100000,
            'expense_category_id' => $category->id,
            'receipt_image'       => UploadedFile::fake()->create('receipt.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Taxi')
            ->assertJsonPath('data.receipt_image_url', fn ($url) => is_string($url) && str_contains($url, 'receipts/'));
    }

    public function test_create_expense_requires_title_and_amount(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $response = $this->postJson('/api/expenses/create', []);

        $response->assertStatus(400)
            ->assertJsonStructure(['error' => ['title', 'amount', 'expense_category_id']]);
    }

    public function test_create_expense_rejects_category_of_another_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $other = User::factory()->create(['role' => 'user']);
        $category = ExpenseCategory::create([
            'user_id' => $other->id,
            'name'    => 'Other user category',
        ]);

        Passport::actingAs($user);

        $response = $this->postJson('/api/expenses/create', [
            'title'               => 'Test',
            'amount'              => 10000,
            'expense_category_id' => $category->id,
        ]);

        $response->assertStatus(400);

        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_unauthenticated_user_cannot_create_expense(): void
    {
        $response = $this->postJson('/api/expenses/create', [
            'title'  => 'Test',
            'amount' => 10000,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_list_own_categories(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        ExpenseCategory::create(['user_id' => $user->id, 'name' => 'Food']);
        ExpenseCategory::create(['user_id' => $user->id, 'name' => 'Rent']);

        $other = User::factory()->create(['role' => 'user']);
        ExpenseCategory::create(['user_id' => $other->id, 'name' => 'Hidden']);

        Passport::actingAs($user);

        $response = $this->getJson('/api/expense-categories');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonMissing(['name' => 'Hidden']);
    }

    public function test_user_can_create_category(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Passport::actingAs($user);

        $response = $this->postJson('/api/expense-categories/create', [
            'name'  => 'Shopping',
            'icon'  => '🛍️',
            'color' => '#26A69A',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Shopping');

        $this->assertDatabaseHas('expense_categories', [
            'user_id' => $user->id,
            'name'    => 'Shopping',
        ]);
    }

    public function test_user_cannot_create_duplicate_category(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        ExpenseCategory::create(['user_id' => $user->id, 'name' => 'Food']);

        Passport::actingAs($user);

        $response = $this->postJson('/api/expense-categories/create', [
            'name' => 'Food',
        ]);

        $response->assertStatus(400);
    }
}