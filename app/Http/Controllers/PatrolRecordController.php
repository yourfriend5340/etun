<?php

namespace App\Http\Controllers;
use App\Models\PatrolRecord;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Patrol_Import;
use App\Exports\PatrolRecordExport;
use Illuminate\Support\Facades\Storage;

class PatrolRecordController extends Controller
{

    //public function __construct()
    //{
    //    $this->middleware('auth');
    // }
    public function index(){
        $user_name=DB::table('customers')->select('firstname','customer_id')->get();
        return view("show_patrol_record",["user_info"=>$user_name]);
    }

    public function show(Request $request,$search_name,$start_time,$end_time,$upload_time){

        if ($search_name==0 || $start_time==0 || $end_time==0)
        {
            return redirect()->route("patrol_record")->with('danger', '請至少給定前三個變數');;
        }

        if($search_name!=0 && $start_time!=0 && $end_time!=0){
            //$name=$search_name;

            $userid=DB::table('customers')->select('customer_id')->where('firstname','=',$search_name)->get()->pluck('customer_id')->first();
            
            //選單用的清單
            $user_name=DB::table('customers')->select('firstname')->get();
            
            if ($upload_time==0){

                $patrol_record= DB::table('patrol_records')
                ->join('customers','patrol_records.customer_id','=','customers.customer_id')
                ->where('patrol_records.customer_id','=', $userid)
                ->whereBetween('patrol_RD_DateB',[$start_time,$end_time])
                ->paginate(50);
            }
          
            else{
                $upload_start_time=$upload_time.' 00:00:00';
                $upload_end_time=$upload_time.' 23:59:59';
         
                $patrol_record= DB::table('patrol_records')
                ->join('customers','patrol_records.customer_id','=','customers.customer_id')
                ->where('patrol_records.customer_id','=', $userid)
                ->whereBetween('patrol_RD_DateB',[$start_time,$end_time])
                ->whereBetween('patrol_upload_date',[$upload_start_time,$upload_end_time])
                ->paginate(50);
            }
            
            return view("show_patrol_record",["patrol_records"=>$patrol_record,"user_info"=>$user_name]);
        }

    }

    public function show_desc(Request $request,$search_name,$start_time,$end_time,$upload_time){

        if($search_name!=0 && $start_time!=0 && $end_time!=0){
            //$name=$search_name;

            $userid=DB::table('customers')->select('customer_id')->where('firstname','=',$search_name)->get()->pluck('customer_id')->first();
            
            //選單用的清單
            $user_name=DB::table('customers')->select('firstname')->get();
            
            if ($upload_time==0){

                $patrol_record= DB::table('patrol_records')
                ->join('customers','patrol_records.customer_id','=','customers.customer_id')
                ->where('patrol_records.customer_id','=', $userid)
                ->whereBetween('patrol_RD_DateB',[$start_time,$end_time])
                ->orderby('id','desc')
                ->paginate(50);
            }
          
            else{
                $upload_start_time=$upload_time.' 00:00:00';
                $upload_end_time=$upload_time.' 23:59:59';
         
                $patrol_record= DB::table('patrol_records')
                ->join('customers','patrol_records.customer_id','=','customers.customer_id')
                ->where('patrol_records.customer_id','=', $userid)
                ->whereBetween('patrol_RD_DateB',[$start_time,$end_time])
                ->whereBetween('patrol_upload_date',[$upload_start_time,$upload_end_time])
                ->orderby('id','desc')
                ->paginate(50);
            }
            return view("show_patrol_record",["patrol_records"=>$patrol_record,"user_info"=>$user_name]);
        }
        if ($search_name==0 || $start_time==0 || $end_time==0)
        {return view("show_patrol_record")->back()->with('danger', '請至少給定前三個變數');}
    }

    public function request(Request $request)
    {
        $name=$request->input('name');
        $stime=$request->input('start_time');
        $etime=$request->input('end_time');
        $utime=$request->input('upload_time');
     
        if ($name==null && $stime==null && $etime==null && $utime==null){
            return view("show_patrol_record");
        }

        else{
            if($name==null)
            {$name='無設定';}

            if($stime==null)
            {$stime='無設定';}

            if($etime==null)
            {$etime='無設定';}

            if($utime==null)
            {$utime='無設定';}


        $patrol_record= DB::table('patrol_records')
                        ->where('customer_id','=', $name)
                        ->paginate(50);
       //dd($patrol_record->nextPageUrl());
        return view("show_patrol_record",["patrol_records"=>$patrol_record]);
        }

    }

    public function show_result_asc(Request $request)
    {
        
        $patrol_record= PatrolRecord::orderBy('id','asc')
            ->paginate(50);

        return view("show_patrol_record",["patrol_records"=>$patrol_record,]);
    }

    public function show_result_desc()
    {
        $patrol_record= PatrolRecord::orderBy('id','desc')
            ->paginate(50);

        return view("show_patrol_record",["patrol_records"=>$patrol_record,]);
    }

   public function import(Request $request){

    $file = $request->file('select_file1');

    //副檔名，選擇編碼器
    $extension = $file->extension();
    if('csv' == $extension) 
    {$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();} 
    else if('xls' == $extension) 
    {$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();} 
    else     
    {$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();}
              
    $spreadsheet = $reader->load($file); 

    $worksheet = $spreadsheet->getActiveSheet()->toarray();  // 獲取當前的工作表數據

    //dd($worksheet);
    if (count($worksheet[0])!=6){
            return back()->with('danger', '資料格式有錯誤，沒有上傳表格');
    }

    $count=count($worksheet);
    $sub_count=count($worksheet[0]);

    $cus_id=DB::table('customers')->select('customer_id')->where('firstname','=',$worksheet[1][2])->get()->first();
    //dd($cus_id->customer_id);

    $code=$worksheet[1][1];
    $date=date('Y-m-d',strtotime($worksheet[1][0]));
    $time=substr($worksheet[1][4],0,8);
    $check_count=DB::table('patrol_records')->where([
                 ['patrol_RD_code','=',$code],
                 ['patrol_RD_DateB','=',$date],
                 ['patrol_RD_TimeB','=',$time]   
        ])->count();
    //dd($check_count);    

    if ($check_count<1){
        for ($i=1;$i<$count;$i++){
            $date=date('Y-m-d',strtotime($worksheet[$i][0]));
            $time=substr($worksheet[$i][4],0,8);
            
            $data=[
            'customer_id'=>$cus_id->customer_id,
            'patrol_RD_DateB'=>$date,    
            'patrol_RD_Code'=>$worksheet[$i][1],
            'patrol_upload_user'=>$worksheet[$i][2].'系統手動匯入',
            'patrol_RD_Name'=>$worksheet[$i][3],
            'patrol_RD_TimeB'=>$time,
            'patrol_upload_date'=>date("Y-m-d H:i:s"),
            ];
            //dd($data);
            $organize= PatrolRecord::create($data);
        }
        return back()->with('success', $worksheet[1][2].'的巡邏資料己上傳！');
    }
    else
    {return back()->with('danger', $worksheet[1][2].'的巡邏資料己存在，沒有上傳！');}

    }

    public function api_store(Request $request)
    {
        
        $json_arr=$request->all();
        $now=date("Y-m-d H:i:s");
        $search_employee=DB::table('employees')->select('member_name')->where('member_sn','=',$json_arr['EmployeeID'])->get()->first();
        $str=$search_employee->member_name.'App上傳';

        foreach ($json_arr['Qrcode'] as $data){
            $search_cusid=DB::table('qrcode')->select('customer_id','patrol_RD_Name')->where('patrol_RD_No','=',$data['QrcodeID'])->get()->first();
            //dd($search_employee->member_name);

            $data=[
                'customer_id'=>$search_cusid->customer_id,
                'patrol_upload_user'=>$str,
                'patrol_RD_DateB'=>$json_arr['Date'],
            
                'patrol_RD_TimeB'=>$data['time'],
                'patrol_RD_No'=>$data['QrcodeID'],
                'patrol_RD_Name'=>$search_cusid->patrol_RD_Name,
                'patrol_upload_date'=>$now,
            ];
            $patrol= PatrolRecord::create($data);
        }
        
        return response()->json([
            'message' => 'success to upload data'
        ]);
    }

    public function api_store_PIC(Request $request)
    {   
        $json=json_decode($request->input('req'));
        $file=$request->file('file');
        $now=date("Y-m-d H:i:s");

        $search_employee=DB::table('employees')->select('member_name')->where('member_sn','=',$json->EmployeeID)->get()->first();
        $search_cusid=DB::table('qrcode')->select('customer_id','patrol_RD_Name')->where('patrol_RD_No','=',$json->Qrcode->QrcodeID)->get()->first();
        $search_cus=DB::table('customers')->select('firstname')->where('customer_id','=',$search_cusid->customer_id)->get()->first();
        $str=$search_employee->member_name.'App上傳';

        if ($request->file('file')!=null){
            $imageName = $json->Date.'_'.$json->Qrcode->time.'_'.$json->Qrcode->QrcodeID.'.'.$request->file('file')->extension();
            $path = $request->file('file')->storeas('public/patrolPIC/'.$search_cusid->customer_id.'/'.$json->EmployeeID,$imageName);
        }
            $data=[
                'customer_id'=>$search_cusid->customer_id,
                'patrol_upload_user'=>$str,
                'patrol_RD_DateB'=>$json->Date,
            
                'patrol_RD_TimeB'=>$json->Qrcode->time,
                'patrol_RD_No'=>$json->Qrcode->QrcodeID,
                'patrol_RD_Name'=>$search_cusid->patrol_RD_Name,
                'patrol_upload_date'=>$now,
                'picturePath'=>'storage/patrolPIC/'.$search_cusid->customer_id.'/'.$json->EmployeeID.'/'.$imageName
            ];
            $patrol= PatrolRecord::create($data);
            //dd($data);
            
            return response()->json([
            'message' => 'success to upload data'
        ]);
    }

    //iclude gps version
    public function api_store2(Request $request)
    {
        
        $json_arr=$request->all();
        $check = false;

        if($json_arr['Lat'] == "" || $json_arr['Lng'] == "")
        {
            return response()->json([
                'message' => 'failure , you need set up gps coordinate'
            ]);
        }
        else
        {
            $lat1 = $json_arr['Lat'];//緯度
            $lng1 = $json_arr['Lng'];//經度
        }
        
        $now = $json_arr['Date'];
        //$now = '2025-07-23 07:11:00';
        //$now = '2025-07-23 20:59:00';
        $year = date('Y', strtotime($now));
        $month = intval(date('m', strtotime($now)));
        $day = intval(date('d', strtotime($now)));
        $time = date('H:i', strtotime($now));        
        $employeeID = $json_arr['EmployeeID'];
        $lat2 = "";
        $lng2 = "";

        //查詢公告、員工名字
        $announce=DB::table('announcements')->latest()->take(5)->get();
        $employeeName=DB::table('employees')->where('member_sn','=',$employeeID)->first()->member_name;

        //查詢當天排班表
        $schedule=DB::table('schedules')->where([
                    ['employee_id','=',$employeeID],
                    ['year','=',$year],
                    ['month','=',$month]
                    ])->get()->toarray();

        for ($i=0;$i<count($schedule);$i++){//該員工該月有排i-1個客戶的班，在第i個客戶中今天有j個班，找到對應的班
            $queryDate='day'.$day;
            $classList=$schedule[$i]->$queryDate;

            if($classList == ""){
                continue;
            }
            $countClassList=strlen($classList);

            for($j=1;$j<=$countClassList;$j++){
                $queryClassName=substr($classList, $j-1, $j);//讀第j個班 
                $queryClassEndTime=$queryClassName.'_end';//組合語法
                $classStartTime=$schedule[$i]->$queryClassName;//第j個班的開始時間
                $classEndTime=$schedule[$i]->$queryClassEndTime;//第j個班的結束時間
                $time=$year.'-'.$month.'-'.$day.' '.$time;


                if(strtotime($classEndTime) < strtotime($classStartTime))
                { 
                    $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                    $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                    $classEndTime = date("Y-m-d H:i",strtotime("+1 day",strtotime($classEndTime)));
                }
                else{
                    $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                    $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                }

                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($classStartTime)));//第j個班開始前十分鐘開放打卡
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classEndTime)));

                //現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){
                //if(strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) < strtotime($classEndTime)){
                     $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                     $cusId=$schedule[$i]->customer_id;
                     $class=$queryClassName;
                     //dd($cusId,$class,$classStartTime,$classEndTime);
                     $lat2=$queryLocation->lat;
                     $lng2=$queryLocation->lng;
                     break;
                }
            }
        }
        //大遲到隔天上班的,找昨天班表，並考慮到可能是1號，前一天是上月底
        if($lat2 == "" && $lng2 =="")
        {
            if($day == 1)
            {
                $year = date("Y",strtotime("-1 day",strtotime($now)));
                $month = date("m",strtotime("-1 day",strtotime($now)));
            }
            $day = date("d",strtotime("-1 day",strtotime($now)));
            

            //查詢昨天排班表
            $schedule=DB::table('schedules')->where([
                        ['employee_id','=',$employeeID],
                        ['year','=',$year],
                        ['month','=',$month]
                        ])->get()->toarray();

            for ($i=0;$i<count($schedule);$i++){//該員工該月有排i-1個客戶的班，在第i個客戶中今天有j個班，找到對應的班
                $queryDate='day'.$day;
                if(isset($schedule[$i]->$queryDate))
                {
                    $classList=$schedule[$i]->$queryDate;
                }
                else{
                    $classList=="";
                }
                
                if($classList == ""){
                    continue;
                }
                $countClassList=strlen($classList);

                for($j=1;$j<=$countClassList;$j++){
                    $queryClassName=substr($classList, $j-1, $j);//讀第j個班 
                    $queryClassEndTime=$queryClassName.'_end';//組合語法
                    $classStartTime=$schedule[$i]->$queryClassName;//第j個班的開始時間
                    $classEndTime=$schedule[$i]->$queryClassEndTime;//第j個班的結束時間
                    $time=$year.'-'.$month.'-'.$day.' '.$time;


                    if(strtotime($classEndTime) < strtotime($classStartTime))
                    { 
                        $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                        $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                        $classEndTime = date("Y-m-d H:i",strtotime("+1 day",strtotime($classEndTime)));
                    }
                    else{
                        $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                        $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                    }

                    $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($classStartTime)));//第j個班開始前十分鐘開放打卡
                    $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classEndTime)));//第j個班開始時二十分鐘開放打卡(此rule移除)

                    //現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                    if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){
                    //if(strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) < strtotime($classEndTime)){
                        $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                        $cusId=$schedule[$i]->customer_id;
                        $class=$queryClassName;
                        //dd($cusId,$class,$classStartTime,$classEndTime);
                        $lat2=$queryLocation->lat;
                        $lng2=$queryLocation->lng;
                        break;
                    }
                }
            }
        }

        //以上是針對班表查詢，以下為額外代班查詢
        if($lat2 == "" && $lng2 =="")
        {
            $day = intval(date('d', strtotime($now)));
                $request_extra = DB::table('extra_schedules')
                ->join('customers', 'extra_schedules.cus_id', '=', 'customers.customer_id')
                ->where('emp_id', $employeeID)
                ->select('extra_schedules.*', 'customers.lat', 'customers.lng') // 自訂欄位
                ->get();

            for($i=0;$i<count($request_extra);$i++)
            {
                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($request_extra[$i]->start)));
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($request_extra[$i]->end)));

                if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($allowPunchEndTime))
                //if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($request_extra[$i]->end))
                {
                    $lat2 = $request_extra[$i]->lat;
                    $lng2 = $request_extra[$i]->lng;
                    $cusId = '';
                    $class = '代';
                    $classStartTime = $request_extra[$i]->start;
                    $classEndTime = $request_extra[$i]->end;
                    break;
                }
            }
        }    

        //計算距離演算法
        //先判斷參數是否有設定，若無被設定表示沒有班，跳錯誤訊息
        if($lat2 != "" && $lng2 != "")
        {
            //$now=date("Y-m-d H:i:s");
            $search_employee=DB::table('employees')->select('member_name')->where('member_sn','=',$json_arr['EmployeeID'])->get()->first();
            $str=$search_employee->member_name.'App上傳';

            foreach ($json_arr['Qrcode'] as $data){
                $search_cusid=DB::table('qrcode')->select('customer_id','patrol_RD_Name')->where('patrol_RD_No','=',$data['QrcodeID'])->get()->first();
                //dd($search_employee->member_name);

                $data=[
                    'customer_id'=>$search_cusid->customer_id,
                    'patrol_upload_user'=>$str,
                    'patrol_RD_DateB'=>$json_arr['Date'],
                
                    'patrol_RD_TimeB'=>$data['time'],
                    'patrol_RD_No'=>$data['QrcodeID'],
                    'patrol_RD_Name'=>$search_cusid->patrol_RD_Name,
                    'patrol_upload_date'=>$now,
                ];
                $patrol= PatrolRecord::create($data);
            }
            
            return response()->json([
                'message' => 'success , upload data already'
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'failure , please upload in your work place and work time'
            ]);
        }


    }
    //include gps version
    public function api_store_PIC2(Request $request)
    {   
        $json=json_decode($request->input('req'));
        $file=$request->file('file');

        if($json->Lat == "" || $json->Lng == "")
        {
            return response()->json([
                'message' => 'failure , you need set up gps coordinate'
            ]);
        }
        else
        {
            $lat1 = $json->Lat;//緯度
            $lng1 = $json->Lng;//經度
        }
        
        $now = $json->Date;
        //$now = '2025-07-23 07:11:00';
        //$now = '2025-07-23 20:59:00';
        $year = date('Y', strtotime($now));
        $month = intval(date('m', strtotime($now)));
        $day = intval(date('d', strtotime($now)));
        $time = date('H:i', strtotime($now));        
        $employeeID = $json->EmployeeID;
        $lat2 = "";
        $lng2 = "";

        //查詢公告、員工名字
        $announce=DB::table('announcements')->latest()->take(5)->get();
        $employeeName=DB::table('employees')->where('member_sn','=',$employeeID)->first()->member_name;

        //查詢當天排班表
        $schedule=DB::table('schedules')->where([
                    ['employee_id','=',$employeeID],
                    ['year','=',$year],
                    ['month','=',$month]
                    ])->get()->toarray();

        for ($i=0;$i<count($schedule);$i++){//該員工該月有排i-1個客戶的班，在第i個客戶中今天有j個班，找到對應的班
            $queryDate='day'.$day;
            $classList=$schedule[$i]->$queryDate;

            if($classList == ""){
                continue;
            }
            $countClassList=strlen($classList);

            for($j=1;$j<=$countClassList;$j++){
                $queryClassName=substr($classList, $j-1, $j);//讀第j個班 
                $queryClassEndTime=$queryClassName.'_end';//組合語法
                $classStartTime=$schedule[$i]->$queryClassName;//第j個班的開始時間
                $classEndTime=$schedule[$i]->$queryClassEndTime;//第j個班的結束時間
                $time=$year.'-'.$month.'-'.$day.' '.$time;


                if(strtotime($classEndTime) < strtotime($classStartTime))
                { 
                    $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                    $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                    $classEndTime = date("Y-m-d H:i",strtotime("+1 day",strtotime($classEndTime)));
                }
                else{
                    $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                    $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                }

                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($classStartTime)));//第j個班開始前十分鐘開放打卡
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classEndTime)));

                //現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){
                //if(strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) < strtotime($classEndTime)){
                     $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                     $cusId=$schedule[$i]->customer_id;
                     $class=$queryClassName;
                     //dd($cusId,$class,$classStartTime,$classEndTime);
                     $lat2=$queryLocation->lat;
                     $lng2=$queryLocation->lng;
                    //dd($cusId,$class,$classStartTime,$classEndTime,$lat2,$lng2);
                     break;
                }
            }
        }
        //大遲到隔天上班的,找昨天班表，並考慮到可能是1號，前一天是上月底
        if($lat2 == "" && $lng2 =="")
        {
            if($day == 1)
            {
                $year = date("Y",strtotime("-1 day",strtotime($now)));
                $month = date("m",strtotime("-1 day",strtotime($now)));
            }
            $day = date("d",strtotime("-1 day",strtotime($now)));
            

            //查詢昨天排班表
            $schedule=DB::table('schedules')->where([
                        ['employee_id','=',$employeeID],
                        ['year','=',$year],
                        ['month','=',$month]
                        ])->get()->toarray();

            for ($i=0;$i<count($schedule);$i++){//該員工該月有排i-1個客戶的班，在第i個客戶中今天有j個班，找到對應的班
                $queryDate='day'.$day;
                if(isset($schedule[$i]->$queryDate))
                {
                    $classList=$schedule[$i]->$queryDate;
                }
                else{
                    $classList=="";
                }
                
                if($classList == ""){
                    continue;
                }
                $countClassList=strlen($classList);

                for($j=1;$j<=$countClassList;$j++){
                    $queryClassName=substr($classList, $j-1, $j);//讀第j個班 
                    $queryClassEndTime=$queryClassName.'_end';//組合語法
                    $classStartTime=$schedule[$i]->$queryClassName;//第j個班的開始時間
                    $classEndTime=$schedule[$i]->$queryClassEndTime;//第j個班的結束時間
                    $time=$year.'-'.$month.'-'.$day.' '.$time;


                    if(strtotime($classEndTime) < strtotime($classStartTime))
                    { 
                        $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                        $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                        $classEndTime = date("Y-m-d H:i",strtotime("+1 day",strtotime($classEndTime)));
                    }
                    else{
                        $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                        $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                    }

                    $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($classStartTime)));//第j個班開始前十分鐘開放打卡
                    $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classEndTime)));//第j個班開始時二十分鐘開放打卡(此rule移除)

                    //現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                    if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){
                    //if(strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) < strtotime($classEndTime)){
                        $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                        $cusId=$schedule[$i]->customer_id;
                        $class=$queryClassName;
                        //dd($cusId,$class,$classStartTime,$classEndTime);
                        $lat2=$queryLocation->lat;
                        $lng2=$queryLocation->lng;
                        break;
                    }
                }
            }
        }

        //以上是針對班表查詢，以下為額外代班查詢
        if($lat2 == "" && $lng2 =="")
        {
            $day = intval(date('d', strtotime($now)));
                $request_extra = DB::table('extra_schedules')
                ->join('customers', 'extra_schedules.cus_id', '=', 'customers.customer_id')
                ->where('emp_id', $employeeID)
                ->select('extra_schedules.*', 'customers.lat', 'customers.lng') // 自訂欄位
                ->get();

            for($i=0;$i<count($request_extra);$i++)
            {
                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($request_extra[$i]->start)));
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($request_extra[$i]->end)));

                if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($allowPunchEndTime))
                //if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($request_extra[$i]->end))
                {
                    $lat2 = $request_extra[$i]->lat;
                    $lng2 = $request_extra[$i]->lng;
                    $cusId = '';
                    $class = '代';
                    $classStartTime = $request_extra[$i]->start;
                    $classEndTime = $request_extra[$i]->end;
                    break;
                }
            }
        }  

        if($lat2 != "" && $lng2 != "")
        {
            $search_employee=DB::table('employees')->select('member_name')->where('member_sn','=',$json->EmployeeID)->get()->first();
            $search_cusid=DB::table('qrcode')->select('customer_id','patrol_RD_Name')->where('patrol_RD_No','=',$json->Qrcode->QrcodeID)->get()->first();
            $search_cus=DB::table('customers')->select('firstname')->where('customer_id','=',$search_cusid->customer_id)->get()->first();
            $str=$search_employee->member_name.'App上傳';

            if ($request->file('file')!=null){
                //$imageName = $json->Date.'_'.$json->Qrcode->time.'_'.$json->Qrcode->QrcodeID.'.'.$request->file('file')->extension();
                //$path = $request->file('file')->storeas('public/patrolPIC/'.$search_cusid->customer_id.'/'.$json->EmployeeID,$imageName);

                //for WINDOWS

                // 將日期字串中的冒號與空白取代成底線
                $cleanDate = str_replace([':', ' '], '_', $json->Date);
                $cleanTime = str_replace(':', '_', $json->Qrcode->time);

                // 組合合法檔名
                $imageName = $cleanDate . '_' . $cleanTime . '_' . $json->Qrcode->QrcodeID . '.' . $request->file('file')->extension();

                $directory = 'public/patrolPIC/' . $search_cusid->customer_id . '/' . $json->EmployeeID;

                // 確保資料夾存在（不會重複建立）
                Storage::makeDirectory($directory);

                $path = $request->file('file')->storeAs($directory, $imageName);
                
            }
                $data=[
                    'customer_id'=>$search_cusid->customer_id,
                    'patrol_upload_user'=>$str,
                    'patrol_RD_DateB'=>$json->Date,
                
                    'patrol_RD_TimeB'=>$json->Qrcode->time,
                    'patrol_RD_No'=>$json->Qrcode->QrcodeID,
                    'patrol_RD_Name'=>$search_cusid->patrol_RD_Name,
                    'patrol_upload_date'=>$now,
                    'picturePath'=>'storage/patrolPIC/'.$search_cusid->customer_id.'/'.$json->EmployeeID.'/'.$imageName
                ];
                $patrol= PatrolRecord::create($data);
                //dd($data);
                
                return response()->json([
                'message' => 'success to upload data'
            ]);
        }
        else
        {
            return response()->json([
                'message' => 'failure , please upload in your work place and in your work place and work time'
            ]);
        }
    }

    public function export(Request $request){

        $id=customer::where('firstname',$request->input('type'))->pluck('customer_id')->first();
        $date=$request->input('exportbymonth');
        $file_name = 'patrolrecord_'.date('Y_m_d_H_i_s').'.xlsx'; 
        return Excel::download(new PatrolRecordExport($id,$date), $file_name);

    }

    public function patrolpointAPI(Request $request){
        $json_arr=$request->all();
        $employeeID = $json_arr['EmployeeID'];
        
        $now = date("Y-m-d H:i:s");
        //$now = '2025-07-23 07:11:00';
        //$now = '2025-07-23 20:59:00';
        $year = date('Y', strtotime($now));
        $month = intval(date('m', strtotime($now)));
        $day = intval(date('d', strtotime($now)));
        $time = date('H:i', strtotime($now));        
        $employeeID = $json_arr['EmployeeID'];
        $cusId = "";

        //查詢公告、員工名字
        $announce=DB::table('announcements')->latest()->take(5)->get();
        $employeeName=DB::table('employees')->where('member_sn','=',$employeeID)->first()->member_name;

        //查詢當天排班表
        $schedule=DB::table('schedules')->where([
                    ['employee_id','=',$employeeID],
                    ['year','=',$year],
                    ['month','=',$month]
                    ])->get()->toarray();

        for ($i=0;$i<count($schedule);$i++){//該員工該月有排i-1個客戶的班，在第i個客戶中今天有j個班，找到對應的班
            $queryDate='day'.$day;
            $classList=$schedule[$i]->$queryDate;

            if($classList == ""){
                continue;
            }
            $countClassList=strlen($classList);

            for($j=1;$j<=$countClassList;$j++){
                $queryClassName=substr($classList, $j-1, $j);//讀第j個班 
                $queryClassEndTime=$queryClassName.'_end';//組合語法
                $classStartTime=$schedule[$i]->$queryClassName;//第j個班的開始時間
                $classEndTime=$schedule[$i]->$queryClassEndTime;//第j個班的結束時間
                $time=$year.'-'.$month.'-'.$day.' '.$time;


                if(strtotime($classEndTime) < strtotime($classStartTime))
                { 
                    $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                    $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                    $classEndTime = date("Y-m-d H:i",strtotime("+1 day",strtotime($classEndTime)));
                }
                else{
                    $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                    $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                }

                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($classStartTime)));//第j個班開始前十分鐘開放打卡
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classEndTime)));

                //現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){
                     $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                     $cusId=$schedule[$i]->customer_id;
                     //$class=$queryClassName;
                     //dd($cusId,$class,$classStartTime,$classEndTime);
                     //$lat2=$queryLocation->lat;
                     //$lng2=$queryLocation->lng;
                     break;
                }
            }
        }
        //大遲到隔天上班的,找昨天班表，並考慮到可能是1號，前一天是上月底
        if($cusId == "")
        {
            if($day == 1)
            {
                $year = date("Y",strtotime("-1 day",strtotime($now)));
                $month = date("m",strtotime("-1 day",strtotime($now)));
            }
            $day = date("d",strtotime("-1 day",strtotime($now)));
            

            //查詢昨天排班表
            $schedule=DB::table('schedules')->where([
                        ['employee_id','=',$employeeID],
                        ['year','=',$year],
                        ['month','=',$month]
                        ])->get()->toarray();

            for ($i=0;$i<count($schedule);$i++){//該員工該月有排i-1個客戶的班，在第i個客戶中今天有j個班，找到對應的班
                $queryDate='day'.$day;
                if(isset($schedule[$i]->$queryDate))
                {
                    $classList=$schedule[$i]->$queryDate;
                }
                else{
                    $classList=="";
                }
                
                if($classList == ""){
                    continue;
                }
                $countClassList=strlen($classList);

                for($j=1;$j<=$countClassList;$j++){
                    $queryClassName=substr($classList, $j-1, $j);//讀第j個班 
                    $queryClassEndTime=$queryClassName.'_end';//組合語法
                    $classStartTime=$schedule[$i]->$queryClassName;//第j個班的開始時間
                    $classEndTime=$schedule[$i]->$queryClassEndTime;//第j個班的結束時間
                    $time=$year.'-'.$month.'-'.$day.' '.$time;


                    if(strtotime($classEndTime) < strtotime($classStartTime))
                    { 
                        $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                        $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                        $classEndTime = date("Y-m-d H:i",strtotime("+1 day",strtotime($classEndTime)));
                    }
                    else{
                        $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                        $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;
                    }

                    $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($classStartTime)));//第j個班開始前十分鐘開放打卡
                    $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classEndTime)));//第j個班開始時二十分鐘開放打卡(此rule移除)

                    //現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                    if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){
                    //if(strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) < strtotime($classEndTime)){
                        $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                        $cusId=$schedule[$i]->customer_id;
                        //$class=$queryClassName;
                        //dd($cusId,$class,$classStartTime,$classEndTime);
                        //$lat2=$queryLocation->lat;
                        //$lng2=$queryLocation->lng;
                        break;
                    }
                }
            }
        }

        //以上是針對班表查詢，以下為額外代班查詢
        if($cusId == "")
        {
            $day = intval(date('d', strtotime($now)));
                $request_extra = DB::table('extra_schedules')
                ->join('customers', 'extra_schedules.cus_id', '=', 'customers.customer_id')
                ->where('emp_id', $employeeID)
                ->select('extra_schedules.*', 'customers.lat', 'customers.lng') // 自訂欄位
                ->get();

            for($i=0;$i<count($request_extra);$i++)
            {
                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($request_extra[$i]->start)));
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($request_extra[$i]->end)));

                if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($allowPunchEndTime))
                //if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($request_extra[$i]->end))
                {
                    //$lat2 = $request_extra[$i]->lat;
                    //$lng2 = $request_extra[$i]->lng;
                    $cusId = $request_extra[$i]->cus_id;
                    //$class = '代';
                    //$classStartTime = $request_extra[$i]->start;
                    //$classEndTime = $request_extra[$i]->end;
                    break;
                }
            }


        } 
        if($cusId != "")
        {
            $point = DB::table('qrcode')
            ->select('patrol_RD_No','patrol_RD_Name')
            ->join('customers','qrcode.customer_id','=','customers.customer_id')
            ->where([
                ['qrcode.customer_id',$cusId],
                ['patrol_RD_No','!=',''],
                ['printQR',1]
            ])->get()->toarray();

            $cus_name = DB::table('customers')->where('customer_id',$cusId)->first()->firstname;
            $cus_arr['customer_id'] = $cusId;
            $cus_arr['customer_name'] = $cus_name;
            
            $data = array_merge($cus_arr,$point);
            $json = json_encode($data);

            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else
        {
            return response()->json(['message' => '請在工作地點跟時間進行此操作'], 404);
        } 


        
    }

}
