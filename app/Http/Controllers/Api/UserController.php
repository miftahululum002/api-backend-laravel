<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * List
     * 
     * Get List Users
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = User::all();
        return $this->responseSuccess('Data user', $data, 200);
    }

    /**
     * Detail
     * 
     * Get detail user by id
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        return $this->responseSuccess('Detail Data User', $user, 200);
    }

    /**
     * Tambah
     * 
     * Tambah user baru
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email',
            'umur'      => 'nullable|numeric|min:1',
            'password'  => 'required|min:8|confirmed'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return $this->responseError('Tambah user gagal', $validator->errors(), 422);
        }
        $input = $validator->validated();
        $userData = $input;
        $userData['password'] = bcrypt($input['password']);
        try {
            $user = User::create($userData);
            return $this->responseSuccess('Data User Berhasil Disimpan!', $user, 201);
        } catch (Exception $e) {
            return $this->responseError('Data User Gagal Disimpan!', $e->getMessage(), 500);
        }
    }

    /**
     * Update
     * 
     * Update user by id
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            "umur"      => 'nullable|numeric|min:1',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|min:8'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return $this->responseError('Update user gagal', $validator->errors(), 422);
        }
        $input = $validator->validated();
        $userUpdate = $input;
        if ($request->password) {
            $userUpdate['password'] = bcrypt($input['password']);
        }
        try {
            $user->update($userUpdate);
            return $this->responseSuccess('Data User Berhasil Diupdate!', $user, 200);
        } catch (Exception $e) {
            return $this->responseError('Data User Gagal Diupdate!', $e->getMessage(), 500);
        }
    }

    /**
     * Delete
     * 
     * Delete user by id
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return $this->responseSuccess('Data User Berhasil Dihapus!', null, 200);
        } catch (Exception $e) {
            return $this->responseError('Data User Gagal Dihapus!', $e->getMessage(), 500);
        }
    }
}
