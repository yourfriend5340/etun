<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    //public function __construct()
    //{
    //    $this->middleware('guest');
    //}
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*↓↓↓↓↓this original code will auto login after resiter↓↓↓↓↓

        protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        $salt =  substr(md5(uniqid(rand(), true)), 0, 9);

        return User::create([
            'name' => $data['name'],
            'user_group_id'=>$data['user_level'],
            'status'=>1,
            'email' => $data['email'],
            //'password' => Hash::make($data['password']),
            'password' => sha1($salt . sha1($salt . sha1($data['password']))),
            'salt'=>$salt,
        ]);
    }*/

    //my custom validate and it will not auto login new account after register
    protected function register(Request $request){

        $validREsult=$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        $salt =  substr(md5(uniqid(rand(), true)), 0, 9);

        //dd($request->input('user_level'));
        $reg= User::create([
            'name' => $request->input('name'),
            'user_group_id'=>$request->input('user_level'),
            'status'=>1,
            'email' => $request->input('email'),
            //'password' => Hash::make($request->input('password')),
            'password' => sha1($salt . sha1($salt . sha1($request->input('password')))),
            'salt'=>$salt,
        ]);

        $user= USER::orderBy('id','asc')
        ->paginate(20);
        
        return redirect()->route("user_asc");

        //return view("update_employee",["employees"=>$employee,]);

        //return view("show_user",["users"=>$user,]);
        //return redirect($this->redirectPath());
    }


    public function show_result_asc()
    {
        //return view('home');
        $user= User::orderBy('id','asc')
            ->paginate(20);
        
        //return view("update_employee",["employees"=>$employee,]);

        return view("show_user",["users"=>$user,]);

    }
}