<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CustomersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?? 20;

        $customer = Customer::orderBy('id','desc')
            ->paginate($limit)
            ->appends($request->query());

        return response($customer,Response::HTTP_OK);
        //return view('index',['employees'=> $employee]);
    }

    public function show(Customer $customer)
    {
        return view("customer");
    }

    public function store(Request $request){
        try{
            $rules=[
            "cid" => "required|unique:customers,customer_sn",
            "group" => "required",
            "status"=>"required",
            //"active"=>"required",
            "name"=>"required|unique:customers,firstname",
            "phone"=>"required",
            "addr"=>"required",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "cid.required" => '"客戶編號"為必填資料',
                "cid.unique"=>'"客戶編號"己經被使用，請重新填入',
                "group.required" => '"客戶群組"為必填資料',
                "status.required"=>'"status"為必填資料',
                //"active.required"=>'"active"為必填資料',
                "name.required"=>'"客戶名稱"為必填資料',
                "name.unique"=>'"客戶名稱"不可以重覆',
                "phone.required"=>'"電話號碼"為必填資料',
                "addr.required"=>'"通訊地址"為必填資料',
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
        $cus_id = $request->input('cid');
        $cus_group = $request->input('group');
        $cus_status = $request->input('status');
        //$cus_active = $request->input('active');
        $cus_name = $request->input('name');
        $cus_phone = $request->input('phone');
        $cus_addr = $request->input('addr');
        $cus_ip = $request->input('ip');
        $cus_lat = $request->input('lat');
        $cus_lng = $request->input('lng');
        $cus_account = $request->input('member_account');
        $cus_password = $request->input('member_password');
        $cus_password_text = $cus_password;
        
        $salt =  substr(md5(uniqid(rand(), true)), 0, 9);

        if ($cus_ip=="")
        {$cus_ip=0;}

        if ($cus_account!="")
        {$cus_password = sha1($salt . sha1($salt . sha1($cus_password)));}
        else
        {$cus_password = "";
        $cus_account="";
        $salt="";}

        $customer= new Customer();

        $count = $customer::where('customer_sn', '=', $cus_id)->count();
        //$max_id = DB::table('customers')->max('customer_id');
        //dd($max_id);

        if ($count==0){
            $data=[
            'customer_sn'=>$cus_id,
            'customer_group_id'=>$cus_group,
            'status'=>$cus_status,
            //'active'=>$cus_active,
            'firstname'=>$cus_name,
            'addr'=>$cus_addr,
            'tel'=>$cus_phone,
            'ip'=>$cus_ip,
            'lat'=>$cus_lat,
            'lng'=>$cus_lng,
            'account'=>$cus_account,
            'password'=>$cus_password,
            'password_text'=>$cus_password_text,
            'salt'=>$salt,
            ];

            $customer= Customer::create($data);
            $customer= Customer::orderBy('customer_id','asc')
            ->paginate(5);
        
            //dd($organize);
       
//dd($data);
        return redirect()->route('customer_desc');
        //return view("update_organize",["organizes"=>$organize]);
        }
    }

    public function update(Request $request){
        if (! Gate::allows('group_admin')) {
            abort(403,'抱歉，你沒有使用此功能權限');
        }

        try{
            $rules=[
            "cid" => "required",
            "group" => "required",
            "status"=>"required",
            //"active"=>"required",
            "name"=>"required",
            "phone"=>"required",
            "addr"=>"required",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "cid.required" => '"客戶編號"為必填資料',
                "group.required" => '"客戶群組"為必填資料',
                "status.required"=>'"status"為必填資料',
                //"active.required"=>'"active"為必填資料',
                "name.required"=>'"客戶名稱"為必填資料',
                //"name.unique"=>'"客戶名稱"不可以重覆',
                "phone.required"=>'"電話號碼"為必填資料',
                "addr.required"=>'"通訊地址"為必填資料',
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
        $cus_id = $request->input('cid');
        $cus_group = $request->input('group');
        $cus_status = $request->input('status');
        //$cus_active = $request->input('active');
        $cus_name = $request->input('name');
        $cus_phone = $request->input('phone');
        $cus_addr = $request->input('addr');
        $cus_ip = $request->input('ip');
        $cus_lat = $request->input('lat');
        $cus_lng = $request->input('lng');
        $cus_account = $request->input('member_account');
        $cus_password = $request->input('member_password');
        $cus_password_text = $cus_password;

        $salt =  substr(md5(uniqid(rand(), true)), 0, 9);

        if ($cus_ip=="")
        {$cus_ip=0;}

        if ($cus_account!="")
        {$cus_password = sha1($salt . sha1($salt . sha1($cus_password)));}
        else
        {$cus_password ="";
        $cus_account="";
        $salt="";}

        //$customer= new Customer();

            $data=[

            'customer_group_id'=>$cus_group,
            'status'=>$cus_status,
            //'active'=>$cus_active,
            'firstname'=>$cus_name,
            'addr'=>$cus_addr,
            'tel'=>$cus_phone,
            'ip'=>$cus_ip,
            'lat'=>$cus_lat,
            'lng'=>$cus_lng,
            'account'=>$cus_account,
            'password'=>$cus_password,
            'password_text'=>$cus_password_text,
            'salt'=>$salt,
            ];

            $customer=Customer::where('customer_sn','=',$cus_id)->update($data);

        return redirect()->route('customer_desc');

        
    }


    public function show_result_asc()
    {
        //$customer= Customer::orderBy('customer_id','asc')->paginate(20);
        //return view("show_customer",["customers"=>$customer]);
        $cus=DB::table('customers')
            ->join('cus_group', 'customers.customer_group_id', '=', 'cus_group.id')
            ->join('cus_status','customers.customer_group_id', '=', 'cus_status.id')
            ->orderby('customer_id','asc')
            ->paginate(20);
        
        return view("show_customer",["customers"=>$cus]);

    }

    public function show_result_desc()
    {
        //$customer= Customer::orderBy('customer_id','desc')->paginate(20);
        //return view("show_customer",["customers"=>$customer]);
        $cus=DB::table('customers')
            ->join('cus_group', 'customers.customer_group_id', '=', 'cus_group.id')
            ->join('cus_status','customers.customer_group_id', '=', 'cus_status.id')
            ->orderby('customer_id','desc')
            ->paginate(20);

        return view("show_customer",["customers"=>$cus]);
    }

    public function destroy(Request $request,$Delete_id)
    {   if (! Gate::allows('group_admin')) {
            abort(403,'抱歉，你沒有使用此功能權限');
        }
        
        $deleted=DB::table('customers')->where('customer_id','=',$Delete_id)->delete();
        //return response(null,Response::HTTP_NO_CONTENT);
        return redirect()->route('customer_desc');
    }

    public function request(Request $request,$Request_id){
        if (! Gate::allows('group_admin')) {
            abort(403,'抱歉，你沒有使用此功能權限');
        }
        $customer=DB::table('customers')->where('customer_id','=',$Request_id)->get();
        $group=$customer->pluck('customer_group_id')->first();
        $status=$customer->pluck('status')->first();
        //$active=$customer->pluck('active')->first();

        if($group==1)
        {$group='VIP客戶';}
        else
        {$group='普通客戶';}

        if($status==1)
        {$status='現有客戶';}
        else
        {$status='非現有客戶';}

        //if($active==1)
        //{$active='現有客戶';}
        //else
        //{$active='非現有客戶';}
       

        //return view("edit_customer",["customers"=>$customer,"group"=>$group,"status"=>$status,"active"=>$active]);
        return view("edit_customer",["customers"=>$customer,"group"=>$group,"status"=>$status]);

    }
}
