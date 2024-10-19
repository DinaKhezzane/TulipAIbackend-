<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Manager;

class UserProfileController extends Controller
{
    
    public function getUserProfile(Request $request)
    {
        // Assuming tokenInfo contains the authenticated user ID
        $tokenInfo = $request->attributes->get('tokenInfo');
        $managerId = $tokenInfo->manager_id; // This should be the ID of the manager
        $employeeId = $tokenInfo->employee_id; // This should be the ID of the employee
    
        // Check if the user is a Manager or Employee
        if ($managerId) {
            // Fetch manager information
            $manager = Manager::find($managerId);
            if ($manager) {
                return response()->json([
                    'name' => $manager->name,
                    'email' => $manager->email,
                    'role' => 'manager',
                    'avatar' => $manager->profile_picture,
                ]);
            }
        }
    
        if ($employeeId) {
            // Fetch employee information
            $employee = Employee::find($employeeId);
            if ($employee) {
                return response()->json([
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'role' => 'employee',
                    'avatar' => $employee->profile_picture, // If you want to include an avatar for employees too
                ]);
            }
        }
    
        return response()->json(['message' => 'User not found'], 404);
    }
    
}
