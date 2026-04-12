<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class Customer extends Model
// {
//     use HasFactory;
//     protected $fillable =[
//         'customer_id',
//         'customer_sn',
//         'customer_group_id',
//         'firstname',
//         'lastname',
//         'account',
//         'password',
//         'password_text',
//         'salt',
//         'addr',
//         'lat',
//         'lng',
//         'ip',
//         'status',
//         'active',
//         'tel',
//         'person'
//     ];
// }

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;

    protected $fillable = [
        'customer_sn',
        'password',
        'salt',
        'firstname',
        'lastname',
    ];

    public function getAuthPassword()
    {
        return [
            'password' => $this->attributes['password'],
            'salt' => $this->attributes['salt'],
        ];
    }


    // Passport 查帳號
    public function findForPassport($username)
    {
        return $this->where('customer_sn', $username)->first();
    }

    // 你舊系統 hash（跟 employee 同概念）
    public function validateForPassportPasswordGrant($password)
    {
        return $this->password === sha1($password . $this->salt);
    }
}
