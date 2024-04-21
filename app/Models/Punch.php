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
        'year',
        'month',
        'day',
        'PunchInTime1',
        'scheduleStart1',
        'PunchOutTime1',
        'scheduleEnd1',
        
        'PunchInTime2',
        'scheduleStart2',
        'PunchOutTime2',
        'scheduleEnd2',

        'PunchInTime3',
        'scheduleStart3',
        'PunchOutTime3',
        'scheduleEnd3',

        'PunchInTime4',
        'scheduleStart4',
        'PunchOutTime4',
        'scheduleEnd4',

        'PunchInTime5',
        'scheduleStart5',
        'PunchOutTime5',
        'scheduleEnd5',

        'PunchInTime6',
        'scheduleStart6',
        'PunchOutTime6',
        'scheduleEnd6',

        'PunchInTime7',
        'scheduleStart7',
        'PunchOutTime7',
        'scheduleEnd7',

        'PunchInTime8',
        'scheduleStart8',
        'PunchOutTime8',
        'scheduleEnd8',

        'PunchInTime9',
        'scheduleStart9',
        'PunchOutTime9',
        'scheduleEnd9',

        'PunchInTime10',
        'scheduleStart10',
        'PunchOutTime10',
        'scheduleEnd10',
    ];
}
