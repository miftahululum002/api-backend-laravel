<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_delete_user_by_id()
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

        $roleUser = $this->getRole('user');
        $createLagi = User::create([
            'name' => 'Lagi',
            'email' => 'oke@mail.com',
            'password' => bcrypt('password'),
            'role_id' => $roleUser->id,
        ]);
        $response = $this->deleteJson(route('api.users.destroy', $createLagi->id), [], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'error',
            'data',
            'error',
        ]);
    }

    public function test_cannot_delete_user_without_existing_id()
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

        $roleUser = $this->getRole('user');
        $createLagi = User::create([
            'name' => 'Lagi',
            'email' => 'oke@mail.com',
            'password' => bcrypt('password'),
            'role_id' => $roleUser->id,
        ]);
        $response = $this->deleteJson(route('api.users.destroy', 100), [], ['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(404);
        // $response->assertJsonStructure([
        //     'success',
        //     'error',
        //     'data',
        //     'error',
        // ]);
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
