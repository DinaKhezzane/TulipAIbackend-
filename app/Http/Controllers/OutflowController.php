<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outflow;
use App\Models\OutflowCategory;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

use App\Services\DashboardMetricsService;

class OutflowController extends Controller
{

    protected $dashboardMetricsService;

    public function __construct(DashboardMetricsService $dashboardMetricsService)
    {
        $this->dashboardMetricsService = $dashboardMetricsService;
    }
    public function store(Request $request)
{
    try {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',  // Ensure category is required
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Retrieve token information from the request attributes (assuming token middleware parsed it)
        $tokenInfo = $request->attributes->get('tokenInfo');

        // Determine the company_id from the token info
        $company_id = null;
        if ($tokenInfo->manager_id) {
            // Fetch the company using the manager_id from the token
            $company = Company::where('manager_id', $tokenInfo->manager_id)->first();
        } elseif ($tokenInfo->employee_id) {
            // Fetch the company using the employee's company_id
            $company = Company::whereHas('employees', function ($query) use ($tokenInfo) {
                $query->where('id', $tokenInfo->employee_id);
            })->first();
        }

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Find the OutflowCategory by name
        $outflowCategory = OutflowCategory::where('name', $request->category)->first();

        if (!$outflowCategory) {
            return response()->json(['message' => 'Category not found'], 404);  // Return if category is not found
        }

        // Log or print the category ID for debugging purposes
        Log::info('Outflow Category ID: ' . $outflowCategory->id); // Log the category ID
        // Alternatively, use dd() to stop the code execution and display the ID for debugging:
        // dd($outflowCategory->id);

        // Ensure that outflow_category_id exists and is not null
        if (!$outflowCategory->id) {
            return response()->json(['message' => 'Category ID is missing or invalid'], 400);
        }

        // Store the outflow record
        $outflow = Outflow::create([
            'outflow_name' => $request->name,
            'outflow_category_id' => $outflowCategory->id,  // Use the retrieved category ID
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
            'company_id' => $company->id,
        ]);

        $this->dashboardMetricsService->calculateMetrics($outflow->company_id);

        return response()->json(['message' => 'Outflow added successfully!', 'outflow' => $outflow], 201);
    } catch (\Exception $e) {
        Log::error('Error storing outflow: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to add outflow', 'error' => $e->getMessage()], 500);
    }
}

    



    public function getOutflowCategories()
    {
        try {
            // Fetch all outflow categories
            $categories = OutflowCategory::all();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch categories'], 500);
        }
    }

    public function getAllOutflows(Request $request)
    {
        try {
            // Retrieve token information from the request attributes
            $tokenInfo = $request->attributes->get('tokenInfo');

            // Determine the company_id from the token info
            $company = null;
            if ($tokenInfo->manager_id) {
                $company = Company::where('manager_id', $tokenInfo->manager_id)->first();
            } elseif ($tokenInfo->employee_id) {
                $company = Company::whereHas('employees', function ($query) use ($tokenInfo) {
                    $query->where('id', $tokenInfo->employee_id);
                })->first();
            }

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            // Fetch all outflows for the company
            $outflows = Outflow::where('company_id', $company->id)
                        ->with('category') // Assuming there's a relationship defined for categories
                        ->get();

            return response()->json(['outflows' => $outflows], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching outflows: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch outflows', 'error' => $e->getMessage()], 500);
        }
    }
}
