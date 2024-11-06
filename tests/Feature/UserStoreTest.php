<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_user_with_valid_data()
    {
        $this->createRoles();
        $role = $this->getRole('admin');
        // untuk mendapatkan token
        $password = 'password';
        $credentials = [
            'name' => 'Miftahul Ulum',
            'email' => 'miftahululum@mail.com',
            'password' => bcrypt($password),
            'role_id' => $role->id,
        ];
        $userCreate = User::create($credentials);
        $token = JWTAuth::fromUser($userCreate);

        $roleUser = $this->getRole('user');
        $response = $this->postJson(route('api.users.store'), [
            'name' => 'Ulum Miftahul',
            'email' => 'test@mail.com',
            'umur' => 20,
            'role_id' => $roleUser->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_cannot_store_user_with_existing_email()
    {
        $this->createRoles();
        $role = $this->getRole('admin');
        // untuk mendapatkan token
        $password = 'password';
        $credentials = [
            'name' => 'Miftahul Ulum',
            'email' => 'miftahululum@mail.com',
            'password' => bcrypt($password),
            'role_id' => $role->id,
        ];
        $userCreate = User::create($credentials);
        $token = JWTAuth::fromUser($userCreate);

        $roleUser = $this->getRole('user');
        $response = $this->postJson(route('api.users.store'), [
            'name' => 'Ulum Miftahul',
            'email' => 'miftahululum@mail.com',
            'role_id' => $roleUser->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_cannot_store_user_with_invalid_data()
    {
        $this->createRoles();
        $role = $this->getRole('admin');
        // untuk mendapatkan token
        $password = 'password';
        $credentials = [
            'name' => 'Miftahul Ulum',
            'email' => 'miftahululum@mail.com',
            'password' => bcrypt($password),
            'role_id' => $role->id,
        ];
        $userCreate = User::create($credentials);
        $token = JWTAuth::fromUser($userCreate);

        // $roleUser = $this->getRole('user');
        $response = $this->postJson(route('api.users.store'), [
            'name' => '',
            'email' => 'bukan-email',
            'role_id' => 'bukan-integer',
            'password' => 'password',
            'password_confirmation' => 'tidak sama',
        ], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_cannot_store_user_with_umur_invalid()
    {
        $this->createRoles();
        $role = $this->getRole('admin');
        // untuk mendapatkan token
        $password = 'password';
        $credentials = [
            'name' => 'Miftahul Ulum',
            'email' => 'miftahululum@mail.com',
            'role_id' => $role->id,
            'password' => bcrypt($password),
        ];
        $userCreate = User::create($credentials);
        $token = JWTAuth::fromUser($userCreate);

        $roleUser = $this->getRole('user');
        $response = $this->postJson(route('api.users.store'), [
            'name' => 'Ulum Miftahul',
            'email' => 'other@mail.com',
            'umur' => 'huru',
            'role_id' => $roleUser->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(422);
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
