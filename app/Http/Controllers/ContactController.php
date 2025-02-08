<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Contact;
use App\Models\ContactGroup;

class ContactController extends Controller
{
    public function groupindex()
    {
        return view("contact_group");
    }

    public function groupshow()
    {
        //return view('home');
        $group= ContactGroup::orderBy('id','asc')
            ->paginate(20);

        return view("show_contact_group",["groups"=>$group]);

    }

    public function groupstore(Request $request){
        try{
            $rules=[
            "group_name" => "required|unique:contact_group,groupName",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "group_name.required" => '"名稱"為必填資料',
                "group_name.unique" => '已有相同名稱'

            ];
            $validResult = $request->validate($rules, $message);

        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
            return $errorMessage;
        }
        
        $group_name = $request->input('group_name');

        $count = ContactGroup::where('groupName', '=', $group_name)->count();

        if ($count==0){
            $data=[
            'groupName'=>$group_name,
            ];

            ContactGroup::create($data);

            $group = ContactGroup::orderBy('id','asc')
            ->paginate(5);
    
            //return view("show_contact_group",["groups"=>$group]);

        return redirect()->route('contact_group.asc');
        }
    }

    public function grouprequest(Request $request,$Request_id){

        $group=DB::table('contact_group')->where('id','=',$Request_id)->orderBy('id','asc')->get();

        //dd($Request_id,$employee,$customer,$organize);
        return view("edit_contact_group",["groups"=>$group]);

    }

    public function groupdestroy(Request $request,$Delete_id)
    {   
        $deleted=DB::table('contact_group')->where('id','=',$Delete_id)->delete();
        //return response(null,Response::HTTP_NO_CONTENT);
        return redirect()->route('contact_group.asc');
    }

    public function groupupdate(Request $request)
    {
            try{
            $rules=[
            "group_name" => "required",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "organize_name.required" => '"名稱"為必填資料',
            ];
            $validResult = $request->validate($rules, $message);

        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
        return $errorMessage;
        }
        $old = $request->input('old_group_name');
        $group_name = $request->input('group_name');

        $data=[
            'groupName'=>$group_name,
        ];


        ContactGroup::where('groupName','=',$old)->update($data);

        return redirect()->route('contact_group.asc');
    }





    public function index()
    {

        $group= ContactGroup::orderBy('id','asc')->get();

        return view("contact",["groups"=>$group]);
    }

    public function show()
    {
        $group=DB::table('contact')
            ->join('contact_group', 'contact.gid', '=', 'contact_group.id')
            ->select('contact.id','contact.contactName','contact.contactPhone','groupName')
            ->orderby('groupName','asc')
            ->orderby('contact.id','asc')
            ->paginate(20);
        //$group = ContactGroup::orderBy('id','asc')->paginate(20);   

        return view("show_contact",["groups"=>$group]);

    }

    public function store(Request $request){
        try{
            $rules=[
            "group_name" => "required",
            "group_user_name" => "required",
            "group_user_phone" => "required",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "group_name.required" => '"群組"為必選資料',
                "group_user_name.required" => '"名稱"為必填資料',
                "group_user_phone.required" => '"電話為"必填資料'

            ];
            $validResult = $request->validate($rules, $message);

        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
            return $errorMessage;
        }
        
        $group_name = $request->input('group_name');
        $name = $request->input('group_user_name');
        $phone = $request->input('group_user_phone');

        $count = ContactGroup::where('groupName', '=', $group_name)->count();

        if ($count !=0){
            $gid = ContactGroup::select('id')->where('groupName','=',$group_name)->first();

            $data=[
            'gid'=>$gid->id,    
            'contactName'=>$name,
            'contactPhone'=>$phone,
            ];

            Contact::create($data);

            //$group = Contact::orderBy('id','asc')
            //->paginate(20);
    
            //return view("show_contact_group",["groups"=>$group]);

        return redirect()->route('contact.asc');
        }
    }

    public function request(Request $request,$Request_id){

        $group= ContactGroup::orderBy('id','asc')->get();
 
        $contact=DB::table('contact')
            ->where('id','=',$Request_id)
            ->first();

        $gid=$contact->gid;

        return view("edit_contact",["groups"=>$group,"contacts"=>$contact,'gid'=>$gid]);

    }

    public function destroy(Request $request,$Delete_id)
    {   
        $deleted=DB::table('contact')->where('id','=',$Delete_id)->delete();
        //return response(null,Response::HTTP_NO_CONTENT);
        return redirect()->route('contact.asc');
    }

    public function update(Request $request)
    {
            try{
            $rules=[
            "group_name" => "required",
            "group_user_name" => "required",
            "group_user_phone" => "required",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "group_name.required" => '"群組"為必選資料',
                "group_user_name.required" => '"名稱"為必填資料',
                "group_user_phone.required" => '"電話為"必填資料'

            ];
            $validResult = $request->validate($rules, $message);

        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
        return $errorMessage;
        }
        $old = $request->input('old_id');
        $gid = $request->input('group_name');
        $name = $request->input('group_user_name');
        $phone = $request->input('group_user_phone');

        $data=[
            'gid'=>$gid,
            'contactName'=>$name,
            'contactPhone'=>$phone,
        ];

        Contact::where('id','=',$old)->update($data);

        return redirect()->route('contact.asc');
    }
}
