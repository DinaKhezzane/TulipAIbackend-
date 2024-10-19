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
            $inviteLink = URL::to('/signup') . '?token=' . $token;

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
}
