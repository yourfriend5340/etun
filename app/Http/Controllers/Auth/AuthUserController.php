<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Punch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthUserController extends Controller
{
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
        
        //return response(['user' => auth()->user()->only('name','email'), 'token' => $token]);
        return response(['user' => $data, 'token' => $token]);
    }


    public function logout(Request $request)
    {
        //$request->user()->token()->revoke();
        $res=$request->user()->token()->delete();
        //dd($res);
        if($res==true){
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
        }

        else{
        return response()->json([
            'message' => 'faild to logged out'
        ]);
        }
    }

    public function api_PunchIn(Request $request)
    {
        $request=$request->all();

        $now=date("Y-m-d H:i:s");
        $year=date('Y', strtotime($now));
        $month=intval(date('m', strtotime($now)));
        $day=intval(date('d', strtotime($now)));
        $time=date('H:i', strtotime($now));        
        $employeeID=$request['EmployeeID'];

        //查詢公告、員工名字
        $announce=DB::table('announcements')->latest()->take(5)->get();
        $employeeName=DB::table('employees')->where('member_sn','=',$employeeID)->first()->member_name;

        //查詢當天排班表
        $schedule=DB::table('schedules')->where([
                    ['employee_id','=',$employeeID],
                    ['year','=',$year],
                    ['month','=',$month]
                    ])->get()->toarray();
        //dd($announce,count($announce),$employeeName,count($schedule),$schedule);

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
                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-10 minute",strtotime($classStartTime)));//第j個班開始時十分鐘開放打卡
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+20 minute",strtotime($classStartTime)));//第j個班開始時十分鐘開放打卡
                //dd($schedule);
                
                if(strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){//現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                    $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                    $cusId=$schedule[$i]->customer_id;
                    $class=$queryClassName;
                    //dd($cusId,$class,$classStartTime,$classEndTime);
                    $lat2=$queryLocation->lat;
                    $lng2=$queryLocation->lng;
                    break;
                }

                //else {
                //    return "打卡失敗，超過打卡時間或查無班表！！";
                //} 
            }
            //dd($now,strtotime($now) <= strtotime($allowPunchEndTime) && strtotime($now) >= strtotime($allowPunchStartTime));
        }
                

        //計算距離演算法
        //先判斷參數是否有設定，若無被設定表示沒有班，跳錯誤訊息
        if(isset($lat2) && isset($lng2))
        {
            $lat2 = ($lat2 * pi() ) / 180;
            $lng2 = ($lng2 * pi() ) / 180;
        }
        else{
            return "打卡失敗，可能不是你的上班時間，請於您的工作場地及上班前十分鐘再試行打卡。";
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

            for($i=0;$i<=4;$i++){
                $announce_arr[$i]['time']=$announce[$i]->created_at;
                $announce_arr[$i]['title']=$announce[$i]->title;
                $announce_arr[$i]['announce']=$announce[$i]->announcement;
            }
            //dd($announce_arr);

            //計算體檢日
            $request=DB::table('employees')->select('Birthday','checkup')->where('member_sn','=',$employeeID)->first(); 
            $body_check=$request->checkup;
            $Birthday=$request->Birthday;

            $diff=strtotime($now)-strtotime($body_check);//現在離最後體檢日有幾秒
            $age=strtotime($now)-strtotime($Birthday);

            //體檢日差距換算
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            //年紀換算
            $age_years = floor($age / (365*60*60*24));
            $age_months = floor(($age - $age_years * 365*60*60*24) / (30*60*60*24));

            $data['message']="punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)";
            //$data['person_announce']=null;
            //dd($employeeID,'體檢日：'.$body_check,'今天：'.$now,'體檢差：'.$years.'年'.$months.'月'.$days.'天','年紀：'.$age_years.'歲'.$age_months.'月',$data['message']);

            if($age_years<40){
                if($years==4){
                    if($months>=10){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check per 5 years,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+5 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                            }
                }
                if($years>4){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check per 5 years,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+5 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                }
            }
            if($age_years>=40 && $age_years<65){
                if($years==2){
                    if($months>=10){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check per 3 years,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+3 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                            }
                }
                if($years>2){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check per 3 years,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+5 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                }
            }
            if($age_years>=65){
                if($years<1){
                    if($months>=10){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check every years,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+3 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                            }
                }
                if($years>=1){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check every years,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+5 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                }
            }

            //寫入打卡記錄
            $check=Punch::where([
                 ['employee_id','=',$employeeID],
                 ['year','=',$year],
                 ['month','=',$month],
                 ['day','=',$day],
                 ['class','=',$class]   
            ])->get()->toarray();
            //dd($check,count($check));
            //dd($cusId,$class,$classStartTime);
            //本日第一筆的第n班紀錄
            if (count($check)==0){
                $DBdata=[
                    'employee_id'=>$employeeID,
                    'customer_id'=>$cusId,
                    'year'=>$year,
                    'month'=>$month,
                    'day'=>$day,
                    'class'=>$class,
                    'PunchInTime'=>$now,
                    'start'=>$classStartTime,
                    'end'=>$classEndTime,
                ];
                //dd($DBdata);
                $punch= Punch::create($DBdata);
                return response()->json([$data],200);
            }
            else{
                return "打卡失敗，您已經打過卡";
            }
            //dd($now,$year,$month,$day,$time);
            
            //return "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)";
        }
        else{
            return 'punch in failure,please punch in your workplace in the work time!';
        }

    }

    public function api_PunchOut(Request $request)
    {
        $request=$request->all();

        $now=date("Y-m-d H:i:s");
        $year=date('Y', strtotime($now));
        $month=intval(date('m', strtotime($now)));
        $day=intval(date('d', strtotime($now)));
        $time=date('H:i', strtotime($now));        
        $employeeID=$request['EmployeeID'];

        //查詢公告、員工名字
        $announce=DB::table('announcements')->latest()->take(5)->get();
        $employeeName=DB::table('employees')->where('member_sn','=',$employeeID)->first()->member_name;

        //查詢當天排班表
        $schedule=DB::table('schedules')->where([
                    ['employee_id','=',$employeeID],
                    ['year','=',$year],
                    ['month','=',$month]
                    ])->get()->toarray();
        //dd($announce,count($announce),$employeeName,count($schedule),$schedule);

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
                $allowPunchStartTime=date("Y-m-d H:i",strtotime("-10 minute",strtotime($classStartTime)));//第j個班開始時十分鐘開放打卡
                $allowPunchEndTime=date("Y-m-d H:i",strtotime("+20 minute",strtotime($classStartTime)));//第j個班開始時十分鐘開放打卡
                //dd($schedule);
                
                if(strtotime($now) <= strtotime($classEndTime) && strtotime($now) >= strtotime($allowPunchStartTime)){//現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                    $queryLocation=DB::table('customers')->where('customer_id','=',$schedule[$i]->customer_id)->first();
                    $cusId=$schedule[$i]->customer_id;
                    $class=$queryClassName;
                    //dd($cusId,$class,$classStartTime,$classEndTime);
                    $lat2=$queryLocation->lat;
                    $lng2=$queryLocation->lng;
                    break;
                }
            }
            //dd($now,strtotime($now) <= strtotime($classEndTime) && strtotime($now) >= strtotime($allowPunchStartTime));
        }
                

        //計算距離演算法
        //先判斷參數是否有設定，若無被設定表示沒有班，跳錯誤訊息
        if(isset($lat2) && isset($lng2))
        {
            $lat2 = ($lat2 * pi() ) / 180;
            $lng2 = ($lng2 * pi() ) / 180;
        }
        else{
            return "打卡失敗，可能不是你的上班時間，請於您的工作場地及上班前十分鐘再試行打卡。";
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
            $check=Punch::where([
                 ['employee_id','=',$employeeID],
                 ['customer_id','=',$cusId],
                 ['year','=',$year],
                 ['month','=',$month],
                 ['day','=',$day],
                 ['class','=',$class]   
            ])->get()->toarray();
            //dd($check,count($check),$check[0]['id']);
            //dd($cusId,$class,$classStartTime);
            if (count($check)==1){
                $DBdata=[
                    'PunchOutTime'=>$now,
                ];

                $punch= Punch::where('id','=',$check[0]['id'])->update($DBdata);
                return "打下班卡成功";
            }
            else{
                return "打卡失敗，查無上班打卡記錄，請補打卡";
            }
            //dd($now,$year,$month,$day,$time);
            
            //return "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)";
        }
        else{
            return 'punch in failure,please punch in your workplace in the work time!';
        }
    }
}
