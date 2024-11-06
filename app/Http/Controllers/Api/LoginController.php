<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseError('Login gagal', $validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            $message = 'Email atau Password Anda salah';
            return $this->responseError($message, $message, 401);
        }
        return $this->responseSuccess('Login Berhasil!', ['token'   => $token], 200);
    }
}
