<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cusStatus extends Model
{
    use HasFactory;

    //因為此table無update跟create 時間欄位，故關掉
    public $timestamps = FALSE;

    protected $table = 'cus_status';

    protected $fillable = [
        'status',

    ];
}
