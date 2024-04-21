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
