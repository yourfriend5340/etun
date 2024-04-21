<?php

namespace App\Http\Controllers;
use App\Models\PatrolRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Patrol_Import;

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
            $path = $request->file('file')->storeas('patrolPIC/'.$search_cus->firstname.'/'.$search_employee->member_name,$imageName);
        }
            $data=[
                'customer_id'=>$search_cusid->customer_id,
                'patrol_upload_user'=>$str,
                'patrol_RD_DateB'=>$json->Date,
            
                'patrol_RD_TimeB'=>$json->Qrcode->time,
                'patrol_RD_No'=>$json->Qrcode->QrcodeID,
                'patrol_RD_Name'=>$search_cusid->patrol_RD_Name,
                'patrol_upload_date'=>$now,
            ];
            $patrol= PatrolRecord::create($data);
            //dd($data);
            
            return response()->json([
            'message' => 'success to upload data'
        ]);
    }


}
