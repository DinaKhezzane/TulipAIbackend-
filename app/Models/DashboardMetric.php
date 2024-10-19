<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardMetric extends Model
{
    use HasFactory;

    protected $table = 'dashboard_metrics';

    protected $fillable = [
        'company_id',
        'working_capital',
        'leverage',
        'quick_ratio',
    ];

    // Define any relationships if needed
}
