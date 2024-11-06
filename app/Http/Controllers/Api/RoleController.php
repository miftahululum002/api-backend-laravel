<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * List
     * 
     * Get List Users
     * 
     * @param Request $request The request object.
     */

    public function index(Request $request)
    {
        $roles = cache('roles');
        $data = $roles;
        return $this->responseSuccess('Data role', $data, 200);
    }
}
