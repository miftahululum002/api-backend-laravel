<?php

namespace App\Http\Controllers;

abstract class Controller
{

    protected function responseApi($response, $statusCode)
    {
        return response()->json($response, $statusCode);
    }
}
