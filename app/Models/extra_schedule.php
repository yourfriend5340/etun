<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class extra_schedule extends Model
{
    use HasFactory;

    protected $table='extra_schedules';

    protected $fillable =[
        'emp_id',
        'start',
        'end',
        'leave_member',
        'cus_id',
    ];
}
