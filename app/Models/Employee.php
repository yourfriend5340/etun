<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use function PHPSTORM_META\map;
use Illuminate\Foundation\Auth\User as Authenticatable;


//class Employee extends Model
class Employee extends Authenticatable
{
    use Notifiable;
    use HasApiTokens;
    use HasFactory;
    
    protected $table='employees';

    protected $fillable =[
        'member_name',
        'salt',
        'member_sn',
        'member_phone',
        'member_account',
        'member_password',
        'member_password_text',
        'organize',
        'area',
        'SSN',
        'Gender',
        'Blood_type',
        'Birthday',
        'Height',
        'Weight',
        'Branch',
        'mobile',
        'mail',
        'addr',
        'current_addr',
        'pic_route1',
        'pic_route2',
        'pic_route3',
        'pic_route4',
        'driver',
        'language',
        'school',
        'department',
        'graduate',
        'status',
        'work_place',
        'position',
        'salary',
        'register',
        'leave',
        'check_send',
        'check_back',
        'agreement_send',
        'agreement_back',
        'labor_date',
        'labor_account',
        'retirement_account',
        'health_date',
        'health_account',
        'life_date',
        'group_date',
        'care_date',
        'checkup',
        'memo',
    ];


    public function getAuthPassword()
    {
        return ['password'=>$this->attributes['member_password'], 'salt'=>$this->attributes['salt']];
    }

    public function findForPassport($username)
    {
        return $this->where('member_account', $username)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        $salt='4f408bfec';
        //dd($this->password == sha1($salt . sha1($salt . sha1($password))));
        //return ($this->password ==sha1($salt . sha1($salt . sha1($password))));
        return($this->password==$password);
        //return Hash::check($password, $this->password);
    }
}
