<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Punch extends Model
{
    use HasFactory;

    protected $table = 'punch_record';
    
    protected $fillable = [
        'employee_id',
        'customer_id',
        'year',
        'month',
        'day',
        'class',
        'start',
        'end',
        'PunchInTime',
        'PunchOutTime'
    ];
}
