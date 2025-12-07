<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Employee;
use App\Models\Organize;
use App\Models\Customer;
use App\Models\ClockSalary;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;






class EmployeeController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$employee=Employee::get();
        //return response(['data'=> $employee],Response::HTTP_OK);
        
        $limit = $request->limit ?? 20;

        $employee = Employee::orderBy('id','asc')
            ->paginate($limit)
            ->appends($request->query());
        //dd($employee);
        //return response($employee,Response::HTTP_OK);
        //return view('employee/store',['employees'=> $employee,]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     protected function validator(array $data)
     {
         return Validator::make($data, [
             'uid' => ['required', 'integer', 'unique:employees'],
             'organize' => ['required', 'string'],
             'area' => ['required', 'string','confirmed'],
         ]);
     }

    public function store(Request $request)
    {

        try{
            $rules=[
            "uid" => "bail|required|integer|unique:employees,member_sn",
            "organize" => "required",
            "area" => "required",
            "member_name" => "required|unique:employees,member_name",
            "SSN" => "required|min:10|unique:employees,SSN",
            "Birthday" => "required",
            "mobile" => "required|min:10",
            "Gender" => "required",
            //"Blood_type" => "required",
            "addr" => "required",
            "current_addr" => "required",
            //"salary" => "integer",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "uid.required" => '"職工編號"為必填資料',
                "uid.unique" => '"職工編號"重覆，請另定義職工編號',
                "member_name.required"=>'"姓名"為必填資料',
                "member_name.unique"=>"'姓名'發現有同名同姓的人，請利用括號區分，如：陳小明(職工編號)",
                "organize.required" => '請選擇"所屬組織"',
                "area.required" => '請選擇"所屬區域"',
                "SSN.required"=>'"身份證字號"為必填資料',
                "SSN.min" => '"身份證字號為10個字元"',
                "SSN.unique"=>'"身份證字號"不可重複',
                "Birthday.required"=>'"生日"為必填資料',
                "mobile.required"=>'"手機"為必填資料',
                "mobile.min"=>'"手機為10個字元"',
                "Gender.required"=>'請選擇"性別"',
                "Blood_type.required"=>'請選擇"血型"',
                "Branch.required"=>'請選擇"役別"',
                "addr.required"=>'"戶籍地址"為必填資料',
                "current_addr.required"=>'"通訊地址"為必填資料',
                //"salary.required"=>'"薪資"為必填資料',

            ];
            $validResult = $request->validate($rules, $message);
            //dd($validResult);
            //if ($validator->fails()) {
            //    return redirect()->back();
            //}
        }
       catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
            return $errorMessage;
        }

        //if (isset ($errorMessageData)){
        //var_dump($errorMessages);}
        
        $membersn = $request->input('uid');
        $organize = $request->input('organize');
        $area = $request->input('area');
        $name = $request->input('member_name');
        $SSN = $request->input('SSN');//身份字號
        $birthday = $request->input('Birthday');
        $height = $request->input('Height');
        $weight = $request->input('Weight');
        $phone = $request->input('member_phone');
        $mobile = $request->input('mobile');
        $gender = $request->input('Gender');
        //dd($gender);
        $blood = $request->input('Blood_type');
        $branch = $request->input('Branch');
        $mail = $request->input('mail');
        $addr = $request->input('addr');
        $current_addr = $request->input('current_addr');

        //$ID_front= $request->file('IDCard_front');
        //$ID_back = $request->file('IDCard_back');
        //$employeeCard = $request->file('EmployeeCard');
        //$other_card = $request->file('OthersCard');

        if ($request->input('drive')!=null)
        {$drive = implode(",",$request->input('drive'));}
        else
        {$drive = null;}

        if ($request->input('language')!=null)
        {$language = implode(",",$request->input('language'));}
        else
        {$language=null;}

        $school = $request->input('School');
        $department = $request->input('Department');
        $graduate = $request->input('graduate');
        $status = $request->input('status');
        $work_place = $request->input('work_place');
        $position = $request->input('position');
        $salary = $request->input('salary');
        $regist_day = $request->input('regist');
        $leave_day = $request->input('leave');
        $check_send = $request->input('check_send');
        $check_back = $request->input('check_back');
        $agreement_send = $request->input('agreement_send');
        $agreement_back = $request->input('agreement_back');
        $labor_date = $request->input('labor_date');
        $labor_account = $request->input('labor_account');
        $retirement_account = $request->input('retirement_account');
        $health_date = $request->input('health_date');
        $health_account = $request->input('health_account');
        $life_date = $request->input('life_date');
        $group_date = $request->input('group_date');
        $care_date = $request->input('care_date');
        $checkup = $request->input('checkup');
        $memo = $request->input('memo');

        $member_account = $request->input('member_account');
        $member_password_text = $request->input('member_password');
        $salt =  substr(md5(uniqid(rand(), true)), 0, 9);

        if ($member_account!=null)
        {$member_password = sha1($salt . sha1($salt . sha1($member_password_text)));}
        else
        {$member_password = null;
        $salt=null;}

        $IDCard_front_imageName=null;
        $IDCard_back_imageName=null;
        $EmployeeCard_imageName=null;
        $OthersCard_imageName=null;

        if ($request->file('IDCard_front')!=null){
            $IDCard_front_imageName = $membersn.'_IDCard_front.'.$request->file('IDCard_front')->extension();
            $path = $request->file('IDCard_front')->storeas('credential/IDCard_front',$IDCard_front_imageName);
        }

        if ($request->file('IDCard_back')!=null){
            $IDCard_back_imageName = $membersn.'_IDCard_back.'.$request->file('IDCard_back')->extension();
            $path = $request->file('IDCard_back')->storeas('credential/IDCard_back',$IDCard_back_imageName);
        }

        if ($request->file('EmployeeCard')!=null){
            $EmployeeCard_imageName = $membersn.'_EmployeeCard.'.$request->file('EmployeeCard')->extension();
            $path = $request->file('EmployeeCard')->storeas('credential/EmployeeCard',$EmployeeCard_imageName);
        }

        if ($request->file('OthersCard')!=null){
        $OthersCard_imageName = $membersn.'_OthersCard.'.$request->file('OthersCard')->extension();
        $path = $request->file('OthersCard')->storeas('credential/OthersCard',$OthersCard_imageName);
        }

        $data=[
            'member_name'=>$name,
            'member_sn'=>$membersn,
            'member_phone'=>$phone,
            'member_account'=>$member_account,
            'member_password'=>$member_password,
            'member_password_text'=>$member_password_text,
            'organize'=>$organize,
            'area'=>$area,
            'SSN'=>$SSN,
            'Gender'=>$gender,
            'Blood_type'=>$blood,
            'Birthday'=>$birthday,
            'Height'=>$height,
            'Weight'=>$weight,
            'Branch'=>$branch,
            'mobile'=>$mobile,
            'mail'=>$mail,
            'pic_route1'=>'/etun/storage/app/credential/IDCard_front/'.$IDCard_front_imageName,
            'pic_route2'=>'/etun/storage/app/credential/IDCard_back/'.$IDCard_back_imageName,
            'pic_route3'=>'/etun/storage/app/credential/EmployeeCard/'.$EmployeeCard_imageName,
            'pic_route4'=>'/etun/storage/app/credential/OthersCard/'.$OthersCard_imageName,
            'driver'=>$drive,
            'language'=>$language,
            'school'=>$school,
            'department'=>$department,
            'graduate'=>$graduate,
            'status'=>$status,
            //'work_place'=>$work_place,
            'position'=>$position,
            //'salary'=>$salary,
            'register'=>$regist_day,
            'leave'=>$leave_day,
            'check_send'=>$check_send,
            'check_back'=>$check_back,
            'agreement_send'=>$agreement_send,
            'agreement_back'=>$agreement_back,
            'labor_date'=>$labor_date,
            'labor_account'=>$labor_account,
            'retirement_account'=>$retirement_account,
            'health_date'=>$health_date,
            'health_account'=>$health_account,
            'life_date'=>$life_date,
            'group_date'=>$group_date,
            'care_date'=>$care_date,
            'memo'=>$memo,
            'salt'=>$salt,
            'addr'=>$addr,
            'current_addr'=>$current_addr,
            'checkup'=>$checkup
        ];
        
        $employee= Employee::create($data);
    
        //儲存鐘點費
        $clock = [];
        //$month = [];
        $clock_salary=$request->input('clock_salary');
        $clock_salary_array=array_filter(explode(',',$clock_salary));
        $num=count($clock_salary_array);
        //dd($clock_salary_array);
        $coffset = 0;
        $moffset = 0;

        for($i=0;$i<$num;$i+=4){
            //if($clock_salary_array[$i+1] == '時薪'){
                $clock[$coffset]['name']= $clock_salary_array[$i];
                $clock[$coffset]['type']= $clock_salary_array[$i+1];
                $clock[$coffset]['amount'] = $clock_salary_array[$i+2];
                $clock[$coffset]['hour'] = $clock_salary_array[$i+3];
                $coffset++;
            //}
            //else{
            //    $month[$moffset]['name']= $clock_salary_array[$i];
            //    $month[$moffset]['type']= $clock_salary_array[$i+1];
            //    $month[$moffset]['amount'] = $clock_salary_array[$i+2];
            //    $moffset++;
            //}
        }

        $cnum=count($clock);
        //$mnum=count($month);
        for ($i=0;$i<$cnum;$i++){
            $data=[
            'member_sn'=>$membersn,
            'member_name'=>$name,
            'customer'=>$clock[$i]['name'],
            'salaryType'=>$clock[$i]['type'],
            'salary'=>$clock[$i]['amount'],
            'hour'=>$clock[$i]['hour'],
            ];

            $count = DB::table('clock_salary')->where([
                ['member_sn',$membersn],
                ['customer',$clock[$i]['name']],

            ])->count();

            if($count == 0)
            {    
                $store = ClockSalary::create($data);
            }
            else{
                DB::table('clock_salary')->where([
                    ['member_sn',$membersn],
                    ['customer',$clock[$i]['name']]
                ])->update(['salary'=>$clock[$i]['amount'],'salaryType'=>$clock[$i]['type'],'hour'=>$clock[$i]['hour']]);
                
                //$errorMessage = '查到'.$name.'已在'.$clock[$i]['name'].'設定'.$clock[$i]['type'].'，請利用修改功能調整';
                // return Redirect::back()->withErrors($errorMessage)->withInput();
                // break;
            }
        }

        // for ($i=0;$i<$mnum;$i++){
        //     $data=[
        //     'member_sn'=>$membersn,
        //     'member_name'=>$name,
        //     'customer'=>$month[$i]['name'],
        //     'salaryType'=>$month[$i]['type'],
        //     'salary'=>$month[$i]['amount'],
        //     ];

        //     $count = DB::table('clock_salary')->where([
        //         ['member_sn',$membersn],
        //         ['customer',$month[$i]['name']],
        //     ])->count();

        //     if($count == 0)
        //     {    
        //         $store = ClockSalary::create($data);
        //     }
        //     else{
        //         DB::table('clock_salary')->where([
        //             ['member_sn',$membersn],
        //             ['customer',$month[$i]['name']]
        //         ])->update(['salary'=>$month[$i]['amount'],'salaryType'=>$month[$i]['type']]);

        //         //$errorMessage = '查到'.$name.'已在'.$month[$i]['name'].'設定'.$month[$i]['type'].'，請利用修改功能調整';
        //         // return Redirect::back()->withErrors($errorMessage)->withInput();
        //         // break;
        //     }
        // }

        return redirect()->route('employee_desc');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //return response($employee,Response::HTTP_OK);
        //return view('employee');
        //$employee= Employee::orderBy('id','desc')->paginate(20);
        $customer= Customer::orderBy('customer_id','asc')->get();
        $organize= Organize::orderBy('id','asc')->get();


    return view("employee",["organizes"=>$organize],["customers"=>$customer]);
   
    //return view("employee");
    }

    public function show_result_asc()
    {
        //return view('home');
        $employee= Employee::orderBy('id','asc')
            ->paginate(20);
        
        //return view("update_employee",["employees"=>$employee,]);

        return view("show_employee",["employees"=>$employee,]);

    }

    public function show_result_desc()
    {
        //return view('home');
        $employee= Employee::orderBy('id','desc')
            ->paginate(20);

        //return view("update_employee",["employees"=>$employee,]);

        return view("show_employee",["employees"=>$employee,]);


    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
            try{
            $rules=[
            "uid" => "bail|required|integer",
            "organize" => "required",
            "area" => "required",
            "member_name" => "required",
            "SSN" => "required|min:10",
            "Birthday" => "required",
            "mobile" => "required|min:10",
            "Gender" => "required",
            //"Blood_type" => "required",
            "addr" => "required",
            "current_addr" => "required",
            //"salary" => "integer",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "uid.required" => '"職工編號"為必填資料',
                //"uid.unique" => '"職工編號"重覆，請另定義職工編號',
                "member_name.required"=>'"姓名"為必填資料',
                //"member_name.unique"=>"'姓名'發現有同名同姓的人，請利用括號區分，如：陳小姐(職工編號)",
                "organize.required" => '請選擇"所屬組織"',
                "area.required" => '請選擇"所屬區域"',
                "SSN.required"=>'"身份證字號"為必填資料',
                "SSN.min" => '"身份證字號為10個字元"',
                //"SSN.unique"=>'"身份證字號"不可重複',
                "Birthday.required"=>'"生日"為必填資料',
                "mobile.required"=>'"手機"為必填資料',
                "mobile.min"=>'"手機為10個字元"',
                "Gender.required"=>'請選擇"性別"',
                "Blood_type.required"=>'請選擇"血型"',
                "Branch.required"=>'請選擇"役別"',
                "addr.required"=>'"戶籍地址"為必填資料',
                "current_addr.required"=>'"通訊地址"為必填資料',
                //"salary.required"=>'"薪資"為必填資料',

            ];
            $validResult = $request->validate($rules, $message);
            //dd($validResult);
            //if ($validator->fails()) {
            //    return redirect()->back();
            //}
        }
       catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
            return $errorMessage;
        }

        //if (isset ($errorMessageData)){
        //var_dump($errorMessages);}
        
        $membersn = $request->input('uid');
        $organize = $request->input('organize');
        $area = $request->input('area');
        $name = $request->input('member_name');
        $SSN = $request->input('SSN');//身份字號
        $birthday = $request->input('Birthday');
        $height = $request->input('Height');
        $weight = $request->input('Weight');
        $phone = $request->input('member_phone');
        $mobile = $request->input('mobile');
        $gender = $request->input('Gender');

        $blood = $request->input('Blood_type');
        $branch = $request->input('Branch');
        $mail = $request->input('mail');
        $addr = $request->input('addr');
        $current_addr = $request->input('current_addr');


        if ($request->input('drive')!=null)
        {$drive = implode(",",$request->input('drive'));}
        else
        {$drive = null;}

        if ($request->input('language')!=null)
        {$language = implode(",",$request->input('language'));}
        else
        {$language=null;}

        $school = $request->input('School');
        $department = $request->input('Department');
        $graduate = $request->input('graduate');
        $status = $request->input('status');
        //$work_place = $request->input('work_place');
        $position = $request->input('position');
        //$salary = $request->input('salary');
        $clock_salary = $request->input('clock_salary');
        $regist_day = $request->input('regist');
        $leave_day = $request->input('leave');
        $check_send = $request->input('check_send');
        $check_back = $request->input('check_back');
        $agreement_send = $request->input('agreement_send');
        $agreement_back = $request->input('agreement_back');
        $labor_date = $request->input('labor_date');
        $labor_account = $request->input('labor_account');
        $retirement_account = $request->input('retirement_account');
        $health_date = $request->input('health_date');
        $health_account = $request->input('health_account');
        $life_date = $request->input('life_date');
        $group_date = $request->input('group_date');
        $care_date = $request->input('care_date');
        $checkup= $request->input('checkup');
        $memo = $request->input('memo');

        $member_account = $request->input('member_account');
        $member_password_text = $request->input('member_password');
        $salt =  substr(md5(uniqid(rand(), true)), 0, 9);

        if ($member_account!=null)
        {$member_password = sha1($salt . sha1($salt . sha1($member_password_text)));}
        else
        {$member_password = null;
        $salt=null;}


        $img_route=employee::where('member_sn',$request->input('uid'))->get();

        //$IDCard_front_imageName=null;
        //$IDCard_back_imageName=null;
        //$EmployeeCard_imageName=null;
        //$OthersCard_imageName=null;
        $IDCard_front_addr=$img_route[0]['pic_route1'];
        $IDCard_back_addr=$img_route[0]['pic_route2'];
        $EmployeeCard_addr=$img_route[0]['pic_route3'];
        $OtherCard_addr=$img_route[0]['pic_route4'];
        //dd($IDCard_front_addr,$IDCard_back_addr,$EmployeeCard_addr,$OtherCard_addr);

        if ($request->file('IDCard_front')!=null){
            $IDCard_front_imageName = $membersn.'_IDCard_front.'.$request->file('IDCard_front')->extension();
            $path = $request->file('IDCard_front')->storeas('credential/IDCard_front',$IDCard_front_imageName);

        }

        if ($request->file('IDCard_back')!=null){
            $IDCard_back_imageName = $membersn.'_IDCard_back.'.$request->file('IDCard_back')->extension();
            $path = $request->file('IDCard_back')->storeas('credential/IDCard_back',$IDCard_back_imageName);
        }

        if ($request->file('EmployeeCard')!=null){
            $EmployeeCard_imageName = $membersn.'_EmployeeCard.'.$request->file('EmployeeCard')->extension();
            $path = $request->file('EmployeeCard')->storeas('credential/EmployeeCard',$EmployeeCard_imageName);
        }

        if ($request->file('OthersCard')!=null){
            $OthersCard_imageName = $membersn.'_'.date("Y-m-d H:i:s").'_OthersCard.'.$request->file('OthersCard')->extension();
            $path = $request->file('OthersCard')->storeas('credential/OthersCard',$OthersCard_imageName);
            $OtherCard_addr="/etun/storage/app/credential/OthersCard',$OthersCard_imageName";
        }

        $request=Employee::where('member_name','=',$name)->count();
//dd($salary);

/*
        if($request==0){
        
            $data=[
            'member_name'=>$name,
            //'member_sn'=>$membersn,
            'member_phone'=>$phone,
            'member_account'=>$member_account,
            'member_password'=>$member_password,
            'member_password_text'=>$member_password_text,
            'organize'=>$organize,
            'area'=>$area,
            //'SSN'=>$SSN,
            'Gender'=>$gender,
            'Blood_type'=>$blood,
            'Birthday'=>$birthday,
            'Height'=>$height,
            'Weight'=>$weight,
            'Branch'=>$branch,
            'mobile'=>$mobile,
            'mail'=>$mail,
            'pic_route1'=> $IDCard_front_addr,
            'pic_route2'=> $IDCard_back_addr,
            'pic_route3'=> $EmployeeCard_addr,
            'pic_route4'=> $OtherCard_addr,
            'driver'=>$drive,
            'language'=>$language,
            'school'=>$school,
            'department'=>$department,
            'graduate'=>$graduate,
            'status'=>$status,
            'work_place'=>$work_place,
            'position'=>$position,
            'salary'=>$salary,
            'register'=>$regist_day,
            'leave'=>$leave_day,
            'check_send'=>$check_send,
            'check_back'=>$check_back,
            'agreement_send'=>$agreement_send,
            'agreement_back'=>$agreement_back,
            'labor_date'=>$labor_date,
            'labor_account'=>$labor_account,
            'retirement_account'=>$retirement_account,
            'health_date'=>$health_date,
            'health_account'=>$health_account,
            'life_date'=>$life_date,
            'group_date'=>$group_date,
            'care_date'=>$care_date,
            'memo'=>$memo,
            'salt'=>$salt,
            'addr'=>$addr,
            'current_addr'=>$current_addr,
            'checkup'=>$checkup,
            ];
        }
*/
        if($request!=0){

            $data=[
                //'member_name'=>$name,
                //'member_sn'=>$membersn,
                'member_phone'=>$phone,
                'member_account'=>$member_account,
                'member_password'=>$member_password,
                'member_password_text'=>$member_password_text,
                'organize'=>$organize,
                'area'=>$area,
                'SSN'=>$SSN,
                'Gender'=>$gender,
                'Blood_type'=>$blood,
                'Birthday'=>$birthday,
                'Height'=>$height,
                'Weight'=>$weight,
                'Branch'=>$branch,
                'mobile'=>$mobile,
                'mail'=>$mail,
                'pic_route1'=> $IDCard_front_addr,
                'pic_route2'=> $IDCard_back_addr,
                'pic_route3'=> $EmployeeCard_addr,
                'pic_route4'=> $OtherCard_addr,
                'driver'=>$drive,
                'language'=>$language,
                'school'=>$school,
                'department'=>$department,
                'graduate'=>$graduate,
                'status'=>$status,
                //'work_place'=>$work_place,
                'position'=>$position,
                //'salary'=>$salary,
                'register'=>$regist_day,
                'leave'=>$leave_day,
                'check_send'=>$check_send,
                'check_back'=>$check_back,
                'agreement_send'=>$agreement_send,
                'agreement_back'=>$agreement_back,
                'labor_date'=>$labor_date,
                'labor_account'=>$labor_account,
                'retirement_account'=>$retirement_account,
                'health_date'=>$health_date,
                'health_account'=>$health_account,
                'life_date'=>$life_date,
                'group_date'=>$group_date,
                'care_date'=>$care_date,
                'memo'=>$memo,
                'salt'=>$salt,
                'addr'=>$addr,
                'current_addr'=>$current_addr,
                'checkup'=>$checkup,
            ];
            
        }
        
        $employee=Employee::where('member_sn','=',$membersn)->update($data);

        //儲存鐘點費
        $clock = [];
        //$month = [];
        $clock_salary_array=explode(',',$clock_salary);
        array_pop($clock_salary_array);//因為最後是一個空值，故unset掉
        $num=count($clock_salary_array);

        $coffset = 0;
        $moffset = 0;

        for($i=0;$i<$num;$i+=4){
            //if($clock_salary_array[$i+1] == '時薪'){
                $clock[$coffset]['name']= $clock_salary_array[$i];
                $clock[$coffset]['type']= $clock_salary_array[$i+1];
                $clock[$coffset]['amount'] = $clock_salary_array[$i+2];
                $clock[$coffset]['hour'] = $clock_salary_array[$i+3];
                $coffset++;
            //}
            //else{
            //    $month[$moffset]['name']= $clock_salary_array[$i];
            //    $month[$moffset]['type']= $clock_salary_array[$i+1];
            //    $month[$moffset]['amount'] = $clock_salary_array[$i+2];
            //    $moffset++;
            //}
        }
        //$mnum=count($month);
        $cnum=count($clock);

        for ($i=0;$i<$cnum;$i++){
            $data=[
            'member_sn'=>$membersn,
            'member_name'=>$name,
            'customer'=>$clock[$i]['name'],
            'salaryType'=>$clock[$i]['type'],
            'salary'=>$clock[$i]['amount'],
            'hour'=>$clock[$i]['hour'],
            ];

            $count = DB::table('clock_salary')
            ->where([
                ['member_sn',$membersn],
                ['customer',$clock[$i]['name']],
            ])->count();

            if($count == 0)
            {    
                $store = ClockSalary::create($data);
            }
            else{
                DB::table('clock_salary')
                ->where([
                    ['member_sn',$membersn],
                    ['customer',$clock[$i]['name']]
                ])
                ->update(
                    ['salary'=>$clock[$i]['amount'],'salaryType'=>$clock[$i]['type'],'hour'=>$clock[$i]['hour']],
                );
            }
        }

        // for ($i=0;$i<$mnum;$i++){
        //     $data=[
        //     'member_sn'=>$membersn,
        //     'member_name'=>$name,
        //     'customer'=>$month[$i]['name'],
        //     'salaryType'=>$month[$i]['type'],
        //     'salary'=>$month[$i]['amount'],
        //     ];

        //     $count = DB::table('clock_salary')
        //     ->where([
        //         ['member_sn',$membersn],
        //         ['customer',$month[$i]['name']],
        //     ])->count();

        //     if($count == 0)
        //     {    
        //         $store = ClockSalary::create($data);
        //     }
        //     else{
        //         DB::table('clock_salary')
        //         ->where([
        //             ['member_sn',$membersn],
        //             ['customer',$month[$i]['name']]
        //         ])
        //         ->update(
        //             ['salary'=>$month[$i]['amount'],'salaryType'=>$month[$i]['type']],
        //         );
        //     }
        // }

        return redirect()->route('employee_desc');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$Delete_id)
    {   
        DB::table('clock_salary')->where('member_sn',$Delete_id)->delete();
        $deleted=DB::table('employees')->where('member_sn','=',$Delete_id)->delete();
        //return response(null,Response::HTTP_NO_CONTENT);
        return redirect()->route('employee_desc');
    }

    public function request(Request $request,$Request_id){
        $employee=DB::table('employees')->where('member_sn','=',$Request_id)->get();
        $customer=DB::table('customers')->select('customer_id','firstname')->orderBy('customer_id','asc')->get();
        $organize=DB::table('organizes')->select('id','company')->orderBy('id','asc')->get();


        $status=DB::table('employees')->where('member_sn','=',$Request_id)->get()->first();
        $clockSalary_status=DB::table('clock_salary')->where('member_sn','=',$Request_id)->get();


        // if($status->salary==null)
        // {$status=0;}
        // else
        // {$status=1;}

        // if(!isset($clockSalary_status->salary))
        // {$clockSalary_status=0;}
        // else
        // {$clockSalary_status=1;}

        //dd($Request_id,$employee,$customer,$organize);
        return view("edit_employee",["employees"=>$employee,"organizes"=>$organize,"customers"=>$customer,'status'=>$status,'clock_status'=>$clockSalary_status]);

    }

    public function requestName(Request $request,$requestName){
        $employee= Employee::where('member_name','like','%'.$requestName.'%')
            ->paginate(20);

        //return view("update_employee",["employees"=>$employee,]);

        return view("show_employee",["employees"=>$employee,'fromName'=>1]);

    }

public function api_upload_id(Request $request)
{
    
    dd([
        'class' => get_class($request),
        'interfaces' => class_implements($request),
        'parents' => class_parents($request),
    ]);
    // 驗證 -------------------------------------------------------
    $validated = $request->validate([
        'EmployeeID'     => 'required',
        'idcard_front'   => 'nullable|file|mimes:jpg,jpeg,png',
        'idcard_back'    => 'nullable|file|mimes:jpg,jpeg,png',
        'secondcard'     => 'nullable|file|mimes:jpg,jpeg,png',
    ]);

    $membersn = $validated['EmployeeID'];

    $IDCard_front_imageName = null;
    $IDCard_back_imageName = null;
    $EmployeeCard_imageName = null;

    // 上傳身分證正面 -------------------------------------------
    if ($request->hasFile('idcard_front')) {
        $IDCard_front_imageName = $membersn . '_IDCard_front.' . $request->file('idcard_front')->extension();
        $request->file('idcard_front')
            ->storeAs('employee_upload/credential/IDCard_front', $IDCard_front_imageName);
    }

    // 上傳身分證背面 -------------------------------------------
    if ($request->hasFile('idcard_back')) {
        $IDCard_back_imageName = $membersn . '_IDCard_back.' . $request->file('idcard_back')->extension();
        $request->file('idcard_back')
            ->storeAs('employee_upload/credential/IDCard_back', $IDCard_back_imageName);
    }

    // 上傳第二證件 -------------------------------------------
    if ($request->hasFile('secondcard')) {
        $EmployeeCard_imageName = $membersn . '_EmployeeCard.' . $request->file('secondcard')->extension();
        $request->file('secondcard')
            ->storeAs('employee_upload/credential/EmployeeCard', $EmployeeCard_imageName);
    }

    // 檢查員工是否存在 -----------------------------------------
    $request_emp = Employee::where('member_sn', $membersn)->count();
    if ($request_emp == 0) {
        return response()->json(['message' => '沒有權限上傳，請洽管理人員開通'], 403);
    }

    // 寫入資料庫 -----------------------------------------------
    Employee::where('member_sn', $membersn)->update([
        'upload_id_control' => 0,
        'upload_pic_route1' => $IDCard_front_imageName
            ? '/etun/storage/app/employee_upload/credential/IDCard_front/' . $IDCard_front_imageName
            : null,
        'upload_pic_route2' => $IDCard_back_imageName
            ? '/etun/storage/app/employee_upload/credential/IDCard_back/' . $IDCard_back_imageName
            : null,
        'upload_pic_route3' => $EmployeeCard_imageName
            ? '/etun/storage/app/employee_upload/credential/EmployeeCard/' . $EmployeeCard_imageName
            : null,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => '上傳完成',

    ], 200);
}

}