<?php

namespace App\Services;

use App\Models\Inflow;
use App\Models\Outflow;
use App\Models\DashboardMetric;

class DashboardMetricsService
{
    public function calculateMetrics($companyId)
    {
        // Calculate total inflows and outflows
        $totalInflows = Inflow::where('company_id', $companyId)->sum('amount');
        $totalOutflows = Outflow::where('company_id', $companyId)->sum('amount');

        // Calculate working capital
        $workingCapital = $totalInflows - $totalOutflows;

        // Assuming leverage is calculated as Total Outflows / Total Inflows
        $leverage = $totalInflows ? ($totalOutflows / $totalInflows) * 100 : 0;

        // Calculate quick ratio
        $quickRatio = $totalInflows ? $totalInflows / ($totalOutflows ?: 1) : 0;

        // Update or create dashboard metrics for the company
        DashboardMetric::updateOrCreate(
            ['company_id' => $companyId],
            [
                'working_capital' => $workingCapital,
                'leverage' => $leverage,
                'quick_ratio' => $quickRatio,
            ]
        );
    }
}
