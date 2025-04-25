<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class AnnouncementsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        if (Gate::allows('group_admin')) {
            //dd($this->authorize('group_admin'));
            //$announcement = Announcements::all();
            return view("announcement");
        }
        else
        {   return abort(403, '抱歉，你没有使用此功能權限！');}
       
    }

    public function show_result_desc()
    {
        $topAnn = Announcements::where('top',1)->first();
        $announcement= Announcements::where('top',0)->orderBy('id','desc')->paginate(20);
        return view("show_announcement",["announcements"=>$announcement,"topAnn"=>$topAnn]);
    }

    public function store(Request $request){
        try{
            $rules=[
            "title" => "required",
            "text_area" => "required",

            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "title.required" => '"標頭"不可為空',
                "text_area.required" => '"內文"不可為空',
            ];
            $validResult = $request->validate($rules, $message);
        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
            return $errorMessage;
        }
        
        $title = $request->input('title');
        $text = $request->input('text_area');
        $top = $request->input('top');
        //dd($top);
        //若有選擇頂置，先將top歸零
        if($top === '1' )
        {
            //dd(filter_var($top, FILTER_VALIDATE_BOOLEAN));
            Announcements::where('top','=', 1)->update(['top' => 0]);
        }
        //dd($title,$text,$top);

            $data=[
            'title'=>$title,
            'announcement'=>$text,
            'top'=>(bool)$top,
            ];
            //dd($data);
            $ann= Announcements::create($data);
            //$ann= Announcements::orderBy('id','asc')->paginate(20);

        return redirect()->route('announcement_desc');        
    }

    public function destroy(Request $request,$id){
        
        $des=Announcements::where('id','=',$id)->delete();

        return redirect()->route('announcement_desc');        
    }

    public function top(Request $request,$id){
        
        Announcements::where('top','=', 1)->update(['top' => 0]);
        Announcements::where('id',$id)->update(['top'=> 1]);

        return redirect()->route('announcement_desc');        
    }

    public function request(Request $request,$id){

        $ann=DB::table('announcements')->where('id','=',$id)->get();

        //dd($Request_id,$employee,$customer,$organize);
        return view("edit_announcement",["announcements"=>$ann]);

    }

    public function update(Request $request){

        try{
            $rules=[
            "title" => "required",
            "text_area" => "required",

            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "title.required" => '"標頭"不可為空',
                "text_area.required" => '"內文"不可為空',
            ];
            $validResult = $request->validate($rules, $message);
        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
            return $errorMessage;
        }

        $aid=$request->input('aid');
        $data=[
            'title'=>$request->input('title'),
            'announcement'=>$request->input('text_area')
        ];


        $ann=DB::table('announcements')->where('id','=',$aid)->update($data);

        return redirect()->route('announcement_desc'); 
    }

}
