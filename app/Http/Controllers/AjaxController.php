<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }

    public function ajaxRequestPost(Request $request)
    {
        $id= $request->uid;
        //var_dump($name);
        //var_dump($id);
        $DBresult_name = DB::table('employees')->where('member_sn', $id)->value('member_name');
  

        if (isset($DBresult_name)){
            $result = '你輸入的ID是：'.$id.'號，已有員工：'.$DBresult_name."使用，請重新指定一個ID。";
            return $result;

        }
        else{
            $result='你輸入的ID是：'.$id.'號，可以使用此ID！';
            return $result;
        }
 
    }

    public function ajaxRequestCustomer(Request $request)
    {
        $id= $request->cid;
        //var_dump($name);
        //var_dump($id);
        $DBresult_name = DB::table('customers')->where('customer_sn','=', $id)->value('firstname');

        if (isset($DBresult_name)){
            $result = '輸入的ID是：'.$id.'，已有客戶：'.$DBresult_name."使用，請重新指定一個ID。";
            return $result;

        }
        else{
            $result='輸入的ID是：'.$id.'，可以使用此ID！';
            return $result;
        }
 
    }

    public function ajaxRequestSchedule(Request $request)
    {
        $id = $request->cid;
        //$id = '445';

        $time = $request->ctime;
        $endtime = $request->etime;
        //$time = '2025-05-30 18:00:00';
        //$endtime = '2025-05-30 19:00:00';
        $yesterdate = date('Y-m-d',strtotime('-1 day',strtotime($time)));
        $todate = date('Y-m-d',strtotime($time));

        $year = date('Y',strtotime($time));
        $month = intval(date('m',strtotime($time)));
        $day = 'day'.date('d',strtotime($time));
        $yesterday = 'day'.date('d',strtotime('-1 day',strtotime($time)));
        $returnData = [];
        

        $query = DB::table('extra_schedules')
        ->join('employees','extra_schedules.leave_member','employees.member_sn')
        ->where([
            ["emp_id",$id],
        ])->get((array(('extra_schedules.*'),'employees.member_name')));

        foreach($query as $r){

            if((strtotime($time) >= strtotime($r->start) && strtotime($time) <= strtotime($r->end))
                && (strtotime($endtime) >= strtotime($r->start) && strtotime($endtime) <= strtotime($r->end)))
            {
                return ['result'=>false,'customer'=>urlencode($r->member_name),'start'=>($r->start),'end'=>($r->end)];
            }
        }



        $query = DB::table('schedules')
        ->join('customers','schedules.customer_id','customers.customer_id')
        ->where([
            ['schedules.employee_id',$id],
            ['schedules.year',$year],
            ['schedules.month',$month],
        ])
        ->get((array(('schedules.*'),'customers.firstname')));

        if(count($query) == 0)
        {
            return ['result'=>true];
        }
        else{


            for($i=0;$i<count($query);$i++){
                $class = $query[$i]->$yesterday;
                $len = strlen($class);

                for($j=0;$j<$len;$j++){
                    $subclass = substr($class,$j,1);
                    $classEnd = $subclass."_end";
                    $hour =  ((strtotime($query[$i]->$classEnd)-strtotime($query[$i]->$subclass))/60/60);

                    $stime = $yesterdate." ".$query[$i]->$subclass;

                    if($hour <= 0)//有隔天狀況
                    { $etime = $todate." ".$query[$i]->$classEnd;}
                    else
                    { $etime = $yesterdate." ".$query[$i]->$classEnd;}

                    //dd($year,$month,$day,$query,$returnData,$i,$hour,$stime,$etime);
                    if((strtotime($time) >= strtotime($stime) && strtotime($time) <= strtotime($etime))
                        && (strtotime($endtime) >= strtotime($stime) && strtotime($endtime) <= strtotime($etime)))
                    {
                        return ['result'=>false,'customer'=>urlencode($query[$i]->firstname),'start'=>$stime,'end'=>$etime];
                    }

                }


                $class = $query[$i]->$day;
                $len = strlen($class);

                for($j=0;$j<$len;$j++){
                    $subclass = substr($class,$j,1);
                    $classEnd = $subclass."_end";
                    $hour =  ((strtotime($query[$i]->$classEnd)-strtotime($query[$i]->$subclass))/60/60);

                    $stime = $todate." ".$query[$i]->$subclass;

                    if($hour <= 0)//有隔天狀況
                    { $etime = date('Y-m-d',strtotime("1 day",strtotime($todate)))." ".$query[$i]->$classEnd;}
                    else
                    { $etime = $todate." ".$query[$i]->$classEnd;}

                    //dd($year,$month,$day,$query,$returnData,$i,$hour,$stime,$etime);
                    if((strtotime($time) >= strtotime($stime) && strtotime($time) <= strtotime($etime))
                        && (strtotime($endtime) >= strtotime($stime) && strtotime($endtime) <= strtotime($etime)))
                    {
                        return ['result'=>false,'customer'=>urlencode($query[$i]->firstname),'start'=>$stime,'end'=>$etime];
                    }

                }
            }
        }
        return ['result'=>true];
    }


    //未完成
    public function ajaxRequest_patrol_record(Request $request){
        
        $id= $request->customer_id;
        $udate=$request->patrol_upload_date;
        $stime=$request->stime;
        $etime=$request->etime;

                
        $patrol_record= DB::table('patrol_records')
                                    ->where('id','=', $id)
                                    ->whereBetween('patrol_RD_time',$stime,$etime)
                                    ->where('patrol_upload_date','=',$udate)
                                    ->get();
        //dd($id);

        return view("show_patrol_record",["patrol_records"=>$patrol_record,]);

        


    }

}
