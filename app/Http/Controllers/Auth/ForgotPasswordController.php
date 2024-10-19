<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Notifications\ResetPasswordNotification; 
use Illuminate\Support\Facades\Notification;
use App\Models\Manager; 
use App\Models\Employee; 

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
    
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    
        // Try to find the user in the Manager table
        $user = Manager::where('email', $request->email)->first();
    
        // If not found, try to find the user in the Employee table
        if (!$user) {
            $user = Employee::where('email', $request->email)->first();
        }
    
        // If the user is not found in both tables, return an error response
        if (!$user) {
            return response()->json(['message' => 'No user found with this email address.'], 404);
        }

        // Create the password reset token
        $token = Password::createToken($user);
    
        // Send the password reset notification
        Notification::send($user, new ResetPasswordNotification($token));
    
        return response()->json(['message' => 'Password reset link sent!'], 200);
    }
}
