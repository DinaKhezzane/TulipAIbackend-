<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Change this line
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

class Employee extends Authenticatable implements CanResetPassword // Implement CanResetPassword
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number', // Ensure this matches the field name in the database
        'password',
        'company_id',
        'role_id',
        'status', // Include the status field
        'token',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
