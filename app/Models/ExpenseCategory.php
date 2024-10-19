<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';

    // A category can have many expense entries
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
