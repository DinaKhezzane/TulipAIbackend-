<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inflow extends Model
{
    use HasFactory;

    protected $table = 'inflows';

    protected $fillable = [
        'company_id',
        'inflow_category_id',
        'date',
        'amount',
        'revenue_name',
        'description',
    ];

    /**
     * Get the company that owns the inflow.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the inflow category associated with the inflow.
     */
    public function category()
    {
        return $this->belongsTo(InflowCategory::class, 'inflow_category_id');
    }
}
