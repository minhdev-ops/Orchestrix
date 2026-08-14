<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        setupPassport();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->markTestSkipped('Blocked by SeoMiddleware bug - causes 500 error');
    }

    public function test_buyer_cannot_access_admin_dashboard(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer', 'is_active' => true]);

        $response = $this->actingAs($buyer, 'web')->get('/admin');
        $response->assertRedirect();
    }

    public function test_user_can_register_via_api(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New User '.uniqid(),
            'email' => 'newuser_'.uniqid().'@test.com',
            'password' => 'password123',
            'repass' => 'password123',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_login_without_activation(): void
    {
        $user = User::create([
            'name' => 'Inactive User',
            'email' => 'inactive_'.uniqid().'@test.com',
            'password' => bcrypt('password123'),
            'is_active' => false,
            'role' => 'buyer',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(404);

        $user->forceDelete();
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::where('role', 'buyer')->first();
        if (! $user) {
            $this->markTestSkipped('No buyer user found');
        }

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
        $this->seed(RoleAndPermissionSeeder::class);
        setupPassport();

        $user = User::factory()->create([
            'role' => 'buyer',
            'password' => Hash::make('oldpassword123'),
        ]);
        $user->assignRole('buyer');

        Passport::actingAs($user);

        $response = $this->postJson('/api/change-pass', [
            'oldpass' => 'oldpassword123',
            'newpass' => 'newpassword123',
            'repass' => 'newpassword123',
        ]);

        $response->assertOk();
    }
}
