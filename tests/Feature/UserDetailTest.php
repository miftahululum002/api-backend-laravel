<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_detail_user_by_id()
    {
        $this->createRoles();
        // untuk mendapatkan token
        $password = 'password';
        $role = $this->getRole('user');
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
        $response = $this->getJson(route('api.users.show', $createLagi->id), ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_can_get_detail_user_by_id_without_existing_id()
    {
        $this->createRoles();
        $role = $this->getRole('user');
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
        $response = $this->getJson(route('api.users.show', [6]), ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(404);
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
