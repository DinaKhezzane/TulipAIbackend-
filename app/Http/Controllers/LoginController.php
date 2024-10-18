<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\Employee;
use App\Models\Token;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate the request inputs (email and password)
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
    
        // Check if it's a manager login
        $manager = Manager::where('email', $credentials['email'])->first();
        if ($manager && Hash::check($credentials['password'], $manager->password)) {
            $token = Token::createToken($manager->id); // Create a token with an expiry
    
            return response()->json([
                'message' => 'Manager login successful',
                'token' => $token->token, // Return just the token string
                'expires_at' => $token->expires_at, // Optionally return expiry time
            ]);
        }
    
        // Check if it's an employee login
        $employee = Employee::where('email', $credentials['email'])->first();
        if ($employee && Hash::check($credentials['password'], $employee->password)) {
            $token = Token::createToken(null, $employee->id); // Create a token with an expiry
    
            return response()->json([
                'message' => 'Employee login successful',
                'token' => $token->token, // Return just the token string
                'expires_at' => $token->expires_at, // Optionally return expiry time
            ]);
        }
    
        // If login fails for both manager and employee
        return response()->json([
            'message' => 'Invalid login credentials'
        ], 401);
    }
    
}
