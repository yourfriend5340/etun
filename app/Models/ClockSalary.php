<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClockSalary extends Model
{
    use HasFactory;

    protected $table = 'clock_salary';
    
    protected $fillable = [
        'member_sn',
        'member_name',
        'customer',
        'salary'
    ];
}
