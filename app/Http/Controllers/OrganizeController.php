<?php

namespace App\Http\Controllers;

use App\Models\Organize;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;


class OrganizeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {


        return view("organize");


    }


    public function store(Request $request){
        try{
            $rules=[
            "organize_name" => "required|unique:organizes,company",
            "organize_tel" => "required",
            "organize_addr"=>"required"
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "organize_name.required" => '"名稱"為必填資料',
                "organize_name.unique"=>'"名稱"己經被使用，請重新填入',
                "organize_addr.required" => '"住址"為必填資料',
                "organize_tel.required"=>'"電話"為必填資料',
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
        
        $org_name = $request->input('organize_name');
        $org_addr = $request->input('organize_addr');
        $org_tel = $request->input('organize_tel');
        

        $organize= new Organize();

        $count = $organize::where('company', '=', $org_name)->count();



        if ($count==0){
            $data=[
            'company'=>$org_name,
            'addr'=>$org_addr,
            'tel'=>$org_tel,
            ];

            $organize= Organize::create($data);
            $organize= Organize::orderBy('id','asc')
            ->paginate(5);
        
            //dd($organize);
       

        return redirect()->route('organize_asc');
        //return view("update_organize",["organizes"=>$organize]);
        }
    }


    public function show()
    {
        //return view('home');
        $organize= Organize::orderBy('id','asc')
            ->paginate(20);

        //return view("update_employee",["employees"=>$employee,]);

        return view("show_organize",["organizes"=>$organize]);

    }


    public function request(Request $request,$Request_id){

        $organize=DB::table('organizes')->where('id','=',$Request_id)->orderBy('id','asc')->get();

        //dd($Request_id,$employee,$customer,$organize);
        return view("edit_organize",["organizes"=>$organize]);

    }

    public function destroy(Request $request,$Delete_id)
    {   
        $deleted=DB::table('organizes')->where('id','=',$Delete_id)->delete();
        //return response(null,Response::HTTP_NO_CONTENT);
        return redirect()->route('organize_asc');
    }

    public function update(Request $request)
    {
            try{
            $rules=[
            "organize_name" => "required",
            "organize_tel" => "required",
            "organize_addr"=> "required"
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "organize_name.required" => '"名稱"為必填資料',
                "organize_tel.required"=>'"電話"為必填資料',
                "organize_addr.required"=>'"住址"為必填資料',
            ];
            $validResult = $request->validate($rules, $message);

        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
        return $errorMessage;
        }
        
        $name = $request->input('organize_name');
        $tel = $request->input('organize_tel');
        $addr = $request->input('organize_addr');


        $data=[
            'company'=>$name,
            'tel'=>$tel,
            'addr'=>$addr,
        ];


        $organize=Organize::where('company','=',$name)->update($data);

        return redirect()->route('organize_asc');
    }
}
