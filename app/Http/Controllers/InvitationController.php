<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company; // Import Company model
use App\Models\Role; // Import Role model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Facades\Validator; // For validation

class InvitationController extends BaseController
{
    public function inviteEmployee(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'email' => 'required|email|unique:employees,email',
                'role' => 'required|string', // Ensure the role is a string
            ]);

            // Retrieve token info from the request attributes
            $tokenInfo = $request->attributes->get('tokenInfo');

            // Get the company_id using manager_id from tokenInfo
            $company_id = null;
            if ($tokenInfo->manager_id) {
                // Fetch the company using manager_id
                $company = Company::where('manager_id', $tokenInfo->manager_id)->first();
                $company_id = $company ? $company->id : null; // Get company_id if company exists
            }

            // Retrieve the role_id from the role_name
            $role = Role::where('role_name', $request->role)->first();
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }

            // Generate a unique token for the invite link
            $token = Str::random(32);
            $inviteLink = 'http://localhost:3000/employee_signup/' . $token;


            // Extract name from the email (prefix before the '@')
            $name = explode('@', $request->email)[0]; // Get the prefix of the email

            // Create a temporary employee record
            $temporaryEmployee = Employee::create([
                'email' => $request->email,
                'role_id' => $role->id, // Use the retrieved role_id
                'status' => 'invited',
                'token' => $token,
                'company_id' => $company_id,
                'name' => $name, // Set name from the email prefix
            ]);

            // Send the invitation email
            Mail::send('emails.invite', ['link' => $inviteLink], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('You are invited to join our platform!');
            });

            return response()->json(['message' => 'Invitation sent successfully!'], 200);
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send invitation'], 500);
        }
    }

    public function getCompanyInfo($token)
{
    // Retrieve employee info based on the token
    $employee = Employee::where('token', $token)->first();
    
    if (!$employee) {
        return response()->json(['message' => 'Invalid token.'], 404);
    }

    // Fetch the company using company_id from employee
    $company = $employee->company;

    // If no company is found, return an error message
    if (!$company) {
        return response()->json(['message' => 'Company not found.'], 404);
    }

    
    // Return the company, manager info, and list of employees
    return response()->json([
        'companyName' => $company->org_name, // Assuming 'org_name' is the company's name
        'managerName' => $company->manager ? $company->manager->name : 'Not Found', // Assuming manager relationship exists
        'id' => $employee->id,
        'name' => $employee->name,
        'email' => $employee->email,
        
    ]);
}

public function storeEmployee(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:15',
                'password' => 'nullable|string|min:8', // Password can be null
            ]);

            // Return validation errors if any
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Attempt to find the employee by email
            $employee = Employee::where('email', $request->input('email'))->first();

            // Check if the employee exists
            if ($employee) {
                // Update the employee's phone number and status
                $employee->phone_number = $request->input('phone');
                $employee->status = 'active'; // Ensure the status is updated
                // Update password only if provided
                if ($request->filled('password')) {
                    $employee->password = Hash::make($request->input('password')); // Hash the password if provided
                }
                $employee->save();

                return response()->json([
                    'message' => 'Employee record updated successfully.',
                    'employee' => $employee,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Employee not found.',
                ], 404);
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating employee record: ' . $e->getMessage());

            return response()->json([
                'error' => 'An error occurred while updating the employee record. Please try again later.',
            ], 500);
        }
    }

}
