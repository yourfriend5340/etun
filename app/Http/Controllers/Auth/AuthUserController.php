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
        $month=date('m', strtotime($now));
        $day=date('d', strtotime($now));
        $time=date('H:i', strtotime($now));        
        $employeeID=$request['EmployeeID'];

        //查詢公告、員工名字
        $announce=DB::table('announcements')->latest()->take(5)->get();
        $employeeName=DB::table('employees')->where('member_sn','=',$employeeID)->first()->member_name;

        //查詢當天排班表
        $schedule=DB::table('schedules')->where([
                    ['employee_id','=',$employeeName],
                    ['year','=',$year],
                    ['month','=',$month],
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
                
                $allowPunchStartTime=date("H:i",strtotime("-10 minute",strtotime($classStartTime)));//第j個班開始時十分鐘開放打卡
                //dd($classList,$countClassList,$queryClassName,$time,$allowPunchStartTime,$classEndTime);
                //dd((strtotime($time)<strtotime($classEndTime) && strtotime($time)>=strtotime($allowPunchStartTime)));
                
                if(strtotime($time)<strtotime($classEndTime) && strtotime($time)>=strtotime($allowPunchStartTime)){//現在時間介於第j個班的開始時間及結束時間區間時，設定變數並break loop
                    $queryLocation=DB::table('customers')->where('firstname','=',$schedule[$i]->customer_id)->first();
                    $lat2=$queryLocation->lat;
                    $lng2=$queryLocation->lng;
                    break;
                }
                else{
                    $lat2=0;
                    $lng2=0;
                }   
            }
        }
                
        //計算距離演算法
        $earthRadius = 6368000; //地球平均半徑(公尺)

        $lat1=$request['lat'];
        $lng1=$request['lng'];

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
    
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        
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
                if($year>=4){
                    if($months>=10){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check per 5 years,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+5 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                            }
                }
            }
            if($age_years>=40 && $age_years<65){
                if($year>=2){
                    if($months>=10){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check per 3 years,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+3 year",strtotime("$body_check"))),
                                'annoucement'=>$announce_arr
                                ]);
                            }
                }
            }
            if($age_years>=65){
                if($months>=10){
                        $data=(['message' => "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)",
                                'person_announcement'=>"you are $age_years old,last check up date is $body_check ,you have to check every year,please check and hand back the health examination before: ".date("Y-m-d",strtotime("+1 year",strtotime("$body_check"))),
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
            ])->get()->toarray();
//dd($check);
            if (count($check)==0){
                $DBdata=[
                    'employee_id'=>$employeeID,
                    'year'=>$year,
                    'month'=>$month,
                    'day'=>$day,
                    'PunchInTime1'=>$now,
                    'scheduleStart1'=>$classStartTime,
                    'scheduleEnd1'=>$classEndTime,
                ];
                //dd($DBdata);
                $punch= Punch::create($DBdata);
                return response()->json([$data],200);
            }
            else{
                for($i=1;$i<=10;$i++){

                    if ($check[0]['PunchInTime'.$i]==""){

                        $sub=abs(strtotime($time)-strtotime($check[0]['scheduleEnd'.($i-1)]));//計算現在時間跟上一班下班時間的秒差
                    
                        if ($sub<600){//離上一班下班，上下十分鐘內才可打卡
                            DB::table('punch_record')->where([
                                ['employee_id','=',$employeeID],
                                ['year','=',$year],
                                ['month','=',$month],
                                ['day','=',$day],   
                            ])->update(["PunchInTime$i"=>$now]);
                            return response()->json([$data],200);
                            
                        }
                        else{
                            return "打卡失敗，請於您的工作場地及上班前十分鐘再試行打卡";
                        }

                    }
                }
                
            }
            //dd($now,$year,$month,$day,$time);
            
            //return "punch in success!!!(distance-measuring error about $calculatedDistance meters from your workplace)";
        }
        else{
            return 'punch in failure,please punch in your workplace in the work time!';
        }

    }
}
