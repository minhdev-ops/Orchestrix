<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class AuthRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'web')->get('/admin');

        $response->assertOk();
    }

    public function test_buyer_cannot_access_admin_dashboard(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $buyer->assignRole('buyer');

        $response = $this->actingAs($buyer, 'web')->get('/admin');

        $response->assertRedirect();
    }

    public function test_user_can_register_via_api(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'repass' => 'password123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    public function test_user_cannot_login_without_activation(): void
    {
        User::factory()->create([
            'email' => 'inactive@test.com',
            'password' => bcrypt('password123'),
            'is_active' => false,
            'role' => 'buyer',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'inactive@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(404);
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create(['role' => 'buyer']);
        $user->assignRole('buyer');

        Passport::actingAs($user);

        $response = $this->postJson('/api/user/update', [
            'name' => 'Updated Name',
            'phone' => '0123456789',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
            'role' => 'buyer',
        ]);
        $user->assignRole('buyer');

        Passport::actingAs($user);

        $response = $this->postJson('/api/change-pass', [
            'oldpass' => 'oldpassword',
            'newpass' => 'newpassword123',
            'repass' => 'newpassword123',
        ]);

        $response->assertOk();
    }
}
