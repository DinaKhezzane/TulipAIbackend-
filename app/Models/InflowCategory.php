<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InflowCategory extends Model
{
    use HasFactory;

    protected $table = 'inflow_categories';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the inflows associated with the inflow category.
     */
    public function inflows()
    {
        return $this->hasMany(Inflow::class);
    }
}
