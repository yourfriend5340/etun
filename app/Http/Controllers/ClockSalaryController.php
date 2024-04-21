<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClockSalary;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Support\Facades\Gate;

class ClockSalaryController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth');
   }
    
   public function index(){
      if (! Gate::allows('group_admin')) {
         abort(403,'抱歉，你沒有使用此功能權限');
      }

      $name=Employee::select('member_name','member_sn')->where('salary','=',null)->distinct()->get();
      return view('edit_clock_salary',['name'=>$name]);
   }


   public function show_result(Request $request,$inputname){
      if (! Gate::allows('group_admin')) {
        abort(403,'抱歉，你沒有使用此功能權限');
      }
      $employee=Employee::select('member_name')->where([
         ['member_sn','=',$inputname],
         ['salary','=',null],
         ])->get()->first();
      $cus=Customer::select('customer_sn','firstname')->get();
      $name=Employee::select('member_name','member_sn')->where('salary','=',null)->distinct()->get();
      $record=ClockSalary::select('id','member_sn','member_name','customer','salary')->where('member_sn','=',$inputname)->orderBy('id','asc')->get();
      return view('edit_clock_salary',['input'=>$employee->member_name,'name'=>$name,'records'=>$record,'customers'=>$cus]);
   }

   public function add(Request $request,$empname,$name,$salary){
      if (! Gate::allows('group_admin')) {
         abort(403,'抱歉，你沒有使用此功能權限');
      }

      $check=ClockSalary::where([
         ['member_name','=',$empname],
         ['customer','=',$name],
      ])->count();

      $empSN=Employee::select('member_sn')->where('member_name','=',$empname)->get()->first();
      //dd($empSN->member_sn);

      if($check!=0){
         return back()->with('danger',"'已有定義過'$name'的資料，請刪除掉後再新增'");
      }
      else{
         $data=[
            'member_sn'=>$empSN->member_sn,
            'member_name'=>$empname,
            'customer'=>$name,
            'salary'=>$salary,
         ];

         ClockSalary::create($data);
         return redirect("/clocksalary/$empSN->member_sn");
      }

   }

   public function delete(Request $request,$id){
      if (! Gate::allows('group_admin')) {
         abort(403,'抱歉，你沒有使用此功能權限');
      }
      $empSN=ClockSalary::select('member_sn')->where('id','=',$id)->get()->first();
      $del=ClockSalary::where('id','=',$id)->delete();
      return redirect("/clocksalary/$empSN->member_sn");
   }
}
