<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials()
    {
        $this->createRoles();
        // create a user with factory
        $role = Role::where('name', 'admin')->first();
        User::factory()->create([
            'email' => 'miftahululum@mail.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        // send json post request to login with correct credentials
        $response = $this->postJson(route('api.login'), [
            'email' => 'miftahululum@mail.com',
            'password' => 'password',
        ]);

        // assert that the response is a 200 with a json structure containing an access token and a token type
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'error',
                'data' => ['token'],
            ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $this->createRoles();
        // create a user with factory
        $role = Role::where('name', 'admin')->first();
        User::create([
            'name' => 'Oke',
            'email' => 'miftahululum@mail.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        // send json post request to login with incorrect credentials
        $response = $this->postJson(route('api.login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // assert that the response is a 401 with a json structure containing an message
        $response->assertStatus(401);
        // ->assertJson([
        //     'message' => 'The provided credentials are incorrect.'
        // ]);
    }

    public function test_login_validation_fails_with_missing_fields()
    {
        // send json post request to login with empty array
        $response = $this->postJson(route('api.login'), []);

        // assert that the response is a 422 with json validation errors for the email and password
        $response->assertStatus(422);
        // ->assertJsonValidationErrors(['email', 'password']);
    }

    private function createRoles()
    {
        $lists = [
            ['name' => 'admin',],
            ['name' => 'user',],
        ];
        Role::insert($lists);
        return;
    }
}
