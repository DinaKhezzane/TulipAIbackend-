<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    protected $table = 'income_categories';

    // A category can have many income entries
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
}
