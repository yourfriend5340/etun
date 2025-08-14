<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Punch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcements;
use App\Services\AccessSalaryService;

class AuthUserController extends Controller
{

    public function __construct(AccessSalaryService $accessSalaryService)
    {
        $this->accessSalaryService = $accessSalaryService;
    }

    public function login(Request $request)
    {
        /*
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);*/
        $data = $request->validate([
            'account' => 'required',
            'password' => 'required'
        ]);

        $credentials = [
            'member_account'=>$request->account,
            'password'=>$request->password,
        ];
        
        //dd(Auth::guard('app')->attempt($credentials));
        if (!auth()->guard('app')->attempt($credentials)) {
            return response(['error_message' => 'Incorrect account or password.']);
        }

        $token = auth()->guard('app')->user()->createToken('API Token')->accessToken;

        $user=auth()->guard('app')->user()->only('member_sn','member_name','member_account');
 
        $data=[
            'EmployeeID'=>$user['member_sn'],
            'name'=>$user['member_name'],
            'account'=>$user['member_account']
        ];
        
        //以下整理並回傳聯絡人資訊
        $group_name=DB::table('contact_group')->select('groupName')->get();
        $count_group= count($group_name);

        $contact=DB::table('contact')
            ->join('contact_group', 'contact.gid', '=', 'contact_group.id')
            ->select('contact.contactName','contact.contactPhone','groupName')
            ->orderby('groupName','asc')
            ->orderby('contact.id','asc')
            ->get()->toarray();
        $count_contact = count($contact);

        $year = date("Y");
        $month = date("m");

        /*
        $schedule=DB::table('schedules')
                    ->join('customers',function($join){
                        $join->on('schedules.customer_id','=','customers.customer_id');    
                    })
                    ->where([
                        ['employee_id',$user['member_sn']],
                        ['year',$year],
                        ['month',$month]
                    ])
                    ->orwhere([
                        ['employee_id',$user['member_sn']],
                        ['year',$year],
                        ['month',$month+1]
                    ])
                    ->select('customers.firstname','schedules.*')
                    ->orderby('schedules.month')
                    ->get()
                     ->map(function($schedule){
                         return[
                             'customerName'=>$schedule->firstname,
                                'month'=>$schedule->year.'-'.$schedule->month,
                                '1'=>$schedule->day1,
                                '2'=>$schedule->day2,
                                '3'=>$schedule->day3,
                                '4'=>$schedule->day4,
                                '5'=>$schedule->day5,
                                '6'=>$schedule->day6,
                                '7'=>$schedule->day7,
                                '8'=>$schedule->day8,
                                '9'=>$schedule->day9,
                                '10'=>$schedule->day10,
                                '11'=>$schedule->day11,
                                '12'=>$schedule->day12,
                                '13'=>$schedule->day13,
                                '14'=>$schedule->day14,
                                '15'=>$schedule->day15,
                                '16'=>$schedule->day16,
                                '17'=>$schedule->day17,
                                '18'=>$schedule->day18,
                                '19'=>$schedule->day19,
                                '20'=>$schedule->day20,
                                '21'=>$schedule->day21,
                                '22'=>$schedule->day22,
                                '23'=>$schedule->day23,
                                '24'=>$schedule->day24,
                                '25'=>$schedule->day25,
                                '26'=>$schedule->day26,
                                '27'=>$schedule->day27,
                                '28'=>$schedule->day28,
                                '29'=>$schedule->day29,
                                '30'=>$schedule->day30,
                                '31'=>$schedule->day31,
                                'A'=>[$schedule->A,$schedule->A_end],
                                'B'=>[$schedule->B,$schedule->B_end],
                                'C'=>[$schedule->C,$schedule->C_end],
                                'D'=>[$schedule->D,$schedule->D_end],
                                'E'=>[$schedule->E,$schedule->E_end],
                                'F'=>[$schedule->F,$schedule->F_end],
                                'G'=>[$schedule->G,$schedule->G_end],
                                'H'=>[$schedule->H,$schedule->H_end],
                                'I'=>[$schedule->I,$schedule->I_end],
                                'J'=>[$schedule->J,$schedule->J_end],
                                
                         ];
                     })
                    ->toarray();

        $allClassList=['A','B','C','D','E','F','G','H','I','J'];
        $defind = array();
        
        if($schedule != [])
        {
            for($i=0;$i<count($schedule);$i++){
                //過濾空欄位
                foreach($schedule[$i] as $k=>$v){
                    if($schedule[$i][$k] == "" || $v == ","){
                        unset($schedule[$i][$k]);
                    }
                }

                //將班表定義時間整理
                for($j=0;$j<count($allClassList);$j++){
                    if($schedule[$i][$allClassList[$j]][0] == "" || $schedule[$i][$allClassList[$j]][1] == ""){
                        unset($schedule[$i][$allClassList[$j]]);
                    }
                    else{
                        if(!isset($schedule[$i]["Defind"][$allClassList[$j]])){
                            $schedule[$i]["Defind"][$allClassList[$j]]=[];
                        }

                        if(!isset($schedule[$i][$allClassList[$j]])){
                            $schedule[$i][$allClassList[$j]]=[];
                        }

                        array_push($schedule[$i]["Defind"][$allClassList[$j]],
                            $schedule[$i][$allClassList[$j]][0] , $schedule[$i][$allClassList[$j]][1]
                        );

                        $schedule[$i][$allClassList[$j]]=[];
                    }

                }
                //$offset = 0;
                for($k=1;$k<=31;$k++){
                    
                    if(isset($schedule[$i][$k]) && $schedule[$i][$k] != null)
                    {            
                        $class = $schedule[$i][$k];
                        if(strlen($class) == 1)
                        { 
                            array_push($schedule[$i][$class],$k);
                        }
                        else{
                            for($n=1;$n<=strlen($class);$n++)
                            {
                                $oneClass = substr($class,$n-1,1);
                                array_push($schedule[$i][$oneClass],$k);
                            }

                        }
                        unset($schedule[$i][$k]);
                    }
                }
                //$schedule[$i]['customerName'] = urlencode($schedule[$i]['customerName']);
            }
        }
        */


        //建立各群組的array
        for($i=0;$i<$count_group;$i++){
            $gpName = $group_name[$i]->groupName;

            $index = 1;
            for($j=0;$j<$count_contact;$j++){
                if($contact[$j]->groupName === $gpName){
                    $name = $contact[$j]->contactName;    
                    $phone = $contact[$j]->contactPhone;

                    $contactArr[$gpName]["user".$index]["name"] = $name;
                    $contactArr[$gpName]["user".$index]["phone"] = $phone;
                    $index++;
                }
            }
        }

            $topAnn = Announcements::where('top',1)->first();
                $announce_arr['TOP']['time']=date('Y-m-d H:i:s',strtotime($topAnn->created_at));
                $announce_arr['TOP']['title']=$topAnn->title;
                $announce_arr['TOP']['announce']=$topAnn->announcement;

                $checkCount = Announcements::where('top',0)->count();
                if($checkCount >= 4)
                {
                    $announce= Announcements::where('top',0)->orderBy('id','desc')->limit(4)->get();
                }
                else{
                    $announce= Announcements::where('top',0)->orderBy('id','desc')->limit($checkCount)->get();
                }
                
            for($i=0;$i<4;$i++){
                $announce_arr[$i]['time']=date('Y-m-d H:i:s',strtotime($announce[$i]->created_at));
                $announce_arr[$i]['title']=$announce[$i]->title;
                $announce_arr[$i]['announce']=$announce[$i]->announcement;
            }
            //dd($announce_arr);


            //計算體檢日
            $request=DB::table('employees')->select('Birthday','checkup')->where('member_sn','=',$user['member_sn'])->first(); 
            $body_check=$request->checkup;
            $Birthday=$request->Birthday;
            $now=date("Y-m-d H:i:s");
            $diff=strtotime($now)-strtotime($body_check);//現在離最後體檢日有幾秒
            $age=strtotime($now)-strtotime($Birthday);

            //體檢日差距換算
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            //年紀換算
            $age_years = floor($age / (365*60*60*24));
            $age_months = floor(($age - $age_years * 365*60*60*24) / (30*60*60*24));

            //$data1['message']="punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)";
            //$data['person_announce']=null;
            //dd($employeeID,'體檢日：'.$body_check,'今天：'.$now,'體檢差：'.$years.'年'.$months.'月'.$days.'天','年紀：'.$age_years.'歲'.$age_months.'月',$data['message']);

            if($age_years<40){
                if($years==4){
                    if($months>=10){
                        $data1=([
                                'person_announcement'=>"您已 $age_years 歲,上次體檢日是： $body_check ,您必須每五年檢查一次,請在以下時間前線交體檢表: ".date("Y-m-d",strtotime("+5 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                            }
                }
                if($years>4){
                        $data1=([
                                'person_announcement'=>"您已 $age_years 歲,上次體檢日是： $body_check ,您必須每五年檢查一次,請在以下時間前線交體檢表: ".date("Y-m-d",strtotime("+5 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                }
            }
            if($age_years>=40 && $age_years<65){
                if($years==2){
                    if($months>=10){
                        $data1=([
                                'person_announcement'=>"您已 $age_years 歲,上次體檢日是： $body_check ,您必須每三年檢查一次,請在以下時間前線交體檢表: ".date("Y-m-d",strtotime("+3 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                            }
                }
                if($years>2){
                        $data1=([
                                'person_announcement'=>"您已 $age_years 歲,上次體檢日是： $body_check ,您必須每三年檢查一次,請在以下時間前線交體檢表: ".date("Y-m-d",strtotime("+3 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                }
            }
            if($age_years>=65){
                if($years<1){
                    if($months>=10){
                        $data1=([
                                'person_announcement'=>"您已 $age_years 歲,上次體檢日是： $body_check ,您必須每年檢查一次,請在以下時間前線交體檢表: ".date("Y-m-d",strtotime("+1 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                            }
                }
                if($years>=1){
                        $data=([
                                'person_announcement'=>"您已 $age_years 歲,上次體檢日是： $body_check 您必須每年檢查一次,請在以下時間前線交體檢表: ".date("Y-m-d",strtotime("+1 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                }
            }
        
        //薪資資訊
            $now = date('Y-m-d');
            $month = date('Y-m',strtotime("-1 month",strtotime($now)));
            $queryYear = date('Y',strtotime($month));
            $queryMonth = date('m',strtotime($month));
            $TotalData = $this->accessSalaryService->countTotalTime($queryYear,$queryMonth,$user['member_sn']);

            $today = intval(date('d',strtotime($now)));

            if(($TotalData[1] != 0) && ($today >= 10))
            {
                $data2['salary'] = $TotalData[1];
                
                $addORsub = DB::table('salary_items')->where([
                    ['empid',$user['member_sn']],
                    ['month',$month]
                ])->orderby('mark')->get();

                $subIndex = 0;
                $addIndex = 0;
                $countTotal = $TotalData[1];
                for($i=0;$i<count($addORsub);$i++)
                {   

                    if($addORsub[$i]->mark == 'add'){
                        $data2['add'][$addIndex]['item']=$addORsub[$i]->item;
                        $data2['add'][$addIndex]['amount']=$addORsub[$i]->amount;
                        $countTotal = $countTotal + $addORsub[$i]->amount;
                        $addIndex++;
                    }
                    elseif($addORsub[$i]->mark == 'sub'){
                        $data2['sub'][$subIndex]['item']=$addORsub[$i]->item;
                        $data2['sub'][$subIndex]['amount']=$addORsub[$i]->amount;
                        $countTotal = $countTotal - $addORsub[$i]->amount;
                        $subIndex++;
                    }

                }
                $data2['Total'] = $countTotal;
            }
            else{
                $data2 = '尚未結算薪水';
            }

        //return response(['user' => $data, 'contactData'=>$contactArr,'ClassSchedule'=>$schedule,'annoucements'=>$data1,'salary'=>$data2,'token' => $token]);
        return response(['user' => $data, 'contactData'=>$contactArr,'annoucements'=>$data1,'salary'=>$data2,'token' => $token]);
    }


    public function logout(Request $request)
    {
        //$request->user()->token()->revoke();
        $res=$request->user()->token()->delete();
        //dd($res);
        if($res==true){
        return response()->json([
            'message' => 'Successfully'
        ]);
        }

        else{
        return response()->json([
            'message' => 'faild,check your token'
        ]);
        }
    }

    public function api_PunchIn(Request $request)
    {
        $request=$request->all();

        $now = date("Y-m-d H:i:s");
        $now = '2025-07-23 07:11:00';
        //$now = '2025-07-23 20:59:00';
        $year = date('Y', strtotime($now));
        $month = intval(date('m', strtotime($now)));
        $day = intval(date('d', strtotime($now)));
        $time = date('H:i', strtotime($now));        
        $employeeID = $request['EmployeeID'];
        $lat1 = $request['lat'];
        $lng1 = $request['lng'];
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
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classStartTime)));//第j個班開始時二十分鐘開放打卡(此rule移除)

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
                    $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classStartTime)));//第j個班開始時二十分鐘開放打卡(此rule移除)

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
            $request_extra = DB::table('extra_schedules')->where('emp_id',$employeeID)->get();

            for($i=0;$i<count($request_extra);$i++)
            {
                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($request_extra[$i]->start)));
                $allowPunchStartTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($request_extra[$i]->end)));
                if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($allowPunchEndTime))
                //if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($request_extra[$i]->end))
                {
                    $lat2 = $lat1;
                    $lng2 = $lng1;
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
            $lat2 = ($lat2 * pi() ) / 180;
            $lng2 = ($lng2 * pi() ) / 180;
        }
        else{
            return "打卡失敗，請於您的工作場地及上班前十分鐘進行打卡。";
        }
        $earthRadius = 6368000; //地球平均半徑(公尺)

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;

        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = round($earthRadius * $stepTwo);

        if($calculatedDistance<=300)//打卡誤差300公尺內，打卡並執行作業
        {   
            $DBdata=[
                'employee_id'=>$employeeID,
                'year'=>$year,
                'month'=>$month,
                'day'=>$day,
                'type'=>'IN',
                'punchTime'=>$now,

            ];

            $punch= Punch::create($DBdata);
            return response()->json(['success'],200);
        }
        else{
            return '打卡失敗，請於您的工作場地及上班前十分鐘進行打卡。';
        }

    }

    public function api_PunchOut(Request $request)
    {
        $request=$request->all();
        $now=date("Y-m-d H:i:s");
        //$now = '2025-07-23 06:40:00';
        //$now = '2025-07-23 20:59:00';
        $year=date('Y', strtotime($now));
        $month=intval(date('m', strtotime($now)));
        $day=intval(date('d', strtotime($now)));
        $time=date('H:i', strtotime($now));        
        $employeeID=$request['EmployeeID'];
        $lat2="";
        $lng2="";

        $lastYear = date("Y",strtotime("-1 day",strtotime($now)));
        $lastMonth = intval(date("m",strtotime("-1 day",strtotime($now))));
        $lastDay = intval(date("d",strtotime("-1 day",strtotime($now))));

        //查詢公告、員工名字
        //$announce=DB::table('announcements')->latest()->take(5)->get();
        $employeeName=DB::table('employees')->where('member_sn','=',$employeeID)->first()->member_name;

        //判斷是不是昨天的夜班，但如果是每月1號，要查上個月的表
        if($day == 1)
        {
            $schedule=DB::table('schedules')->where([
            ['employee_id','=',$employeeID],
            ['year','=',$lastYear],
            ['month','=',$lastMonth]
            ])->get()->toarray();
        }
        else
        {
            $schedule=DB::table('schedules')->where([
            ['employee_id','=',$employeeID],
            ['year','=',$year],
            ['month','=',$month]
            ])->get()->toarray();
        }

        for ($i=0;$i<count($schedule);$i++){//該員工該月有排i-1個客戶的班，在第i個客戶中今天有j個班，找到對應的班
            $queryDate='day'.$lastDay;
            $classList=$schedule[$i]->$queryDate;
            if($classList == ""){
                continue;
            }
            $countClassList=strlen($classList);
            //dd($classList,$countClassList);

            for($j=1;$j<=$countClassList;$j++){
                $queryClassName=substr($classList, $j-1, $j);//讀第j個班
                $queryClassEndTime=$queryClassName.'_end';//組合語法
                $classStartTime=$schedule[$i]->$queryClassName;//第j個班的開始時間
                $classEndTime=$schedule[$i]->$queryClassEndTime;//第j個班的結束時間
                //dd(strtotime($classEndTime)-strtotime($classStartTime));)
                if($day == 1)
                {
                    $classStartTime= $lastYear.'-'.$lastMonth.'-'.($lastDay).' '.$classStartTime;
                    $classEndTime= $lastYear.'-'.$lastMonth.'-'.($lastDay).' '.$classEndTime;
                }
                else{
                    $classStartTime= $year.'-'.$month.'-'.($lastDay).' '.$classStartTime;
                    $classEndTime= $year.'-'.$month.'-'.($lastDay).' '.$classEndTime;
                }

                if(strtotime($classEndTime) < strtotime($classStartTime) )
                {
                    $classEndTime = date("Y-m-d H:i",strtotime("+1 day",strtotime($classEndTime)));
                }

                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($classEndTime)));//第j個班下班前十分鐘開放打卡
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classEndTime)));//第j個班下班時間後二十分鐘開放打卡
                //dd($queryClassName,$schedule,$queryDate,$classStartTime,$classEndTime,$allowPunchStartTime,$allowPunchEndTime);
                
                //現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){
                    $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                    $cusId=$schedule[$i]->customer_id;
                    $class=$queryClassName;
                    $lat2=$queryLocation->lat;
                    $lng2=$queryLocation->lng;
                    $day = $day-1;
                    break;
                    
                }
            }
            //dd($now,strtotime($now) <= strtotime($classEndTime) && strtotime($now) >= strtotime($allowPunchStartTime));
        }

        //上面檢查夜班程式，因無查詢到，故無設定gps座標，再查詢今日班表
        if($lat2 == "" && $lng2 == "")
        {
            $schedule=DB::table('schedules')->where([
            ['employee_id','=',$employeeID],
            ['year','=',$year],
            ['month','=',$month]
            ])->get()->toarray();

            for ($i=0;$i<count($schedule);$i++){//該員工該月有排i-1個客戶的班，在第i個客戶中今天有j個班，找到對應的班
                $queryDate='day'.$day;
                $classList=$schedule[$i]->$queryDate;
                $countClassList=strlen($classList);
                //dd($classList,$countClassList);

                for($j=1;$j<=$countClassList;$j++){
                    $queryClassName=substr($classList, $j-1, $j);//讀第j個班
                    $queryClassEndTime=$queryClassName.'_end';//組合語法
                    $classStartTime=$schedule[$i]->$queryClassName;//第j個班的開始時間
                    $classEndTime=$schedule[$i]->$queryClassEndTime;//第j個班的結束時間
                    //dd(strtotime($classEndTime)-strtotime($classStartTime));)
                    $time=$year.'-'.$month.'-'.$day.' '.$time;
                    $classStartTime= $year.'-'.$month.'-'.$day.' '.$classStartTime;
                    $classEndTime= $year.'-'.$month.'-'.$day.' '.$classEndTime;

                    if(strtotime($classEndTime) < strtotime($classStartTime) )
                    {
                        $classEndTime = date("Y-m-d H:i",strtotime("+1 day",strtotime($classEndTime)));
                    }

                    $allowPunchStartTime=date("Y-m-d H:i",strtotime("-20 minute",strtotime($classStartTime)));//第j個班下班前十分鐘開放打卡
                    $allowPunchEndTime=date("Y-m-d H:i",strtotime("+10 minute",strtotime($classEndTime)));//第j個班下班時間後二十分鐘開放打卡
                    //dd($queryClassName,$schedule,$now,$classStartTime,$classEndTime,$allowPunchStartTime,$allowPunchEndTime);
                    //dd(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime));
                    
                    //現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                    if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){
                        $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                        //$cusId=$schedule[$i]->customer_id;
                        //$class=$queryClassName;
                        //dd($cusId,$class,$classStartTime,$classEndTime);
                        $lat2=$queryLocation->lat;
                        $lng2=$queryLocation->lng;
                        break;
                    }
                }
                //dd($now,strtotime($now) <= strtotime($classEndTime) && strtotime($now) >= strtotime($allowPunchStartTime));
            }
        }       
        //以上是針對班表查詢，以下為額外代班查詢
        $request_extra = DB::table('extra_schedules')->where('emp_id',$employeeID)->get();
        
        if(count($request_extra) != 0)
        {
            for($i=0;$i<count($request_extra);$i++)
            {
                $allowPunchStartTime=date("Y-m-d H:i",strtotime($request_extra[$i]->start));
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+20 minute",strtotime($request_extra[$i]->end)));
                if( strtotime($now) >= strtotime($allowPunchStartTime) && strtotime($now) <= strtotime($allowPunchEndTime))
                {
                    $DBdata=[
                        'employee_id'=>$employeeID,
                        'year'=>$year,
                        'month'=>$month,
                        'day'=>$day,
                        'type'=>'OUT',
                        'PunchTime'=>$now,
                    ];

                    $punch= Punch::create($DBdata);
                    return "打下班卡成功";
                }
                else{
                    continue;
                }
            }
        }

        //計算距離演算法
        //先判斷參數是否有設定，若無被設定表示沒有班，跳錯誤訊息
        if($lat2 != "" && $lng2 != "")
        {
            $lat2 = ($lat2 * pi() ) / 180;
            $lng2 = ($lng2 * pi() ) / 180;
        }
        else{
            return "打卡失敗，請於該班規定時間前打下班卡1";
        }
        $earthRadius = 6368000; //地球平均半徑(公尺)

        $lat1=$request['lat'];
        $lng1=$request['lng'];

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;

        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = round($earthRadius * $stepTwo);

        if($calculatedDistance<=300)//打卡誤差300公尺內，打卡並執行作業
        {   
            //寫入打卡記錄
                $DBdata=[
                    'employee_id'=>$employeeID,
                    'year'=>$year,
                    'month'=>$month,
                    'day'=>$day,
                    'type'=>'OUT',
                    'punchTime'=>$now,
                ];

                $punch= Punch::create($DBdata);
                return "打下班卡成功";
        }
        else{
            return '打卡失敗，請在你工作所在地打卡!';
        }
    }

    public function api_Schedule(Request $request)
    {
        $employeeID=$request['EmployeeID'];
        $year = date("Y");
        $month = date("m");

        $schedule=DB::table('schedules')
                    ->join('customers',function($join){
                        $join->on('schedules.customer_id','=','customers.customer_id');    
                    })
                    ->where([
                        ['employee_id',$employeeID],
                        ['year',$year],
                        ['month',$month]
                    ])
                    ->orwhere([
                        ['employee_id',$employeeID],
                        ['year',$year],
                        ['month',$month+1]
                    ])
                    ->select('customers.firstname','schedules.*')
                    ->orderby('schedules.month')
                    ->get()
                    ->map(function($schedule){
                        return[
                            'customerName'=>$schedule->firstname,
                                'month'=>$schedule->year.'-'.$schedule->month,
                                '1'=>$schedule->day1,
                                '2'=>$schedule->day2,
                                '3'=>$schedule->day3,
                                '4'=>$schedule->day4,
                                '5'=>$schedule->day5,
                                '6'=>$schedule->day6,
                                '7'=>$schedule->day7,
                                '8'=>$schedule->day8,
                                '9'=>$schedule->day9,
                                '10'=>$schedule->day10,
                                '11'=>$schedule->day11,
                                '12'=>$schedule->day12,
                                '13'=>$schedule->day13,
                                '14'=>$schedule->day14,
                                '15'=>$schedule->day15,
                                '16'=>$schedule->day16,
                                '17'=>$schedule->day17,
                                '18'=>$schedule->day18,
                                '19'=>$schedule->day19,
                                '20'=>$schedule->day20,
                                '21'=>$schedule->day21,
                                '22'=>$schedule->day22,
                                '23'=>$schedule->day23,
                                '24'=>$schedule->day24,
                                '25'=>$schedule->day25,
                                '26'=>$schedule->day26,
                                '27'=>$schedule->day27,
                                '28'=>$schedule->day28,
                                '29'=>$schedule->day29,
                                '30'=>$schedule->day30,
                                '31'=>$schedule->day31,
                                'A'=>[$schedule->A,$schedule->A_end],
                                'B'=>[$schedule->B,$schedule->B_end],
                                'C'=>[$schedule->C,$schedule->C_end],
                                'D'=>[$schedule->D,$schedule->D_end],
                                'E'=>[$schedule->E,$schedule->E_end],
                                'F'=>[$schedule->F,$schedule->F_end],
                                'G'=>[$schedule->G,$schedule->G_end],
                                'H'=>[$schedule->H,$schedule->H_end],
                                'I'=>[$schedule->I,$schedule->I_end],
                                'J'=>[$schedule->J,$schedule->J_end],      
                        ];
                    })->toarray();

        $allClassList=['A','B','C','D','E','F','G','H','I','J'];
        $defind = array();
        
        if($schedule != [])
        {
            for($i=0;$i<count($schedule);$i++){
                //過濾空欄位
                foreach($schedule[$i] as $k=>$v){
                    if($schedule[$i][$k] == "" || $v == ","){
                        unset($schedule[$i][$k]);
                    }
                }

                //將班表定義時間整理
                for($j=0;$j<count($allClassList);$j++){
                    if($schedule[$i][$allClassList[$j]][0] == "" || $schedule[$i][$allClassList[$j]][1] == ""){
                        unset($schedule[$i][$allClassList[$j]]);
                    }
                    else{
                        if(!isset($schedule[$i]["Defind"][$allClassList[$j]])){
                            $schedule[$i]["Defind"][$allClassList[$j]]=[];
                        }

                        if(!isset($schedule[$i][$allClassList[$j]])){
                            $schedule[$i][$allClassList[$j]]=[];
                        }

                        array_push($schedule[$i]["Defind"][$allClassList[$j]],
                            $schedule[$i][$allClassList[$j]][0] , $schedule[$i][$allClassList[$j]][1]
                        );

                        $schedule[$i][$allClassList[$j]]=[];
                    }

                }
                //$offset = 0;
                for($k=1;$k<=31;$k++){
                    
                    if(isset($schedule[$i][$k]) && $schedule[$i][$k] != null)
                    {            
                        $class = $schedule[$i][$k];
                        if(strlen($class) == 1)
                        { 
                            array_push($schedule[$i][$class],$k);
                        }
                        else{
                            for($n=1;$n<=strlen($class);$n++)
                            {
                                $oneClass = substr($class,$n-1,1);
                                array_push($schedule[$i][$oneClass],$k);
                            }

                        }
                        unset($schedule[$i][$k]);
                    }
                }
            }
        }

        return response(['ClassSchedule'=>$schedule]);
    }


}
