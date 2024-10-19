<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'org_name',
        'category_id',
        'description',
        'company_logo',
    ];

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    public function employees() // Define the relationship for employees
    {
        return $this->hasMany(Employee::class);
    }
}
