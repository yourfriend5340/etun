<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show_result_asc()
    {
        //$user= User::orderBy('id','asc')->paginate(20);
        $user=DB::table('users')->where('id','>',5)->orderby('id','asc')->paginate(20);
        return view("show_user",["users"=>$user,]);
    }

    public function show_result_desc()
    {     
       // $user= User::orderBy('id','desc')->paginate(20);
        $user=DB::table('users')->where('id','>','5')->orderby('id','desc')->paginate(20);
        return view("show_user",["users"=>$user,]);
    }

    public function destroy(Request $request,$Delete_id)
    {   
 
        $deleted=DB::table('users')->where('id','=',$Delete_id)->delete();
        //return response(null,Response::HTTP_NO_CONTENT);
        return redirect()->route('user_asc');
    }
}
