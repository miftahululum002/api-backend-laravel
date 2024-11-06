<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    public function __invoke(Request $request)
    {
        //make rule for validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'umur'      => 'nullable|numeric|min:1',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8|confirmed'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return $this->responseError('Register gagal', $validator->errors(), 422);
        }
        // get all validate data
        $input = $validator->validated();
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
