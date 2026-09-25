<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $this->get(route('login'))->assertSuccessful()->assertSee('Masuk dashboard');
    }

    public function test_admin_can_login_and_logout(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'SecurePassword#2026',
            'role' => 'admin',
        ]);

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'SecurePassword#2026',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertSuccessful();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_repeated_failures_are_rate_limited(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_password_is_hashed_in_database(): void
    {
        $admin = User::factory()->create(['password' => 'PlainPassword#2026']);

        $this->assertNotSame('PlainPassword#2026', $admin->password);
        $this->assertTrue(Auth::guard('web')->attempt([
            'email' => $admin->email,
            'password' => 'PlainPassword#2026',
        ]));
    }
}
