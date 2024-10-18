<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['role_name', 'description'];

    // A role can be assigned to many employees
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
