<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Import Carbon for date handling

class Token extends Model
{
    protected $fillable = [
        'manager_id',
        'employee_id',
        'token',
        'expires_at', // Make sure to include this in fillable
    ];

    // Relationship with Manager
    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Create a new token and set the expiration time
    public static function createToken($managerId = null, $employeeId = null)
    {
        $token = new self();
        $token->token = bin2hex(random_bytes(30)); // Generate a random token
        $token->manager_id = $managerId;
        $token->employee_id = $employeeId;

        // Set expiration time to 24 hours from now
        $token->expires_at = Carbon::now()->addHours(24); 

        $token->save(); // Save the token to the database

        return $token;
    }

    // Check if the token is expired
    public function isExpired()
    {
        return $this->expires_at < Carbon::now();
    }
}
