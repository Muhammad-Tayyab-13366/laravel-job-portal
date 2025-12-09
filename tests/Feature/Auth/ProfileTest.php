<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_profile_page_responding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('account.profile'));

        $response->assertStatus(200);
    }

    public function test_profile_page_redirects_to_login_when_not_logged_in()
    {
        $response = $this->get(route('account.profile'));

        $response->assertRedirect(route('account.login'));
    }    

    public function test_update_prfile(){

        $user = User::factory()->create([
            "name" => "test",
            "email" => "test@gmail.com",
            "designation" => "software developer",
            "mobile" => "0320-40903245"
        ]);

        $this->actingAs($user);

        $response = $this->put(route('account.profile.update'),[
            "name" => "test updated",
            "email" => "test1@gmail.com",
            "designation" => "updated designation",
            "mobile" => "0320-0000000"
        ]);

       
        $response->assertJson([
            "status" => true
        ]);

        $this->assertDatabaseHas('users', [
            "id" => $user->id,
            "name" => "test updated",
            "email" => "test1@gmail.com",
            "designation" => "updated designation",
            "mobile" => "0320-0000000"
        ]);


    }

    public function test_profile_picture_upload_success()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tempDir = public_path('profile_pic');
        $thumbDir = public_path('profile_pic/thumb');
        if(!is_dir($tempDir)) { mkdir($tempDir, 0777, true); }
        if(!is_dir($thumbDir)) { mkdir($thumbDir, 0777, true); }

        $image = UploadedFile::fake()->image('avatar.png', 500, 500);

        $response = $this->post(route('account.profile-pic.update'),[
            "image" => $image
        ]);

        $response->assertJson([
            "status" => true,
            "errors" => []
        ]);

        $this->assertDatabaseHas('users', [
            "id" => $user->id
        ]);

        $updatedUser = User::find($user->id);

        $savedImage = $updatedUser->image;

        $this->assertFileExists(public_path('profile_pic').'/'.$savedImage);
        $this->assertFileExists(public_path('profile_pic/thumb').'/'.$savedImage);
    }

    public function test_profile_picture_upload_validation_error()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('account.profile-pic.update'), [
            // No image
        ]);

        $response->assertJson([
            "status" => false,
        ]);

        $response->assertJsonValidationErrors(['image']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('account.logout'));
        $this->assertGuest();
        $response->assertRedirect(route('account.login'));

    }
}
