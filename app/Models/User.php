<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
//use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    //use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    const ROLE_ADMIN=1;
    const ROLE_SUPER_MANAGER=2;
    const ROLE_MANAGER=3;
    const ROLE_USER=4;

    protected $fillable = [
        'user_group_id',
        'status',
        'name',
        'firstname',
        'lastname',
        'email',
        'password',
        'salt',
        'ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function getAuthPassword()
    {
        return ['password'=>$this->attributes['password'], 'salt'=>$this->attributes['salt']];
    }

    public function findForPassport($username)
    {
        return $this->where('email', $username)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        $salt='d7019d866';
        //dd($this->password == sha1($salt . sha1($salt . sha1($password))));
        return ($this->password ==sha1($salt . sha1($salt . sha1($password))));
        //return Hash::check($password, $this->password);
    }
}
