<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatrolRecord extends Model
{
    use HasFactory;
 
    protected $table = 'patrol_records';
    
    protected $fillable = [
        //'id',
        'customer_id',
        'patrol_RD_Code',
        'patrol_RD_No',
        'patrol_RD_Name',
        'patrol_RD_TimeB',
        'patrol_RD_DateB',
        'patrol_upload_user',
        'patrol_upload_date',
    ];

}
