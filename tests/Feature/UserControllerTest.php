<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user index page.
     *
     * @return void
     */
    public function test_user_index()
    {
        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    /**
     * Test user create page.
     *
     * @return void
     */
    public function test_user_create_page()
    {
        $response = $this->get('/users/create');

        $response->assertStatus(200);
        $response->assertViewIs('users.create');
    }

    /**
     * Test store user.
     *
     * @return void
     */
    public function test_store_user()
    {
        Storage::fake('users');

        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'image' => UploadedFile::fake()->image('avatar.jpg')
        ];

        $response = $this->post('/users', $data);

        $response->assertRedirect(route('user.index'));
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }

    /**
     * Test edit user page.
     *
     * @return void
     */
    public function test_edit_user_page()
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas('user', $user);
    }

    /**
     * Test update user.
     *
     * @return void
     */
    public function test_update_user()
    {
        Storage::fake('users');

        $user = User::factory()->create();
        $data = [
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'image' => UploadedFile::fake()->image('avatar.jpg')
        ];

        $response = $this->put("/users/{$user->id}", $data);

        $response->assertRedirect(route('user.index'));
        $this->assertDatabaseHas('users', ['email' => 'janedoe@example.com']);
    }

    /**
     * Test delete user.
     *
     * @return void
     */
    public function test_delete_user()
    {
        Storage::fake('users');

        $user = User::factory()->create();
        $user->image = UploadedFile::fake()->image('avatar.jpg')->store('users');
        $user->save();

        $response = $this->delete("/users/{$user->id}");

        $response->assertRedirect(route('user.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
