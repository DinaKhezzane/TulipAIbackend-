<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Import the Log facade

class ManagerController extends Controller
{
    public function create(Request $request)
{
    try {
        // Log the incoming request data
        Log::info('Incoming request data:', $request->all());

        // Validate request data
        $request->validate([
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|string|email|max:255|unique:managers,email',
            'user.password' => 'required|string|min:8',
            'organization.orgName' => 'required|string|max:255',
            'organization.category' => 'required', // Accept category as string
            'organization.description' => 'required|string',
        ]);

        // Create the manager
        $manager = Manager::create([
            'name' => $request->user['name'],
            'email' => $request->user['email'],
            'password' => Hash::make($request->user['password']),
            'profile_picture' => null,
            'phone_number' => null,
        ]);

        // Log company creation data
        Log::info('Creating company with data:', [
            'manager_id' => $manager->id,
            'org_name' => $request->organization['orgName'],
            'category_id' => (int)$request->organization['category'], // Log category as integer
            'description' => $request->organization['description'],
        ]);

        // Create the company
        Company::create([
            'manager_id' => $manager->id,
            'org_name' => $request->organization['orgName'],
            'category_id' => (int)$request->organization['category'], // Ensure category is an integer
            'description' => $request->organization['description'],
            'company_logo' => null,
        ]);

        return response()->json(['message' => 'Manager and Company created successfully'], 201);
    } catch (ValidationException $e) {
        // Log validation errors
        Log::error('Validation failed:', $e->validator->errors()->toArray());
        return response()->json(['errors' => $e->validator->errors()], 422);
    } catch (\Exception $e) {
        // Log detailed error message
        Log::error('Error while creating manager and company:', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return response()->json([
            'error' => 'An error occurred while creating the manager and company.',
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
}

}
