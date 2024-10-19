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
        'phone_number',
        'company_id',
        'role_id',
        'status',
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
