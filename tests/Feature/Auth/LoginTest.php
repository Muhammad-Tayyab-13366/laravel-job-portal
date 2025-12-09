<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_login_page_working(): void
    {
        $response = $this->get(route('account.login'));

        $response->assertStatus(200);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        // 1️⃣ Create a user
        $user = User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // 2️⃣ Attempt login
        $response = $this->post(route('account.process-login'), [
            'email' => 'test@gmail.com',
            'password' => 'password',
        ]);

        // 3️⃣ Check JSON response
        $response->assertJson([
            'status' => true,
            'errors' => [],
        ]); 

        // 4️⃣ Assert user is authenticated
        $this->assertAuthenticatedAs($user);
    }

    public function test_email_is_required_for_login(): void
    {

        $response = $this->post(route('account.process-login'), [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertJsonValidationErrors('email');
    }

    public function test_password_is_required_for_login(): void
    {

        $response = $this->post(route('account.process-login'), [
            'email' => 'test@gmail.com',
            'password' => '',
        ]);
        $response->assertJsonValidationErrors('password');
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        // 1️⃣ Create a user
        User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // 2️⃣ Attempt login with incorrect password
        $response = $this->post(route('account.process-login'), [
            'email' => 'test@gmil.com',
            'password' => 'wrongpassword',
        ]);

        // 3️⃣ Check JSON response
        $response->assertJson([ 
            'status' => false,
        ]); 

        $response->assertJsonStructure([
            'errors' => ['password'],
        ]);

    }

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        // 1️⃣ Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2️⃣ Attempt to access login page
        $response = $this->get(route('account.login'));

        // 3️⃣ Assert redirection to home page
        $response->assertRedirect(route('account.profile'));
    }

    public function test_login_rate_limit()
    {
        User::factory()->create([
            'email' => 'rate@example.com',
            'password' => Hash::make('password123')
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('account.process-login'), [
                'email' => 'rate@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        // 6th attempt should throttle (if rate limit enabled)
        $response = $this->postJson(route('account.process-login'), [
            'email' => 'rate@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertJson([
            "status" => false
        ]);

        $response->assertJsonValidationErrors('email');
    }

}
