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
        $topAnn = Announcements::where('top',1)->first();
        $announcement= Announcements::where('top',0)->orderBy('id','desc')->limit(4)->get();

        $patrol=DB::table('patrol_records')->where('id', \DB::raw("(select max(`id`) from patrol_records)"))->get()->first();
        $request_id=($patrol->id)-20;

        $patrol=DB::table('patrol_records')
        ->join('customers','patrol_records.customer_id','=','customers.customer_id')
        ->where('id','>',$request_id)
        ->orderBy('id','desc')
        ->get();

        $leave = DB::table('twotime_table')
                ->join('employees','twotime_table.empid','employees.member_sn')
                ->where('twotime_table.status', null)
                ->orderby('twotime_table.id')
                ->paginate(5,(array(('twotime_table.*'),'employees.member_name')));

        $additionals = DB::table('punch_record')
                ->join('employees','employees.member_sn','employee_id')
                ->where('additional','N')
                ->orderby('punch_record.id')
                ->paginate(5,(array(('punch_record.*'),'employees.member_name')));    

        return view("home",["employees"=>$employee,
                            "topAnn"=>$topAnn,
                            "announcements"=>$announcement,
                            'patrol_records'=>$patrol,
                            'leaves'=>$leave,
                            'additionals'=>$additionals,
                        ]);
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
