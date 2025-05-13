<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class twotime_table extends Model
{
    use HasFactory;

        protected $table='twotime_table';

    protected $fillable =[
        'empid',
        'type',
        'start',
        'end',
        'reason',
        'filePath',
    ];
}
