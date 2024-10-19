<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutflowCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Define any relationships if necessary, e.g.:
    // public function expenses() {
    //     return $this->hasMany(Expense::class);
    // }
}
