<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    
    // Registration Tests

    public function test_user_can_view_register_page(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('register');
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticated();
    }

    public function test_user_is_logged_in_after_registration(): void
    {
        $this->post('/register', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'securepass123',
        ]);

        $this->assertAuthenticatedAs(User::where('email', 'jane@example.com')->first());
    }

    public function test_registration_requires_name(): void
    {
        $response = $this->post('/register', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_registration_requires_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_password_must_be_at_least_8_characters(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_password_is_hashed(): void
    {
        $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'plainpassword123',
        ]);

        $user = User::where('email', 'john@example.com')->first();

        $this->assertNotEquals('plainpassword123', $user->password);
    }


    // Login Tests

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_is_authenticated_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('securepass123'),
        ]);

        $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'securepass123',
        ]);

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_user(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_requires_valid_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'not-an-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'john@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_login_shows_only_email_errors(): void
    {
        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        // Only email error should be shown
        $response->assertSessionHasErrors('email');
        $response->assertSessionDoesntHaveErrors('password');
    }

    public function test_session_is_regenerated_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $oldSessionId = session()->getId();

        $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $newSessionId = session()->getId();

        $this->assertNotEquals($oldSessionId, $newSessionId);
    }


    // Logout Tests

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_user_is_logged_out_after_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }

    public function test_logout_clears_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(auth()->check());

        $this->post('/logout');

        $this->assertFalse(auth()->check());
    }


    // Protected Routes Tests

    public function test_authenticated_user_can_access_home_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_home_page(): void
    {
        $response = $this->get('/home');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
    }


    // Guest Routes Tests

    public function test_authenticated_user_cannot_access_register_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/home');
    }

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/home');
    }

    public function test_authenticated_user_cannot_register_again(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/register', [
            'name' => 'Another User',
            'email' => 'another@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/home');
    }


    // Welcome Page Tests

    public function test_guest_can_access_welcome_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }

    public function test_authenticated_user_is_redirected_from_welcome_to_home(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/home');
    }
}
