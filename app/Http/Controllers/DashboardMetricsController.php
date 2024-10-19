<?php

namespace App\Http\Controllers;

use App\Models\DashboardMetric;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardMetricsController extends Controller
{
    /**
     * Get the dashboard metrics for the logged-in company.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Retrieve token information from the request attributes
            $tokenInfo = $request->attributes->get('tokenInfo');

            // Determine the company_id from the token info
            $company = null;

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

            // Retrieve the dashboard metrics for the identified company
            $metrics = DashboardMetric::where('company_id', $company->id)->first();

            if (!$metrics) {
                return response()->json(['message' => 'Metrics not found for this company'], 404);
            }

            return response()->json(['metrics' => $metrics], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving dashboard metrics: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve dashboard metrics', 'error' => $e->getMessage()], 500);
        }
    }
}
