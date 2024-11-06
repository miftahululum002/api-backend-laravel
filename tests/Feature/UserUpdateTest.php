<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_user_with_valid_data()
    {
        $this->createRoles();
        $role = $this->getRole('admin');
        // untuk mendapatkan token
        $password = 'password';
        $credentials = [
            'name' => 'Miftahul Ulum',
            'role_id' => $role->id,
            'email' => 'miftahululum@mail.com',
            'password' => bcrypt($password),
        ];
        $userCreate = User::create($credentials);
        $token = JWTAuth::fromUser($userCreate);

        $createLagi = User::create([
            'name' => 'Lagi',
            'email' => 'oke@mail.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);
        $roleUser = $this->getRole('user');
        $response = $this->putJson(route('api.users.update', $createLagi->id), [
            'name' => 'Masuk',
            'email' => 'oke@mail.com',
            'umur' => 30,
            'role_id' => $roleUser->id,
        ], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_cannot_update_user_with_other_existing_email()
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

        $createLagi = User::create([
            'name' => 'Lagi',
            'email' => 'oke@mail.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);
        $userRole = $this->getRole('user');
        $response = $this->putJson(route('api.users.update', $createLagi->id), [
            'name' => 'Masuk',
            'email' => 'miftahululum@mail.com',
            'umur' => 30,
            'role_id' => $userRole->id,
        ], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_cannot_update_user_with_invalid_field()
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

        $createLagi = User::create([
            'name' => 'Lagi',
            'email' => 'oke@mail.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);
        $response = $this->putJson(route('api.users.update', $createLagi->id), [
            'name' => '',
            'email' => '',
            'umur' => 30,
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
