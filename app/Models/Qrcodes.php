<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qrcodes extends Model
{
    use HasFactory;

    protected $table = 'qrcode';
    
    protected $fillable = [
        'id',
        'customer_id',
        'patrol_RD_No',
        'patrol_RD_Name',
        'patrol_RD_Code',
        'printQR',
    ];
}
