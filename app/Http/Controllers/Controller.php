<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Facades\Cache;

abstract class Controller
{
    public function __construct()
    {
        Cache::remember('roles', 60, function () {
            $roles = Role::all();
            return $roles;
        });
    }

    protected function responseSuccess($message, $data = null, $statusCode = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'error' => null,
            'data' => $data,
        ];
        return $this->responseApi($response, $statusCode);
    }

    protected function responseError($message, $error = null, $statusCode = 400)
    {
        $response = [
            'success'   => false,
            'message'   => $message,
            'error'     => $error,
            'data'      => null,
        ];
        return $this->responseApi($response, $statusCode);
    }

    private function responseApi($response, $statusCode)
    {
        return response()->json($response, $statusCode);
    }
}
