<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable =[
        'customer_id',
        'customer_sn',
        'customer_group_id',
        'firstname',
        'lastname',
        'account',
        'password',
        'password_text',
        'salt',
        'addr',
        'lat',
        'lng',
        'ip',
        'status',
        'active',
        'tel'
    ];
}
