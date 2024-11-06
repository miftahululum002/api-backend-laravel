<?php

namespace App\Http\Controllers;

abstract class Controller
{

    protected function responseSuccess($response, $statusCode = 200)
    {
        $response['success'] = true;
        return $this->responseApi($response, $statusCode);
    }

    protected function responseError($response, $statusCode = 400)
    {
        $response['success'] = false;
        return $this->responseApi($response, $statusCode);
    }

    private function responseApi($response, $statusCode)
    {
        return response()->json($response, $statusCode);
    }
}
