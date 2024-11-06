<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = JWTAuth::getToken();
        try {
            $removeToken = JWTAuth::invalidate($token);
            return $this->responseSuccess('Logout Berhasil!');
        } catch (Exception $e) {
            return $this->responseError('Logout Gagal!', $e->getMessage(), 400);
        }
    }
}
