<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use App\Models\Employee;
use App\Models\PatrolRecord;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $employee = Employee::paginate(10);
        $announcement= Announcements::orderBy('id','desc')->paginate(4);

        $patrol=DB::table('patrol_records')->where('id', \DB::raw("(select max(`id`) from patrol_records)"))->get()->first();
        $request_id=($patrol->id)-20;

        $patrol=DB::table('patrol_records')
        ->join('customers','patrol_records.customer_id','=','customers.customer_id')
        ->where('id','>',$request_id)
        ->orderBy('id','desc')
        ->get();
        
        //dd($patrol);
        return view("home",["employees"=>$employee,"announcements"=>$announcement,'patrol_records'=>$patrol]);


    }
    public function show()
    {
        //return view('home');
        $employee= Employee::orderBy('id','desc')
            ->paginate(20);

        //return view("update_employee",["employees"=>$employee,]);

        return view("home",["employees"=>$employee,]);


    }
}
