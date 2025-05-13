<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class onetime_table extends Model
{
    use HasFactory;

    protected $table='onetime_table';

    protected $fillable =[
        'empid',
        'type',
        'day',
        'reason',
        'filePath',
    ];
}
