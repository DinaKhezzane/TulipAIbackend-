<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'outflow_name',         // Add this if you are using `name` for the outflow
        'outflow_category_id',  // Ensure this is added for category ID
        'amount',
        'date',
        'description',
        'company_id',
    ];

    // Define relationships, if any, e.g., with OutflowCategory:
    public function category()
    {
        return $this->belongsTo(OutflowCategory::class, 'outflow_category_id');
    }
}

