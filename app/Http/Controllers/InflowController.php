<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inflow;
use App\Models\InflowCategory;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use App\Services\DashboardMetricsService;

class InflowController extends Controller
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
                'category' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'description' => 'nullable|string',
            ]);

            // Retrieve token information from the request attributes
            $tokenInfo = $request->attributes->get('tokenInfo');

            // Determine the company_id from the token info
            $company = $this->getCompanyFromToken($tokenInfo);

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            // Find the InflowCategory by name
            $inflowCategory = InflowCategory::where('name', $request->category)->first();

            if (!$inflowCategory) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            // Store the inflow record
            $inflow = Inflow::create([
                'revenue_name' => $request->name, // Updated to use the correct field
                'inflow_category_id' => $inflowCategory->id,
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => $request->description,
                'company_id' => $company->id,
            ]);

            $this->dashboardMetricsService->calculateMetrics($inflow->company_id);

            return response()->json(['message' => 'Inflow added successfully!', 'inflow' => $inflow], 201);
        } catch (\Exception $e) {
            Log::error('Error storing inflow: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to add inflow', 'error' => $e->getMessage()], 500);
        }
    }

    public function getInflowCategories()
    {
        try {
            // Fetch all inflow categories
            $categories = InflowCategory::all();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch categories'], 500);
        }
    }

    public function getAllInflows(Request $request)
    {
        try {
            // Retrieve token information from the request attributes
            $tokenInfo = $request->attributes->get('tokenInfo');

            // Determine the company_id from the token info
            $company = $this->getCompanyFromToken($tokenInfo);

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            // Fetch all inflows for the company
            $inflows = Inflow::where('company_id', $company->id)
                ->with('category') // Assuming there's a relationship defined for categories
                ->get();

            return response()->json(['inflows' => $inflows], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching inflows: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch inflows', 'error' => $e->getMessage()], 500);
        }
    }

    private function getCompanyFromToken($tokenInfo)
    {
        if ($tokenInfo->manager_id) {
            return Company::where('manager_id', $tokenInfo->manager_id)->first();
        } elseif ($tokenInfo->employee_id) {
            return Company::whereHas('employees', function ($query) use ($tokenInfo) {
                $query->where('id', $tokenInfo->employee_id);
            })->first();
        }
        return null;
    }
}
