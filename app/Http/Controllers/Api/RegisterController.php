<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    /**
     * Register
     * 
     * Register
     * 
     * Digunakan untuk register user baru.
     *
     * @unauthenticated
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        //make rule for validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'umur'      => 'nullable|numeric|min:1',
            'email'     => 'required|email|unique:users,email|max:255',
            'password'  => 'required|min:8|confirmed'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return $this->responseError('Register gagal', $validator->errors(), 422);
        }
        // get all validate data
        $input = $validator->validated();
        $input['role_id'] = Role::where('name', 'admin')->first()->id;
        $userData = $input;
        $userData['password'] = bcrypt($input['password']);
        try {
            $user = User::create($userData);
            return $this->responseSuccess('Register Berhasil!', ['user' => $user], 201);
        } catch (Exception $e) {
            return $this->responseError('Register Gagal!', $e->getMessage(), 500);
        }
    }
}
