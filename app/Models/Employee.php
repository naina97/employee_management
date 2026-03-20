<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Department;

class Employee extends Model
{

    protected $fillable = [
        'name',          // the name field
        'employee_code',          // the employee_code field
        'department_id',
        'manager_id',
        'joining_date',
        'email',         // example, add all columns you want mass-assignable
        'phone',
        'address',
        // Add any other columns you want to fill via create()
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}
