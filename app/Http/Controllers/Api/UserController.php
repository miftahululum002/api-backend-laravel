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
     * Mendapatkan semua daftar user
     * @param Request $request The request object.
     */

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Page query.
            'page' => 'nullable|numeric',
            //jumlah data per page data yang ditampilkan.
            'per_page' => 'nullable|integer'
        ]);
        $page = $request->page;
        $page = isset($page) ? $page : 1;
        $perPage = $request->per_page;
        $perPage = !empty($perPage) ? $perPage : 10;
        $roles = cache('roles');
        $data = User::limit($perPage)->offset(($page - 1) * $perPage)->orderBy('created_at', 'ASC')->get();
        if ($data) {
            foreach ($data as $d => $da) {
                $role = $roles->where('id', $da->role_id)->first();
                if ($role) {
                    $data[$d]->role = $role->name;
                }
            }
        }
        return $this->responseSuccess('Data user', $data, 200);
    }

    /**
     * Detail
     * 
     * Get detail user by id
     * 
     * Mendapatkan detail user berdasarkan id
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $id)
    {
        if (empty($id)) {
            return $this->responseError('Detail Data User Gagal', 'ID tidak boleh kosong', 400);
        }
        $user = User::find($id);
        if ($user) {
            return $this->responseSuccess('Detail Data User', $user, 200);
        }
        return $this->responseError('Detail Data User Gagal', 'Data tidak ditemukan', 404);
    }

    /**
     * Tambah
     * 
     * Tambah user baru
     * 
     * Menambahkan user baru ke dalam sistem
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email|max:255',
            // id dari roles cek end point Role List
            'role_id'   => 'required|exists:roles,id',
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
     * Mengupdate data user berdasarkan id
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            "umur"      => 'nullable|numeric|min:1',
            'role_id'   => 'nullable|exists:roles,id',
            'email'     => 'required|email|unique:users,email,' . $id->id . '|max:255',
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
            $id->update($userUpdate);
            return $this->responseSuccess('Data User Berhasil Diupdate!', $id, 200);
        } catch (Exception $e) {
            return $this->responseError('Data User Gagal Diupdate!', $e->getMessage(), 500);
        }
    }

    /**
     * Delete
     * 
     * Delete user by id
     * 
     * Menghapus data user berdasarkan id
     * 
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $id)
    {
        try {
            $id->delete();
            return $this->responseSuccess('Data user berhasil dihapus!', null, 200);
        } catch (Exception $e) {
            return $this->responseError('Data User Gagal Dihapus!', $e->getMessage(), 500);
        }
    }
}
