<?php

namespace App\Http\Controllers;

use App\Models\Inflow;
use App\Models\Outflow;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CashFlowController extends Controller
{
    public function getCashFlowData(Request $request)
{
    // Fetch token information
    $tokenInfo = $request->attributes->get('tokenInfo');

    // Initialize company ID
    $companyId = null;

    // Check if the user is a manager or an employee and fetch the company
    if ($tokenInfo->manager_id) {
        // Fetch the company using the manager_id from the token
        $company = Company::where('manager_id', $tokenInfo->manager_id)->first();
        $companyId = $company ? $company->id : null;
    } elseif ($tokenInfo->employee_id) {
        // Fetch the company using the employee's company_id
        $company = Company::whereHas('employees', function ($query) use ($tokenInfo) {
            $query->where('id', $tokenInfo->employee_id);
        })->first();
        $companyId = $company ? $company->id : null;
    }

    // Check if company ID was found
    if (!$companyId) {
        return response()->json(['error' => 'Company not found.'], 404);
    }

    // Get the date from the request (you can set a default if needed)
    $date = $request->input('date'); // Expect format: 'Y-m' (e.g., '2024-04')

    // Fetch inflow and outflow data
    $inflows = Inflow::where('company_id', $companyId)
        ->where('date', 'like', "$date%")
        ->get();

    $outflows = Outflow::where('company_id', $companyId)
        ->where('date', 'like', "$date%")
        ->get();

    

    // Prepare data for the chart
    $inflowData = [];
    $outflowData = [];

    foreach ($inflows as $inflow) {
        $dateKey = date('Y-m-d', strtotime($inflow->date));
        $inflowData[$dateKey] = $inflow->amount;
    }

    foreach ($outflows as $outflow) {
        $dateKey = date('Y-m-d', strtotime($outflow->date));
        $outflowData[$dateKey] = $outflow->amount;
    }

    // Create time series data for the line chart
    $dates = array_unique(array_merge(array_keys($inflowData), array_keys($outflowData)));
    sort($dates); // Sort the dates

    $chartData = [
        'dates' => $dates,
        'inflow' => [],
        'outflow' => [],
    ];

    foreach ($dates as $date) {
        $chartData['inflow'][] = $inflowData[$date] ?? 0; // Default to 0 if no inflow
        $chartData['outflow'][] = $outflowData[$date] ?? 0; // Default to 0 if no outflow
    }

    return response()->json($chartData);
}

public function getInflowsByCategories(Request $request)
{
    $tokenInfo = $request->attributes->get('tokenInfo');

    // Initialize company ID
    $companyId = null;

    if ($tokenInfo->manager_id) {
        $company = Company::where('manager_id', $tokenInfo->manager_id)->first();
        $companyId = $company ? $company->id : null;
    } elseif ($tokenInfo->employee_id) {
        $company = Company::whereHas('employees', function ($query) use ($tokenInfo) {
            $query->where('id', $tokenInfo->employee_id);
        })->first();
        $companyId = $company ? $company->id : null;
    }

    if (!$companyId) {
        return response()->json(['error' => 'Company not found.'], 404);
    }

    // Get the current date and past 6 months
    $currentDate = now();
    $months = [];

    for ($i = 0; $i < 7; $i++) {
        $months[] = $currentDate->copy()->subMonths($i)->format('Y-m');
    }

    // Query inflows aggregated by month and category
    $inflows = Inflow::select('inflow_category_id', DB::raw('SUM(amount) as total_amount'), DB::raw('DATE_FORMAT(date, "%Y-%m") as month'))
        ->where('company_id', $companyId)
        ->whereIn(DB::raw('DATE_FORMAT(date, "%Y-%m")'), $months)
        ->groupBy('inflow_category_id', 'month')
        ->with('category') // Assuming there is a relationship defined in your model
        ->get();

    // Prepare the response data in the desired format
    $categories = [];
    foreach ($inflows as $inflow) {
        // Initialize category if not already set
        if (!isset($categories[$inflow->inflow_category_id])) {
            $categories[$inflow->inflow_category_id] = [
                'id' => $inflow->category->name, // Set category name as id
                'label' => $inflow->category->name, // Set category name as label
                'data' => array_fill(0, 7, 0) // Initialize data array with zeros for each month
            ];
        }

        // Map month to index and set total amount
        $monthIndex = array_search($inflow->month, $months);
        if ($monthIndex !== false) {
            $categories[$inflow->inflow_category_id]['data'][$monthIndex] = $inflow->total_amount; // Assign total amount to the corresponding month
        }
    }

    // Convert the associative array to a numerical indexed array
    $responseData = array_values($categories);

    return response()->json($responseData);
}

public function getOutflowsByCategories(Request $request)
{
    // Retrieve token information (for security and company identification)
    $tokenInfo = $request->attributes->get('tokenInfo');

    // Initialize company ID
    $companyId = null;

    // Identify company based on token information (either manager or employee)
    if ($tokenInfo->manager_id) {
        $company = Company::where('manager_id', $tokenInfo->manager_id)->first();
        $companyId = $company ? $company->id : null;
    } elseif ($tokenInfo->employee_id) {
        $company = Company::whereHas('employees', function ($query) use ($tokenInfo) {
            $query->where('id', $tokenInfo->employee_id);
        })->first();
        $companyId = $company ? $company->id : null;
    }

    // Return error if company not found
    if (!$companyId) {
        return response()->json(['error' => 'Company not found.'], 404);
    }

    // Query outflows aggregated by category
    $outflows = Outflow::select('outflow_category_id', DB::raw('SUM(amount) as total_amount'))
        ->where('company_id', $companyId)
        ->groupBy('outflow_category_id')
        ->with('category')  // Assuming a relationship exists with the category
        ->get();

    // Prepare the response data for the pie chart
    $categories = $outflows->map(function ($outflow) {
        return [
            'label' => $outflow->category->name, // Category name
            'value' => (float) $outflow->total_amount // Total amount for this category, cast to float
        ];
    });

    return response()->json($categories);
}




}
