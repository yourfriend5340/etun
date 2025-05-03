<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class salaryItem extends Model
{
    use HasFactory;

    protected $table = 'salary_items';

    protected $fillable = [
        'mark',
        'serialNum',
        'item',
        'month',
        'amount',
        'empid',
        'cusid'
    ];
}
