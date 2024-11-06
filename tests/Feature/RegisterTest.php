<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $this->createRoles();
        $role = $this->getRole('admin');
        // send json post request to register a new user
        $response = $this->postJson(route('api.register'), [
            'name' => 'Test User',
            'email' => 'emaillain@mail.com',
            // 'role_id' => $role->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // assert that the response is a 201 with a json structure containing an access token and a token type
        $response->assertStatus(201);
        // ->assertJsonStructure([
        //     'success',
        //     'data',
        // ]);

        // assert that the user was created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'emaillain@mail.com',
        ]);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        $this->createRoles();
        // create a user with factory
        $role = $this->getRole('admin');
        User::factory()->create([
            'email' => 'miftahul@mail.com',
            'role_id' => $role->id,
        ]);

        // send json post request to register a new user with an existing email
        $response = $this->postJson(route('api.register'), [
            'name' => 'Another User',
            'email' => 'miftahul@mail.com',
            'role_id' => $role->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // assert that the response is a 422 with json validation errors for the email
        $response->assertStatus(422);
        // ->assertJsonValidationErrors(['error']['messages']['email']);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        // send json post request to register a new user with invalid type data
        $response = $this->postJson(route('api.register'), [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'role_id' => 1,
            'password_confirmation' => 'different',
        ]);

        // assert that the response is a 422 with json validation errors for the name, email, and password
        $response->assertStatus(422);
        // ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_cannot_register_without_data()
    {
        // send json post request to register a new user with invalid type data
        $response = $this->postJson(route('api.register'), []);

        // assert that the response is a 422 with json validation errors for the name, email, and password
        $response->assertStatus(422);
        // ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    private function createRoles()
    {
        $lists = [
            ['name' => 'admin',],
            ['name' => 'user',],
        ];
        return Role::insert($lists);
    }

    private function getRole($name)
    {
        return Role::where('name', $name)->first();
    }
}
