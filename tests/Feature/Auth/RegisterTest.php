<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{

    use RefreshDatabase;

    protected string $route = '/account/process-register';
    /**
     * A basic feature test example.
     */
    public function test_is_register_page_shwoing(): void
    {
        $response = $this->get('/account/register');

        $response->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/account/process-register', [
            'name' => 'Test User',
            'email' => 'test@gmisl.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        $response->assertJson([
            'status' => true,
        ]);

        $response->assertJson([
            'errors' => [],
        ]);


        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@gmisl.com',
        ]);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        // First, create a user with a specific email
        $this->post('/account/process-register', [
            'name' => 'Existing User',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]); 
        // Attempt to register with the same email
        $response = $this->post('/account/process-register', [
            'name' => 'New User',
            'email' => 'test@gmail.com',
            'password' => 'newpassword',
            'confirm_password' => 'newpassword',
        ]);

        $response->assertJson([
            'status' => false,
        ]);
        $response->assertJsonStructure([
            'errors' => ['email'],
        ]); 
    }
 
    public function test_name_is_required_for_registration(): void
    {
        $response = $this->post('/account/process-register', [
            'name' => '',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);
        $response->assertJson([
            'status' => false,
        ]);
        $response->assertJsonStructure([
            'errors' => ['name'],
        ]);
    }

    public function test_email_is_required(){
        $this->post('/account/process-register', [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password',
            'confirm_password' => 'password',
        ])->assertJsonValidationErrors('email');


    }

    public function test_invalid_email_cannot_register(){
        $this->post('/account/process-register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password',
            'confirm_password' => 'password',
        ])->assertJsonValidationErrors('email');
    }   

    public function test_password_is_required_for_registration(): void
    {
        $response = $this->post($this->route, [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => '',
            'confirm_password' => 'test12345',
        ]);
        $response->assertJson([
            'status' => false,
        ]);
        $response->assertJsonStructure([
            'errors' => ['password'],
        ]); 
    }

    public function test_confirm_password_is_required_for_registration(): void
    {
        $response = $this->post($this->route, [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'test123456',
            'confirm_password' => '',
        ]);
        $response->assertJson([
            'status' => false,
        ]);
        $response->assertJsonStructure([
            'errors' => ['confirm_password'],
        ]); 
    }

    public function test_password_and_confirm_password_must_match(): void
    {
        $response = $this->post($this->route, [
            'name' => 'Test User',
            'email' => 'test@gmial.com',
            'password' => 'test123456',
            'confirm_password' => 'differentpassword',
        ]);
        $response->assertJson([
            'status' => false,
        ]);
        $response->assertJsonStructure([
            'errors' => ['password'],
        ]);
    }

    public function test_email_should_be_uniue(){
        $user = User::factory()->create([
            'email' => 'test@gmail.com',
        ]);

        $this->post($this->route, [
            'name' => 'Another User',
            'email' => 'test@gmail.com',
            'password' => 'password123',
            'confirm_password' => 'password123',
        ])->assertJsonValidationErrors('email');
    }

    public function test_password_is_hashed_in_database_after_registration(): void
    {
        $this->post($this->route, [
            'name' => 'Test User',
            'email' => 'test@gmai.com',
            'password' => 'password123',
            'confirm_password' => 'password123',
        ]);
        $user = User::where('email', 'test@gmai.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
