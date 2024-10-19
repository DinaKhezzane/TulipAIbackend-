<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'income';

    // An income entry belongs to an income category
    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class);
    }
}
