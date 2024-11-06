<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_users_with_valid_token()
    {
        $this->createRoles();
        $role = $this->getRole('admin');
        $password = 'password';
        $credentials = [
            'name' => 'Miftahul Ulum',
            'email' => 'miftahululum@mail.com',
            'password' => bcrypt($password),
            'role_id' => $role->id,
        ];
        $userCreate = User::create($credentials);
        $token = JWTAuth::fromUser($userCreate);
        $response = $this->getJson(route('api.users.index'), ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_get_users_with_expired_token()
    {
        $this->createRoles();
        $role = $this->getRole('admin');
        $password = 'password';
        $credentials = [
            'name' => 'Miftahul Ulum',
            'email' => 'miftahululum@mail.com',
            'password' => bcrypt($password),
            'role_id' => $role->id,
        ];
        $userCredentials = $credentials;
        unset($userCredentials['name']);
        $userCreate = User::create($credentials);
        $token = auth()->guard('api')->attempt($userCredentials, ['exp' => 0.01]);
        sleep(5);
        $response = $this->getJson(route('api.users.index'), ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_get_users_with_invalid_token()
    {
        $token = 'invalidtoken';
        $response = $this->getJson(route('api.users.index'), ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_get_users_without_token()
    {
        $response = $this->getJson(route('api.users.index'));
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
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
