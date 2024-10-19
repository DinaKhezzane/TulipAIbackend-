<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Fetch all roles
        $roles = Role::all(['role_name', 'description']);

        // Return the roles as JSON
        return response()->json($roles);
    }
}
