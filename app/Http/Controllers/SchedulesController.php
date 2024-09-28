<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use App\Imports\SchedulesImport;
use App\Imports\Schedules2Import;
use App\Exports\SchedulesExport;
use App\Exports\Schedule2Export;
use App\Models\Schedules;
use App\Models\Customer;
use Faker\Provider\fr_BE\Color;
use Maatwebsite\Excel\Facades\Excel;
use Nette\Utils\Strings;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color as XlsColor;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Style\Border;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border as StyleBorder;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use Illuminate\Support\Facades\DB;


class SchedulesController extends Controller
{

   public function __construct()
   {
       $this->middleware('auth');
   }
   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //return view('home');
        $customer = Customer::all();

        return view("Schedule",["customers"=>$customer,]);
    }

   function isDate($date){
      return date('Y-m-d', strtotime($date)) == $date;
   }

   function isTime($time){
      return date('G:i', strtotime($time)) == $time;
   }

   // Import data use laravel excel
   public function importdata(Request $request){
      Excel::import(new SchedulesImport, $request->file('select_file')->store('temp'));
      //return back()->with('success', 'Import successfully!');

      //$array=Excel::toArray(new SchedulesImport, $request->file('select_file')->store('temp'));
      //dd($array);
      //return back()->with('success', 'Import successfully!');

      //$collection = Excel::toCollection(new SchedulesImport, $request->file('file')->store('temp'));

      //dump($array);
      //dump($collection);
   }

   // Validate and Import data use laravel excel
   public function validateAndImportdata(Request $request){

      //Excel::import(new Schedules2Import, "import.xls");
      //dd($request->file('files1')->store('temp'));
      //Excel::import(new Schedules2Import,$request->file('file')->store('/','temp'));
      //$file = $request->file('testfile');
      //dd($file);
      //return back()->with('success', 'Import successfully!');
   }


     // CSV Export
   public function exportCSV(){
      $file_name = 'Schedules_'.date('Y_m_d_H_i_s').'.csv';
      return Excel::download(new SchedulesExport, $file_name);
   }

   // Excel Export
   public function exportExcel(){
      $file_name = 'Schedules_'.date('Y_m_d_H_i_s').'.xlsx';
      return Excel::download(new SchedulesExport, $file_name);
   }

   // Conditional Export (csv)
   public function exportByAgeCSV(Request $request){

      $age = $request->age;

      $file_name = 'schedules_'.date('Y_m_d_H_i_s').'.csv';
      return Excel::download(new Schedule2Export($age), $file_name);
   }

   // data import use phpspreadsheet
   public function import(Request $request)
   {
      // 要求上傳的文件必須是表格格式
      $this->validate($request, ['select_file'  => 'required|mimes:xls,xlsx']);

      // 如果是 POST 方法才讀文件
      if ($request->isMethod('POST')){
         $file = $request->file('select_file');

         // 判斷文件是否上傳成功
         if ($file->isValid()){

            // 原文件名
            $originalName = $file->getClientOriginalName();

            
            // 臨时絕對路徑
            $realPath = $file->getRealPath();
            //dd($realPath);
            // 修改文件名
            $filename = date('Y-m-d-h-i-s').'-'.$originalName;
            
            // 儲存到硬碟相應的路徑
            //$bool = Storage::disk('uploads')->put($filename,file_get_contents($realPath));
            $bool= true;

            //判斷是否上傳成功
               if($bool){
                  
                  $path = public_path('uploads/'.date('Ymd')).'/'.$filename;
                  //$reader = new Xlsx();
                  // $reader = new Xls();
                  //副檔名，選擇編碼器
                  $extension = $file->extension();
                  if('csv' == $extension) 
                  {$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();} 
                  else if('xls' == $extension) 
                  {$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();} 
                  else     
                  {$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();}
                  //$reader->setReadDataOnly(TRUE);
                  
                  $spreadsheet = $reader->load($file); 
                  
                  //$spreadsheet = $reader->load($path); 
                  // 至此導入表格成功，我们可以運用 PhpSpreadSheet 所提供的方法来獲取表格的數據，存入資料庫
                  // 一个例子：
                  $worksheet = $spreadsheet->getActiveSheet()->toArray();  // 獲取當前的工作表數據
                  //dd($worksheet);
                  //dd(count($worksheet).'_____'.count($worksheet[0]));

                  if (count($worksheet)!=27 || count($worksheet[0])!=34)
                  {
                     return back()->with('danger', '資料格式有錯誤，沒有上傳表格');
                  }

                  //檢查上傳時間，限制在隔月10前才可上傳班表
                  $today=date("Y-m-d");//TODAY
                  if(isset($worksheet[1][10]) && isset($worksheet[1][13]))
                  $excel_time=$worksheet[1][10].'-'.$worksheet[1][13];

                  $format_excel_time=date('Y-m-d',strtotime($worksheet[1][10].'-'.($worksheet[1][13])));
                  $upload_limit_time=date("Y-m-d",strtotime("+1 month",strtotime($excel_time.'-10')));//讀取excel時間，並計算隔月十日的真正日期
                  

                  //檢查時間格式是否有問題
                  if($this->isDate($format_excel_time)){
                  
                     if ($excel_time=='1970-01-01'){
                     return back()->with('danger', 'excel中的日期可能有問題，請檢查。');}

                     //若本日超過上傳時限，回傳error
                     if (strtotime($today)>strtotime($upload_limit_time)){
                     return back()->with('danger', '今天是：'.$today.'，你上傳的是：'.$excel_time.'的報表，已超過：'.$upload_limit_time.'的上傳日期限制。');
                     }
                  }
                 
                  //檢查本月天數，是否有不適當排班，如2024/2有29天，卻排了30天的班
                  $howmuch_day=date("t",strtotime($excel_time));
                  if ($howmuch_day<31){
                  for ($i=4;$i<=13;$i++){
                     for ($j=$howmuch_day+2;$j<=32;$j++){
                        if ($worksheet[$i][$j]!=null){
                           return back()->with('danger','你排的班為'.$excel_time.'，本月有'.$howmuch_day.'天，請檢查是不是排了'.($howmuch_day+1).'日之後的班');
                        }
                     }
                  }
                  }

                  //檢查沒輸入員工，但卻有排班的情況
                  for ($i=4;$i<=13;$i++){
                     if($worksheet[$i][1]==null){
                        for ($j=2;$j<=32;$j++){
                           if ($worksheet[$i][$j]!=null)
                           {return back()->with('danger','發現沒有輸入員工，但有排班資訊的列，請把該列資料清空再行排班，例如excel中B14欄，沒有資料，也請把C14至AG14的排班資訊清空');}
                        }
                     }
                  }

                  //檢查欄位個數，數量不對表示格式被user改過, 不上傳表格，對了才會往下檢查excel的人員資料


                  //check excel是否有誤植重覆的員工名字，若有即回傳名字的list
                  //else{
                     $offset=1;
                     for ($i=4;$i<=13;$i++){
                        
                        $check_same_nameuser=$worksheet[$i][1];

                        for($j=$i+1;$j<=13;$j++){
                           if($check_same_nameuser==$worksheet[$j][1] && $worksheet[$j][1]!=null){
                              $same_name_list[$offset]=$worksheet[$j][1];
                              $offset=$offset+1;
                           }
                        }
                     }
                     if(isset($same_name_list))
                     {return back()->with('danger', '檢查到有資料表上有重覆的人員名字：'.implode(' ',$same_name_list));}
                  //}

                  //check有無此客戶，若無，回傳錯誤，若有
                  $result=DB::table('customers')->where('firstname','=',$worksheet[4][0])->count();

                  if($result==0)
                  { return back()->with('danger', '無此客戶：'.$worksheet[4][0]);}

                  //check DB中，有沒有公司內部同名同姓的人,及公司有無此員工

                  $same_name_counter=0;//同名人數
                  $same_name_list=[];//儲存同名的陣列
                  $same_name_offset=1;//陣列offset
                  $error_counter=0;//查無此人筆數
                  $error_list=[];//儲存查無此人之陣列
                  $error_offset=1;//以上陣列之offset
                  
                  for ($i=4;$i<=13;$i++){     
                     if($worksheet[$i][1]!=null) {
                     $check_user=$worksheet[$i][1];
                     $result=DB::table('employees')->where('member_name','=',$check_user)->count();
                     
                     //檢查有無這個員工，若無，加到array
                     if($result==0){
                        $error_list[$error_offset]=$check_user;
                        $error_offset=$error_offset+1;
                        $error_counter=$error_counter+1;

                     }
                     //若是查到兩個以上，代表有同名同姓，均加入array
                     elseif($result>1)
                     {
                        $same_name_list[$same_name_offset]=$worksheet[$i][1];
                        $same_name_counter=$same_name_counter+1;//注意，條件是>1，即查到有同名同姓的意思，不是一個人同名同姓
                        $same_name_offset=$same_name_offset+1;
                     }
                     }
                  }

                  if ($error_counter!=0){
                     return back()->with('danger', '公司無此人員：'.implode(' ',$error_list).'，請確認EXCEL有沒有打錯字，並重新上傳');
                  }
                  if ($same_name_counter>0){
                     return back()->with('danger','公司有同名同姓員工：'.implode(' ',$same_name_list).'，請至人事系統查詢同名員工，並給定不同的名字，如：陳小明(區分條件)');
                  }
                  //dd(strtotime($worksheet[20][33]));
                  //dd(date('G:i', strtotime($worksheet[16][24])));
                  //dd($this->isTime($worksheet[20][33]));
                  //dd($worksheet[16][24]);

                  //檢查各班時間格式
                  $stack=[];
                  for ($i=16;$i<=20;$i++){

                     array_push($stack, $worksheet[$i][24], $worksheet[$i][27]);
                  }

                  for ($i=16;$i<=20;$i++){
                     array_push($stack, $worksheet[$i][30], $worksheet[$i][33]);
                  }
               //$stack=str_replace("Disney World","Wibibi","Welcome to Disney World.");
                 //dd($worksheet[17][27]);
                  $key= array_search(null,$stack);
                  $count=count($stack);
                  //dd($stack);
                  if ($key!=false){
                     for ($i=$key+1;$i<=$count-1;$i++){
                        if ($stack[$i]!=null){
                           return back()->with('danger','發現excel中的各班時間，空格後還有定義時間，是否漏輸入資料？並將沒有要定義的欄位，保持空值即可，請查明後再上傳檢查。');
                        }
                     }
                  }

                  /*兩班制中，大夜班下班時間會很早，故註解
                  for ($i=0;$i<=$count-2;$i+=2){
                     if ($stack[$i]!=null && $stack[$i+1]!=null && strtotime($stack[$i])>strtotime($stack[$i+1])){
                        return back()->with('danger','定義各班別的時間欄位，開始時間一定要小於各班的結束時間。');
                     }
                  }*/

                  for ($i=1;$i<=$count-2;$i+=2){
                     if ($stack[$i]!=null && $stack[$i+1]!=null && strtotime($stack[$i])!=strtotime($stack[$i+1])){
                        return back()->with('danger','除非是最後一班，定義各班別的時間欄位，各班的結束時間，必定等於下一班的開始時間');
                     }
                  }
                  
                  //檢查定義好的班別，並加入array中
                  $shift=array('A','B','C','D','E','F','G','H','I','J');
                  $unlock=[null];
                  $offset=0;
                  for ($i=0;$i<=$count-2;$i+=2){
                     if ($stack[$i]!=null && $stack[$i+1]!=null){
                        array_push($unlock,$shift[$offset]);
                        $offset=$offset+1;
                     }
                  }

                  $no_defind_list=[];
                  for ($i=4;$i<=13;$i++){
                     for ($j=2;$j<=32;$j++){
                        if($worksheet[$i][$j]!=null){
                           //單日多重排班處理
                           //$shift_work=strtoupper($worksheet[$i][$j]);//使用者可能打成小寫，如aAC、ACc這種錯誤，全轉成大寫
                           $shift_work=chunk_split($worksheet[$i][$j],1,',');//把各班用逗號切割

                           $shift_work=explode(',',$shift_work);  //將上面字串以逗號分割成陣列
                                                      
                           //$shift_work=array_unique($shift_work);//將陣列中重覆排班去掉，如：AAB、ABB這種錯誤

                           $shift_work=array_filter($shift_work);//過濾字空字串
                          
                           //$len=strlen($worksheet[$i][$j]);
                           
                           if (count($shift_work) != count(array_unique($shift_work))) {
                              return back()->with('danger','發現有重覆排班，如排成AA、BB班的狀況，請檢查排班表');
                           }

                           foreach ($shift_work as $value){
                              $key=array_search($value,$unlock);

                              if ($key===false){
                              //array_push($no_defind_list,$worksheet[$i][$j]);
                              array_push($no_defind_list,$value);
                              }
                           }
                        }
                     }
                  }

                  $result=array_unique($no_defind_list);
                  //dd($result);
                  if($result!=[])
                  {return back()->with('danger', '檢查到沒定義的班別：'.implode(' ',$result).'班，請訂正排班錯誤或定義各班時間，並請注意大小寫');}
                  
                  //檢查重覆排班
                  $year=$worksheet[1][10];
                  $month=$worksheet[1][13];
                  $customer=$worksheet[4][0];
                  $stack=array_diff($stack,array(null));
                  $stack=array_chunk($stack, 2);


                  //查每月最後一天是幾號
                  $MonthBeginDate=date("Y-$month-01", strtotime(date("Y-m-d")));
                  $MonthLastDate=substr(date('Y-m-d', strtotime("$MonthBeginDate +1 month -1 day")),-2,2);
                  //dd($MonthBeginDate,$MonthLastDate);
                  //查詢DB各人員的每月班表進array，進行是否重複排班的判斷
                  for ($i=4;$i<=13;$i++){
                     
                     if($worksheet[$i][1]!=null){
                        $name=$worksheet[$i][1];

                        $res = schedules::where([  
                                    ['employee_id','=',$name],      
                                    ['year', '=', "$year"],
                                    ['month', '=', "$month"],
                                    ])->get()->toArray();
                        
                        //取得array元素量
                        $array_count=count($res);

                        //清掉array中的""，無用資料
                        for ($k=0;$k<$array_count;$k++){
                           $res[$k] = array_diff($res[$k], [""]);
                        }

                        //查詢31天，即excel第2到32行
                        for ($j=2;$j<=32;$j++){
                           if($worksheet[$i][$j]!=null){
                              $str_count=strlen($worksheet[$i][$j]);//查詢每欄排了幾個班

                                 //讀取第n個班
                                 for($x=0;$x<$str_count;$x++){
                                    $request_schedule=substr($worksheet[$i][$j],$x,1);//取得排班表所排的班別代號

                                    //取得上班下班時間
                                    if($request_schedule=='A'){
                                       $start_time=$stack[0][0];
                                       $end_time=$stack[0][1];
                                    }

                                    if($request_schedule=='B'){
                                       $start_time=$stack[1][0];
                                       $end_time=$stack[1][1];
                                    }

                                    if($request_schedule=='C'){
                                       $start_time=$stack[2][0];
                                       $end_time=$stack[2][1];
                                    }
                                    if($request_schedule=='D'){
                                       $start_time=$stack[3][0];
                                       $end_time=$stack[3][1];
                                    }
                                    if($request_schedule=='E'){
                                       $start_time=$stack[4][0];
                                       $end_time=$stack[4][1];
                                    }
                                    if($request_schedule=='F'){
                                       $start_time=$stack[5][0];
                                       $end_time=$stack[5][1];
                                    }

                                    if($request_schedule=='G'){
                                       $start_time=$stack[6][0];
                                       $end_time=$stack[6][1];
                                    }

                                    if($request_schedule=='H'){
                                       $start_time=$stack[7][0];
                                       $end_time=$stack[7][1];
                                    }
                                    if($request_schedule=='I'){
                                       $start_time=$stack[8][0];
                                       $end_time=$stack[8][1];
                                    }
                                    if($request_schedule=='J'){
                                       $start_time=$stack[9][0];
                                       $end_time=$stack[9][1];
                                    }
/*
if($i==5){
   dd($i,$j,$x,$request_schedule,$start_time,$end_time,$worksheet);
}
*/

                                    //$j=2, 也就是每月第一天排班，查詢上月最後一天有無排班衝突
                                    if($j==2){
                                       //抓取excel中的年月
                                       $year=$worksheet[1][10];
                                       $month=$worksheet[1][13];

                                       //計算上個月是幾年幾月,假設本月是1月，上月即12月
                                       $beforeyear=$year;
                                       $beforemonth=$month-1;
                                          if($beforemonth<1){
                                             $beforemonth=12;
                                             $beforeyear=$year-1;
                                          }

                                       //計算最後一天是幾日   
                                       $BeforeMonthBeginDate=date("Y-$beforemonth-01");
                                       $BeforeMonthLastDate=substr(date('Y-m-d', strtotime("$MonthBeginDate +1 month -1 day")),-2,2);   
                                       //dd($BeforeMonthBeginDate,$BeforeMonthLastDate);
                           

                                       //取得上月班表
                                          $res2 = schedules::where([  
                                                ['employee_id','=',$name],      
                                                ['year', '=', "$beforeyear"],
                                                ['month', '=', $beforemonth],
                                                ])->get()->toArray();
                       
                                       //取得array元素量
                                       $array_count=count($res2);
                                             //dd($MonthBeginDate,$MonthLastDate,$res2);
                                       //清掉array中的""，無用資料
                                          for ($k=0;$k<$array_count;$k++){
                                             $res2[$k] = array_diff($res2[$k], [""]);
                                          }

                                       //取得array元素量
                                          $array_count=count($res2);
                     
                                       //清掉array中的""，無用資料
                                       for ($k=0;$k<$array_count;$k++){
                                          $res2[$k] = array_diff($res2[$k], [""]);
                                       }

                                       for($check=0;$check<$array_count;$check++){

                                          //if($res2[$check]['customer_id']!=$customer){
                                          //dd($res2);
                                             if(isset($res2[$check]['day'.$BeforeMonthLastDate])){//檢查db中有無上月最後一日排班
                                                $dayX_work=$res2[$check]['day'.$BeforeMonthLastDate];
                                                $dayX_work_count=strlen($dayX_work);

                                                for($z=0;$z<$dayX_work_count;$z++){
                                                   $dayX_work_name=substr($dayX_work,$z,1);//取得dayX中的第z個班代號
                                                   $dayX_work_start_time=$res2[$check][$dayX_work_name];
                                                   $dayX_work_end_time=$res2[$check][$dayX_work_name.'_end'];
                                                   /*
                                                      dd($res2[$check]['customer_id'],
                                                      'class:'.$dayX_work_name,
                                                      'DBstart_time:'.$dayX_work_start_time,
                                                      'DBend_time:'.$dayX_work_end_time,
                                                      'excel start:'.$start_time,
                                                      'excel end:'.$end_time);   
                                                   */

                                                   //計算excel中的時間，和db中的時間有無交集，若有，重複排班
                                                   //查詢上月班表有無隔日上班狀況
                                                   if($dayX_work_end_time<$dayX_work_start_time){
                                                      if(strtotime($start_time) < strtotime($dayX_work_end_time)){

                                                         return back()->with('danger',$name.'於'.$year.'年'.($month-1).'月'.$MonthLastDate.'日於'.$res2[$check]['customer_id'].'有排'.$dayX_work_name.
                                                                  '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                                  '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                      }
                                                      else{
                                                         //pass this check
                                                      }
                                                   }    
                                                }//end $z loop
                                             }//end if
                                          //}//end if
                                       }//end check loop
                                    }

                                    //查詢DB TO array的當月報表中，查第n天的班，同客戶的班表就不檢查，同客戶之後再檢查最後一天的排班跟隔月是否衝突
                                    //取得array元素量
                                    $array_count=count($res);

                                    for($y=0;$y<$array_count;$y++){
                                       
                                       if($res[$y]['customer_id']!=$customer){
                                          
                                          if(isset($res[$y]['day'.$j-1])){//檢查db中有無當日排班
                                  
                                             $dayX_work=$res[$y]['day'.$j-1];
                                             $dayX_work_count=strlen($dayX_work);

                                             for($z=0;$z<$dayX_work_count;$z++){
                                                $dayX_work_name=substr($dayX_work,$z,1);//取得dayX中的第z個班代號
                                                $dayX_work_start_time=$res[$y][$dayX_work_name];
                                                $dayX_work_end_time=$res[$y][$dayX_work_name.'_end'];
                                                //dd($res[$y]['customer_id'].'__'.($j-1).'日，'.$dayX_work_start_time.'__'.$dayX_work_end_time,$dayX_work_name);   
                                                //dd($dayX_work,$j,$start_time,$end_time,$dayX_work_start_time,$dayX_work_end_time);

                                                //計算excel中的時間，和db中的時間有無交集，若有，重複排班
                                                if(strtotime($start_time)>=strtotime($dayX_work_start_time)){
                                                   if(strtotime($start_time)>=strtotime($dayX_work_end_time)){

                                                      //考慮資料庫中的班表有無隔日的問題，若有，即重覆排班
                                                      if(strtotime($dayX_work_end_time)<strtotime($dayX_work_start_time)){
                                                         return back()->with('danger',$name.'於'.$year.'年'.$month.'月'.($j-1).'日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                         '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                         '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                      }
                                                      else{
                                                         //pass this check
                                                      }

                                                   }
                                                   else{
                                                      return back()->with('danger',$name.'於'.$year.'年'.$month.'月'.($j-1).'日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                      '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                      '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                   }
                                                }
                                                else{
                                                   if(strtotime($end_time)>strtotime($dayX_work_start_time)){
                                                      return back()->with('danger',$name.'於'.$year.'年'.$month.'月'.($j-1).'日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                      '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                      '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                   }
                                                   else{
                                                      //pass this check
                                                   }
                                                }   
                                             }//end $z loop
                                          }//end if


                                          //每月最後一天且excel中有隔月一日上班，若是，查詢資料庫隔月一日資料，若不是，直查隔天上班資料
                                          if($j-1 == $MonthLastDate && strtotime($end_time)<=strtotime($start_time)){
                                             
                                             //抓取excel中的年月
                                             $year=$worksheet[1][10];
                                             $month=$worksheet[1][13];

                                             //計算隔月是幾年幾月,最後一個月即12月，加1即13，將資料改成"隔一年的1月:
                                             $nextyear=$year;
                                             $nextmonth=$month+1;
                                             if($nextmonth>12){
                                                $nextmonth=1;
                                                $nextyear=$year+1;
                                             }

                                             //取得隔月班表
                                             $res2 = schedules::where([  
                                                ['employee_id','=',$name],      
                                                ['year', '=', "$nextyear"],
                                                ['month', '=', $nextmonth],
                                                ])->get()->toArray();
                       
                                             //取得array元素量
                                             $array_count=count($res2);
                                           
                                             //清掉array中的""，無用資料
                                             for ($k=0;$k<$array_count;$k++){
                                                $res2[$k] = array_diff($res2[$k], [""]);
                                             }

                                             for($check=0;$check<$array_count;$check++){
                                                if($res2[$check]['customer_id']!=$customer){
                                                   if(isset($res2[$check]['day1'])){//檢查db中有無當日排班

                                                      $dayX_work=$res2[$check]['day1'];
                                                      $dayX_work_count=strlen($dayX_work);

                                                      for($z=0;$z<$dayX_work_count;$z++){
                                                         $dayX_work_name=substr($dayX_work,$z,1);//取得dayX中的第z個班代號
                                                         $dayX_work_start_time=$res2[$check][$dayX_work_name];
                                                         $dayX_work_end_time=$res2[$check][$dayX_work_name.'_end'];
                                                         /*
                                                         dd($res2[$check]['customer_id'].'__'.'1日，',
                                                            'class:'.$dayX_work_name,
                                                            'DBstart_time:'.$dayX_work_start_time,
                                                            'DBend_time:'.$dayX_work_end_time,
                                                            'excel start:'.$start_time,
                                                            'excel end:'.$end_time);   
                                                         */

                                                         //計算excel中的時間，和db中的時間有無交集，若有，重複排班
                                                         if(strtotime($end_time)>strtotime($dayX_work_start_time)){
                                                            return back()->with('danger',$name.'於'.$year.'年'.($month).'月1日於'.$res[$check]['customer_id'].'有排'.$dayX_work_name.
                                                                  '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                                  '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                            }
                                                         else{
                                                            //pass this check
                                                         }    
                                                      }//end $z loop
                                                   }//end if
                                                }//end if
                                             }//end check loop
                                          }//end if

                                          //非每月最後一天，且有隔日上班的狀況
                                          else{
                                             if(isset($res[$y]['day'.$j]) && strtotime($end_time)<=strtotime($start_time)){
                                                
                                                $dayX_work_start_time=$res[$y][$res[$y]['day'.$j]];
                                                $dayX_work_end_time=$res[$y][$res[$y]['day'.$j].'_end'];
                                                $dayX_work_name=$res[$y]['day'.$j];//取得dayX中的第z個班代號
                                                //dd($start_time.'_'.$end_time.'__'.$res[$y]['day'.$j].'_'.$dayX_work_start_time.'_'.$dayX_work_end_time);
                                                //dd($res[$y]['day'.$j]);

                                               //計算excel中的時間，和db中的時間有無交集，若有，重複排班
                                                if(strtotime($start_time)>strtotime($dayX_work_start_time)){
                                                   if(strtotime($start_time)>=strtotime($dayX_work_end_time)){

                                                      //考慮資料庫中的班表有無隔日的問題，若有，即重覆排班
                                                      if(strtotime($dayX_work_start_time)<strtotime($end_time)){
                                                         return back()->with('danger',$name.'於'.$year.'年'.$month.'月'.($j).'日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                         '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time."，排班表".($j-1)."日中有排：".$request_schedule.'班'.
                                                         '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                      }
                                                      else{
                                                         //pass this check
                                                      }

                                                   }
                                                   else{
                                                      return back()->with('danger',$name.'於'.$year.'年'.$month.'月'.($j-1).'日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                      '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                      '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                   }
                                                }
                                                else{
                                                   if(strtotime($end_time)>strtotime($dayX_work_start_time)){
                                                      return back()->with('danger',$name.'於'.$year.'年'.$month.'月'.($j-1).'日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                      '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                      '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                   }
                                                   else{
                                                      //pass this check
                                                   }
                                                } 
                                                
                                             }
                                          }
                                       
                                       }//end if


                                       else{
                                          //同客戶只要檢查最後一天跟隔月一日班表是否衝突
                                          if ($j==31){
                                             $nextyear=$year;
                                             $nextmonth=$month+1;
                                             if($nextmonth>12){
                                                $nextmonth=1;
                                                $nextyear=$year+1;
                                             }

                                             //取得隔月班表
                                             $res3 = schedules::where([  
                                                ['employee_id','=',$name],      
                                                ['year', '=', "$nextyear"],
                                                ['month', '=', $nextmonth],
                                                ])->get()->toArray();
                       
                                             //取得array元素量
                                             $array_count=count($res3);
                                             //dd($year,$month,$MonthLastDate,$res,$res3,$array_count,$j);
                                             //dd(isset($res[$y]['day'.$MonthLastDate]),isset($res3[$y]['day1']));
                                      
                                             if(isset($res[$y]['day'.$MonthLastDate]) && isset($res3[$y]['day1'])){
                                                //隔月第一天上班時間
                                                $dayX_work_name=$res3[$y]['day1'];
                                                $dayX_work_start_time=$res3[$y][$dayX_work_name];
                                                $dayX_work_end_time=$res3[$y][$dayX_work_name.'_end'];

                                                /*
                                                dd($res,'excel last date is:'.$res[$y]['day'.$MonthLastDate],$start_time,$end_time,
                                                   $res3,'DB next month first date is:'.$res3[$y]['day1'],$dayX_work_start_time,$dayX_work_end_time);
                                                */
 //if($j==31){dd($start_time,$end_time);}
  //dd($i,$j,$y,$start_time,$end_time);  
                                                //計算excel中的時間，和db中的時間有無交集，若有，重複排班
                                                if(strtotime($end_time)>strtotime($dayX_work_start_time)){
                                                   //dd($i,$j,$y,$request_schedule,$start_time,$end_time);                               
                                                   return back()->with('danger',$name.'於'.$nextyear.'年'.($nextmonth).'月1日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                         '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                         '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                }
                                                else{
                                                   //pass this check                                                      }  
                                                }

                                                //計算excel中的時間，和db中的時間有無交集，若有，重複排班
                                                if(strtotime($start_time)>strtotime($dayX_work_start_time)){
                                                   if(strtotime($start_time)>=strtotime($dayX_work_end_time)){

                                                      //考慮資料庫中的班表有無隔日的問題，若有，即重覆排班
                                                      if(strtotime($dayX_work_start_time)<strtotime($end_time)){
                                                         
                                                         return back()->with('danger',$name.'於'.$nextyear.'年'.$nextmonth.'月1日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                         '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time."，排班表中有排：".$request_schedule.'班'.
                                                         '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                      }
                                                      else{
                                                         //pass this check
                                                      }

                                                   }
                                                   else{
                                                      return back()->with('danger',$name.'於'.$nextyear.'年'.$nextmonth.'月1日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                      '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                      '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                   }
                                                }
                                                else{
                                                   if(strtotime($end_time)>strtotime($dayX_work_start_time)){
                                                      return back()->with('danger',$name.'於'.$nextyear.'年'.$nextmonth.'月1日於'.$res[$y]['customer_id'].'有排'.$dayX_work_name.
                                                      '班，上班時間是：'.$dayX_work_start_time.'，下班時間是：'.$dayX_work_end_time.'，排班表中有排：'.$request_schedule.'班'.
                                                      '，上班時間是：'.$start_time.'，下班時間是：'.$end_time);
                                                   }
                                                   else{
                                                      //pass this check
                                                   }
                                                } 
                                                
                                             }





                                          }
                                       }
                                    }//end $y looop


                                 }//end $x loop

                                 
                           }//end if 

                        }//end $j loop

                     }//end if
                  }//end $i loop


                  //開始進行資料處理及寫入
                  $array=Excel::toArray(new SchedulesImport, $request->file('select_file')->store('temp'));


                  $timezone=\PhpOffice\PhpSpreadsheet\Shared\Date::getDefaultOrLocalTimezone();
                  //$array_type=gettype($array[0][17][24]);
                  //dd($array_type);
                  //dd(is_int($array[0][1][30]));
                  if(is_int($array[0][1][30]))
                  {
                     $time_type=date('Y/m/d',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($array[0][1][30],$timezone));

                     $array[0][1][30]=$time_type;
                  }
//dd($array[0][16][33]===0);
                  for ($i=16;$i<=20;$i++)
                  {
                     for($j=24;$j<=33;$j+=3)
                     {
                           //$array_type=gettype($array[0][$i][$j]);   

                           if(is_double($array[0][$i][$j]))
                           {
                              $time_type=date('H:i',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($array[0][$i][$j],$timezone));
                              //dd($time_type);
                              $array[0][$i][$j]=$time_type;
                              //print_r("[$i][$j]".$array[0][$i][$j]."<br>");
                           }
//dd($array);
                           if($array[0][$i][$j]===0)
                           {
                              $time_type=date('H:i',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($array[0][$i][$j],$timezone));
                              //dd($time_type);
                              $array[0][$i][$j]=$time_type;
                              //print_r("[$i][$j]".$array[0][$i][$j]."<br>");
                              
                           }
//dd($array);
                           if($array[0][$i][$j]==null)
                           {
                              //$time_type=date('H:i',\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($array[0][$i][$j],$timezone));
                              //dd($time_type);
                              //$array[0][$i][$j]=$time_type;
                              //print_r("[$i][$j]".$array[0][$i][$j]."<br>");
                              $array[0][$i][$j]="";
                           }
                           else{
                              //return back()->with('danger', ' type is not string or double: ');
                           }
                           
                     }//end $j loop
                  }//end $i loop
//dd($array);
                  $schedule= new Schedules();
                  //客戶名字轉換成id
                  $cusId=DB::table('customers')->select('customer_id')->where('firstname','=',$worksheet[4][0])->first();

                  //dd($cusId->customer_id);
                  //刪除舊有資料
                  $delete=$schedule::where([
                     ['customer_id', '=', $cusId->customer_id],
                     ['year', '=',  $array[0][1][10]],
                     ['month', '=', $array[0][1][13]],
                     ])->delete();

                  //判斷是否已上傳班表過
                  $count = $schedule::where([
                     ['customer_id', '=', $cusId->customer_id],
                     ['year', '=',  $array[0][1][10]],
                     ['month', '=', $array[0][1][13]],
                     ])->count();
 
                  if ($count==0){

                  for ($x=4;$x<=13;$x++)
                  {
                     if ($array[0][$x][1]!=null){
                     $empId=DB::table('employees')->select('member_sn')->where('member_name','=',$array[0][$x][1])->first();
                     $company_id=$array[0][0][1];
                     $customer_id=$cusId->customer_id;
                     $employee_id=$empId->member_sn;
                     $year=$array[0][1][10];
                     $month=$array[0][1][13];
                     
                     for ($y=2;$y<=32;$y++)
                     {
                        if($array[0][$x][$y]!=null)
                           {${'day'.$y-1} = $array[0][$x][$y];}
                        else
                           {${'day'.$y-1}="";}
                           
                           //print(('day'.$y-1)."_is_".${'day'.$y-1}."<br>");
                     }

                      $schedule::create([
                         'customer_id'=>$customer_id,
                         'employee_id'=>$employee_id,
                         'year' =>$year,
                         'month' =>$month,
                         'day1'=> ${'day1'},
                         'day2'=> ${'day2'},
                         'day3'=> ${'day3'},
                         'day4'=> ${'day4'},
                         'day5'=> ${'day5'},
                         'day6'=> ${'day6'},
                         'day7'=> ${'day7'},
                         'day8'=> ${'day8'},
                         'day9'=> ${'day9'},
                         'day10'=> ${'day10'},
                         'day11'=> ${'day11'},
                         'day12'=> ${'day12'},
                         'day13'=> ${'day13'},
                         'day14'=> ${'day14'},
                         'day15'=> ${'day15'},
                         'day16'=> ${'day16'},
                         'day17'=> ${'day17'},
                         'day18'=> ${'day18'},
                         'day19'=> ${'day19'},
                         'day20'=> ${'day20'},
                         'day21'=> ${'day21'},
                         'day22'=> ${'day22'},
                         'day23'=> ${'day23'},
                         'day24'=> ${'day24'},
                         'day25'=> ${'day25'},
                         'day26'=> ${'day26'},
                         'day27'=> ${'day27'},
                         'day28'=> ${'day28'},
                         'day29'=> ${'day29'},
                         'day30'=> ${'day30'},
                         'day31'=> ${'day31'},
                         'A'=> $array[0][16][24],
                         'A_end'=> $array[0][16][27],
                         'B'=> $array[0][17][24],
                         'B_end'=> $array[0][17][27],
                         'C'=> $array[0][18][24],
                         'C_end'=> $array[0][18][27],
                         'D'=> $array[0][19][24],
                         'D_end'=> $array[0][19][27],
                         'E'=> $array[0][20][24],
                         'E_end'=> $array[0][20][27],
                         'F'=> $array[0][16][30],
                         'F_end'=> $array[0][16][33],
                         'G'=> $array[0][17][30],
                         'G_end'=> $array[0][17][33],
                         'H'=> $array[0][18][30],
                         'H_end'=> $array[0][18][33],
                         'I'=> $array[0][19][30],
                         'I_end'=> $array[0][19][33],
                         'J'=> $array[0][20][30],
                         'J_end'=> $array[0][20][33],
                         ]
                    );
                  }
                  }
                   return back()->with('success', '匯入資料成功，Import successfully!');
               }

               else{
                  return back()->with('danger', '資料己存在，沒有上傳表格');
               }
            }
         }//end second if
      }//end first if
   }
   
   //data export use phpspreadsheet
   public function export(Request $request)
   {

      $spreadsheet = new Spreadsheet();
      $activeWorksheet = $spreadsheet->getActiveSheet();
   
      $spreadsheet->getActiveSheet()->getPageSetup()
      ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
   
      $spreadsheet->getActiveSheet()->getPageSetup()
      ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

      $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.25);
      $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.25);
      $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.25);
      $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.25);

      $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(true);
      $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);

      $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(3);
      $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
      $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(9);
      $spreadsheet->getActiveSheet()->getColumnDimension('AH')->setWidth(6);
      $spreadsheet->getActiveSheet()->getColumnDimension('AG')->setWidth(3.5);

      $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
      $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);

      for ($i=2;$i<28;$i++)
      {
      $spreadsheet->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
      };
    
      $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
      $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setsize(18);

      $spreadsheet -> getActiveSheet()->mergeCells('B1:AD1');
      $spreadsheet -> getActiveSheet()->mergeCells('AE1:AH1');
      $spreadsheet -> getActiveSheet()->mergeCells('A2:A4');
      //$spreadsheet -> getActiveSheet()->mergeCells('B2:AD2');
      //$spreadsheet -> getActiveSheet()->mergeCells('P2:AD2');
      $spreadsheet -> getActiveSheet()->mergeCells('B2:J2');
      $spreadsheet -> getActiveSheet()->mergeCells('K2:L2');
      $spreadsheet -> getActiveSheet()->mergeCells('N2:O2');
      $spreadsheet -> getActiveSheet()->mergeCells('Q2:S2');
      $spreadsheet -> getActiveSheet()->mergeCells('T2:AD2');      
      $spreadsheet -> getActiveSheet()->mergeCells('AE2:AH2');
      $spreadsheet -> getActiveSheet()->mergeCells('A5:A14');
      $spreadsheet -> getActiveSheet()->mergeCells('A15:A27');
      $spreadsheet -> getActiveSheet()->mergeCells('AH3:AH4');
      $spreadsheet -> getActiveSheet()->mergeCells('A15:A27');

      $spreadsheet -> getActiveSheet()->mergeCells('B15:AG15');
      $spreadsheet -> getActiveSheet()->mergeCells('B16:E16');
      $spreadsheet -> getActiveSheet()->mergeCells('B17:E17');
      $spreadsheet -> getActiveSheet()->mergeCells('B18:E18');
      $spreadsheet -> getActiveSheet()->mergeCells('B19:E19');
      $spreadsheet -> getActiveSheet()->mergeCells('B20:E20');
      $spreadsheet -> getActiveSheet()->mergeCells('B21:E21');

      $spreadsheet -> getActiveSheet()->mergeCells('F17:I17');
      $spreadsheet -> getActiveSheet()->mergeCells('F18:I18');
      $spreadsheet -> getActiveSheet()->mergeCells('F19:I19');
      $spreadsheet -> getActiveSheet()->mergeCells('F20:I20');
      $spreadsheet -> getActiveSheet()->mergeCells('F21:I21');

      $spreadsheet -> getActiveSheet()->mergeCells('F16:I16');
      $spreadsheet -> getActiveSheet()->mergeCells('J16:M21');
      $spreadsheet -> getActiveSheet()->mergeCells('U16:W24');
      
      $spreadsheet -> getActiveSheet()->mergeCells('N16:P16');
      $spreadsheet -> getActiveSheet()->mergeCells('Q16:T16');
      $spreadsheet -> getActiveSheet()->mergeCells('N17:P17');
      $spreadsheet -> getActiveSheet()->mergeCells('Q17:T17');
      $spreadsheet -> getActiveSheet()->mergeCells('Q18:T18');
      $spreadsheet -> getActiveSheet()->mergeCells('N18:P18');
      $spreadsheet -> getActiveSheet()->mergeCells('N19:P19');
      $spreadsheet -> getActiveSheet()->mergeCells('Q19:T19');
      $spreadsheet -> getActiveSheet()->mergeCells('N20:P20');
      $spreadsheet -> getActiveSheet()->mergeCells('Q20:T20');
      $spreadsheet -> getActiveSheet()->mergeCells('N21:P21');
      $spreadsheet -> getActiveSheet()->mergeCells('Q21:T21');
      $spreadsheet -> getActiveSheet()->mergeCells('N22:P22');
      $spreadsheet -> getActiveSheet()->mergeCells('Q22:T22');
      $spreadsheet -> getActiveSheet()->mergeCells('N23:P23');
      $spreadsheet -> getActiveSheet()->mergeCells('Q23:T23');
      $spreadsheet -> getActiveSheet()->mergeCells('N24:P24');
      $spreadsheet -> getActiveSheet()->mergeCells('Q24:T24');

      $spreadsheet -> getActiveSheet()->mergeCells('X16:AH16');
      $spreadsheet -> getActiveSheet()->mergeCells('Y17:Z17');
      $spreadsheet -> getActiveSheet()->mergeCells('AB17:AC17');
      $spreadsheet -> getActiveSheet()->mergeCells('AE17:AF17');

      $spreadsheet -> getActiveSheet()->mergeCells('Y18:Z18');
      $spreadsheet -> getActiveSheet()->mergeCells('AB18:AC18');
      $spreadsheet -> getActiveSheet()->mergeCells('AE18:AF18');

      $spreadsheet -> getActiveSheet()->mergeCells('Y19:Z19');
      $spreadsheet -> getActiveSheet()->mergeCells('AB19:AC19');
      $spreadsheet -> getActiveSheet()->mergeCells('AE19:AF19');

      $spreadsheet -> getActiveSheet()->mergeCells('Y20:Z20');
      $spreadsheet -> getActiveSheet()->mergeCells('AB20:AC20');
      $spreadsheet -> getActiveSheet()->mergeCells('AE20:AF20');

      $spreadsheet -> getActiveSheet()->mergeCells('Y21:Z21');
      $spreadsheet -> getActiveSheet()->mergeCells('AB21:AC21');
      $spreadsheet -> getActiveSheet()->mergeCells('AE21:AF21');

      $spreadsheet -> getActiveSheet()->mergeCells('X22:AF22');   

      $spreadsheet -> getActiveSheet()->mergeCells('B22:M22');
      $spreadsheet -> getActiveSheet()->mergeCells('B23:M23');
      $spreadsheet -> getActiveSheet()->mergeCells('B24:M24');
      $spreadsheet -> getActiveSheet()->mergeCells('B25:W25');
      $spreadsheet -> getActiveSheet()->mergeCells('B26:W26');
      $spreadsheet -> getActiveSheet()->mergeCells('B27:W27');

      $spreadsheet -> getActiveSheet()->mergeCells('X23:AC23');
      $spreadsheet -> getActiveSheet()->mergeCells('AD23:AH23');
      $spreadsheet -> getActiveSheet()->mergeCells('X24:AC27');
      $spreadsheet -> getActiveSheet()->mergeCells('AD24:AH27');
      //$spreadsheet -> getActiveSheet()->mergeCells('A28:AH28');

      $activeWorksheet->setCellValue('B1', '萬宇保全');
      $activeWorksheet->setCellValue('A2', '名稱');
      //$activeWorksheet->setCellValue('B2', '2023年4月輪值表');
      //$activeWorksheet->setCellValue('P2', '輪值表');
      $activeWorksheet->setCellValue('K2', '2023');
      $activeWorksheet->setCellValue('M2', '年');
      $activeWorksheet->setCellValue('N2', '10');
      $activeWorksheet->setCellValue('P2', '月');
      $activeWorksheet->setCellValue('Q2', '輪值表');
      $activeWorksheet->setCellValue('B3', '日期');
      $activeWorksheet->setCellValue('B4', '值勤員');
      $activeWorksheet->setCellValue('AH3', '實勤');
      //$activeWorksheet->setCellValue('AE2', '2023年10月10日');
      $today = date('Y-m-d');
      $activeWorksheet->setCellValueExplicit('AE2', "$today",\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      //$activeWorksheet->setCellValue('A5', '台南好市多');
      $activeWorksheet->setCellValue('A15', '交代事項');   
      //$activeWorksheet->setCellValue('B5', '陳小明1');
      //$activeWorksheet->setCellValue('B6', '陳小明2');
      //$activeWorksheet->setCellValue('B7', '陳小明3');
      //$activeWorksheet->setCellValue('B8', '陳小明4');
      //$activeWorksheet->setCellValue('B9', '陳小明5');
      //$activeWorksheet->setCellValue('B10', '陳小明6');
      //$activeWorksheet->setCellValue('B11', '陳小明7');
      //$activeWorksheet->setCellValue('B12', '陳小明8');
      //$activeWorksheet->setCellValue('B13', '陳小明9');
      //$activeWorksheet->setCellValue('B14', '陳小明10');
      $activeWorksheet->setCellValue('B16', '管理服務主管：');
      $activeWorksheet->setCellValue('B17', '營運經理-');
      $activeWorksheet->setCellValue('D17', '黃浩瑋');
      //$activeWorksheet->setCellValue('F17', '行動:');
      $activeWorksheet->setCellValue('F17', '0000-000000');
      $activeWorksheet->setCellValue('B18', '勤務課長-');
      $activeWorksheet->setCellValue('D18', '劉品毅');
      //$activeWorksheet->setCellValue('F18', '行動:');
      $activeWorksheet->setCellValue('F18', '0000-000000');

      $activeWorksheet->setCellValue('N16', '組長');
      $activeWorksheet->setCellValue('N17', '王小明');
      $activeWorksheet->setCellValue('Q17', '0000-000000');

      $activeWorksheet->setCellValue('N18', '王小明');
      $activeWorksheet->setCellValue('Q18', '0000-000000');

      $activeWorksheet->setCellValue('N19', '王小明');
      $activeWorksheet->setCellValue('Q19', '0000-000000');

      $activeWorksheet->setCellValue('N20', '王小明');
      $activeWorksheet->setCellValue('Q20', '0000-000000');

      $activeWorksheet->setCellValue('N21', '王小明');
      $activeWorksheet->setCellValue('Q21', '0000-000000');

      $activeWorksheet->setCellValue('N22', '王小明');
      $activeWorksheet->setCellValue('Q22', '0000-000000');

      $activeWorksheet->setCellValue('N23', '王小明');
      $activeWorksheet->setCellValue('Q23', '0000-000000');

      $activeWorksheet->setCellValue('N24', '王小明');
      $activeWorksheet->setCellValue('Q24', '0000-000000');

      $activeWorksheet->setCellValue('X16', '排班代號說明');
      $activeWorksheet->setCellValue('X17', 'A');
      $activeWorksheet->setCellValueExplicit('Y17', '00:00',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA17', '-');
      $activeWorksheet->setCellValueExplicit('AB17', '02:00',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      
      $activeWorksheet->setCellValue('X18', 'B');
      $activeWorksheet->setCellValueExplicit('Y18', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA18', '-');
      $activeWorksheet->setCellValueExplicit('AB18', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('X19', 'C');
      $activeWorksheet->setCellValueExplicit('Y19', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA19', '-');
      $activeWorksheet->setCellValueExplicit('AB19', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('X20', 'D');
      $activeWorksheet->setCellValueExplicit('Y20', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA20', '-');
      $activeWorksheet->setCellValueExplicit('AB20', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('X21', 'E');
      $activeWorksheet->setCellValueExplicit('Y21', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA21', '-');
      $activeWorksheet->setCellValueExplicit('AB21', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD17', 'F');
      $activeWorksheet->setCellValueExplicit('AE17', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG17', '-');
      $activeWorksheet->setCellValueExplicit('AH17', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD18', 'G');
      $activeWorksheet->setCellValueExplicit('AE18', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG18', '-');
      $activeWorksheet->setCellValueExplicit('AH18', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD19', 'H');
      $activeWorksheet->setCellValueExplicit('AE19', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG19', '-');
      $activeWorksheet->setCellValueExplicit('AH19', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD20', 'I');
      $activeWorksheet->setCellValueExplicit('AE20', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG20', '-');
      $activeWorksheet->setCellValueExplicit('AH20', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD21', 'J');
      $activeWorksheet->setCellValueExplicit('AE21', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG21', '-');
      $activeWorksheet->setCellValueExplicit('AH21', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('X22', '每班時數');
      $activeWorksheet->setCellValue('AH22', '小時');


      $activeWorksheet->setCellValue('B22', '註解說明區');
      $activeWorksheet->setCellValue('B23', '註解說明區');
      $activeWorksheet->setCellValue('B24', '註解說明區');
      $activeWorksheet->setCellValue('B25', '註解說明區');
      $activeWorksheet->setCellValue('B26', '註解說明區');
      $activeWorksheet->setCellValue('B27', '註解說明區');

      $activeWorksheet->setCellValue('X23', '值班人員簽名');
      $activeWorksheet->setCellValue('AD23', '主管簽名');
      $activeWorksheet->setCellValue('AG22', '2');

      for ($i=5;$i<=14;$i++){
         $activeWorksheet->setCellValue("AH"."$i",'=(COUNTIF(C'.$i.':AG'.$i.',"A")+COUNTIF(C'.$i.':AG'.$i.',"B")+COUNTIF(C'.$i.':AG'.$i.',"C")+COUNTIF(C'.$i.':AG'.$i.',"D")+COUNTIF(C'.$i.':AG'.$i.',"E")+COUNTIF(C'.$i.':AG'.$i.',"F")+COUNTIF(C'.$i.':AG'.$i.',"G")+COUNTIF(C'.$i.':AG'.$i.',"H")+COUNTIF(C'.$i.':AG'.$i.',"I")+COUNTIF(C'.$i.':AG'.$i.',"J"))*AG22');
      };

      $activeWorksheet->setCellValue('AH15', '=SUM(AH5:AH13)');
      //$spreadsheet -> getActiveSheet()->mergeCells('A1:AG28');

      //echo everyday萬年曆
      $column= array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
               'AA','AB','AC','AD','AE','AF','AG','AH');
  
      $weekarray=array('日','一','二','三','四','五','六');
      //$day=$weekarray[date("w",strtotime("2023-4-1"))];
      //dd($column[1]);

      $WhichMonth = $request->input('exportbymonth');
      //dd($WhichMonth);
      $years=substr("$WhichMonth", 0,4); 
      $months=substr("$WhichMonth", -2); 
      //dd($years,$months);

      $activeWorksheet->setCellValue('K2', "$years");
      $activeWorksheet->setCellValue('N2', "$months");
      $day=$weekarray[date("w",strtotime("$WhichMonth".'-01'))];

      $offset=implode([date("w",strtotime("$WhichMonth".'-01'))]);
      //dd($day,gettype($offset),$offset);
      $days_per_month=implode([date("t",strtotime("$WhichMonth"))]);
      //dd($days_per_month);
      
      for ($i=0;$i<=$days_per_month-1;$i++){
         $activeWorksheet->setCellValue("$column[$i]".'3', $i+1);

         $activeWorksheet->setCellValue("$column[$i]".'4', "$weekarray[$offset]");
         $offset=($offset+1)%7;
      };//end萬年曆

      $writer = new Xlsx($spreadsheet);
      // redirect output to client browser
      $file_name = 'Schedules_'.date('Y_m_d_H_i_s');
      $styleCenterArray = [
         'borders' => [
            'allBorders' => [
               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
               'color' => ['argb' => '00000000'],
            ],
         ],

         'alignment' => [
            'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
         ]      
      ];

      $styleArray = [
         'borders' => [
            'allBorders' => [
               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
               'color' => ['argb' => '00000000'],
            ],
            'outline' => [
               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
         ],
         
         'alignment' => [
               //'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
               'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
         ]
      ];

      $activeWorksheet->getStyle('A1:AH14')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('A15')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('X16')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('X23')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('AD23')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('AH15')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('AH22')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('X22')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('AG22')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('X17:AH21')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('A1:AH27')->applyFromArray($styleArray);

      $spreadsheet->getActiveSheet()->getStyle('A1:AH1')
      ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);

      $spreadsheet->getActiveSheet()->getStyle('B2:AD2')
      ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);

      $spreadsheet->getActiveSheet()->getStyle('A1:AH27')
      ->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

      $spreadsheet->getActiveSheet()->getStyle('B2:AD2')
      ->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

      $exportmonth = $request->input('exportbymonth');
      $timestamp = strtotime($exportmonth);
      $year=date("Y",$timestamp);
      $month=date("m",$timestamp);

      $option=$request->input('type');

      //write to excel
      $exportschedules = DB::table('schedules')->where([  
         ['customer_id','=',$option],      
         //['customer_id','=','台南好市多'],  
         ['year', '=', "$year"],
         ['month', '=', "$month"],
     ])->get();

     //抓資料進array
      $arrays=[];
      
     foreach($exportschedules as $schedule)
     {
         $arrays[] =  (array) $schedule;
     }
     // Dump array with object-arrays
     //dd($arrays);

     if ($arrays==[])
     {
         return back()->with('ex_error', '查無資料！');
     }

   $num=count($arrays);
   for ($counter=0;$counter<$num;$counter++){
      for ($i=1;$i<=31;$i++){
         $activeWorksheet->setCellValue($column[$i-1].($counter+5), $arrays[$counter]["day".$i]);
         //var_dump($column[$i]."5_is_".$arrays[$counter]["day".$i+1]."<br>");
      };
   }

   for ($i=0;$i<$num;$i++){
      $activeWorksheet->setCellValue('B'.($i+5), $arrays[$i]['employee_id']);
   }

      $activeWorksheet->setCellValue('A5', $arrays[0]['customer_id']);

      $activeWorksheet->setCellValue('B5', $arrays[0]['employee_id']);
      //var_dump($arrays[$counter]['employee_id']);
      $activeWorksheet->setCellValue('K2', $arrays[0]['year']);
      $activeWorksheet->setCellValue('N2', $arrays[0]['month']);

      $activeWorksheet->setCellValueExplicit('Y17', $arrays[0]['A'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AB17', $arrays[0]['A_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('Y18', $arrays[0]['B'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AB18', $arrays[0]['B_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('Y19', $arrays[0]['C'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AB19', $arrays[0]['C_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('Y20', $arrays[0]['D'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AB20', $arrays[0]['D_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('Y21', $arrays[0]['E'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AB21', $arrays[0]['E_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('AE17', $arrays[0]['F'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AH17', $arrays[0]['F_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('AE18', $arrays[0]['G'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AH18', $arrays[0]['G_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('AE19', $arrays[0]['H'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AH19', $arrays[0]['H_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('AE20', $arrays[0]['I'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AH20', $arrays[0]['I_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValueExplicit('AE21', $arrays[0]['J'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AH21', $arrays[0]['J_end'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
         

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
      header('Cache-Control: max-age=0');

      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save('php://output');
   }


   

   public function download_example(Request $request){
      $spreadsheet = new Spreadsheet();
      $activeWorksheet = $spreadsheet->getActiveSheet();
   
      $spreadsheet->getActiveSheet()->getPageSetup()
      ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
   
      $spreadsheet->getActiveSheet()->getPageSetup()
      ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

      $spreadsheet->getActiveSheet()->getPageMargins()->setTop(0.25);
      $spreadsheet->getActiveSheet()->getPageMargins()->setRight(0.25);
      $spreadsheet->getActiveSheet()->getPageMargins()->setLeft(0.25);
      $spreadsheet->getActiveSheet()->getPageMargins()->setBottom(0.25);

      $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(true);
      $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);

      $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(3);
      $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
      $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(9);
      $spreadsheet->getActiveSheet()->getColumnDimension('AH')->setWidth(6);
      $spreadsheet->getActiveSheet()->getColumnDimension('AG')->setWidth(3.5);

      $spreadsheet->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
      $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);

      for ($i=2;$i<28;$i++)
      {
      $spreadsheet->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
      };
    
      $spreadsheet->getDefaultStyle()->getFont()->setSize(12);
      $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setsize(18);

      $spreadsheet -> getActiveSheet()->mergeCells('B1:AD1');
      $spreadsheet -> getActiveSheet()->mergeCells('AE1:AH1');
      $spreadsheet -> getActiveSheet()->mergeCells('A2:A4');

      $spreadsheet -> getActiveSheet()->mergeCells('B2:J2');
      $spreadsheet -> getActiveSheet()->mergeCells('K2:L2');
      $spreadsheet -> getActiveSheet()->mergeCells('N2:O2');
      $spreadsheet -> getActiveSheet()->mergeCells('Q2:S2');
      $spreadsheet -> getActiveSheet()->mergeCells('T2:AD2');      
      $spreadsheet -> getActiveSheet()->mergeCells('AE2:AH2');
      $spreadsheet -> getActiveSheet()->mergeCells('A5:A14');
      $spreadsheet -> getActiveSheet()->mergeCells('A15:A27');
      $spreadsheet -> getActiveSheet()->mergeCells('AH3:AH4');
      $spreadsheet -> getActiveSheet()->mergeCells('A15:A27');

      $spreadsheet -> getActiveSheet()->mergeCells('B15:AG15');
      $spreadsheet -> getActiveSheet()->mergeCells('B16:E16');
      $spreadsheet -> getActiveSheet()->mergeCells('B17:E17');
      $spreadsheet -> getActiveSheet()->mergeCells('B18:E18');
      $spreadsheet -> getActiveSheet()->mergeCells('B19:E19');
      $spreadsheet -> getActiveSheet()->mergeCells('B20:E20');
      $spreadsheet -> getActiveSheet()->mergeCells('B21:E21');

      $spreadsheet -> getActiveSheet()->mergeCells('F17:I17');
      $spreadsheet -> getActiveSheet()->mergeCells('F18:I18');
      $spreadsheet -> getActiveSheet()->mergeCells('F19:I19');
      $spreadsheet -> getActiveSheet()->mergeCells('F20:I20');
      $spreadsheet -> getActiveSheet()->mergeCells('F21:I21');

      $spreadsheet -> getActiveSheet()->mergeCells('F16:I16');
      $spreadsheet -> getActiveSheet()->mergeCells('J16:M21');
      $spreadsheet -> getActiveSheet()->mergeCells('U16:W24');
      
      $spreadsheet -> getActiveSheet()->mergeCells('N16:P16');
      $spreadsheet -> getActiveSheet()->mergeCells('Q16:T16');
      $spreadsheet -> getActiveSheet()->mergeCells('N17:P17');
      $spreadsheet -> getActiveSheet()->mergeCells('Q17:T17');
      $spreadsheet -> getActiveSheet()->mergeCells('Q18:T18');
      $spreadsheet -> getActiveSheet()->mergeCells('N18:P18');
      $spreadsheet -> getActiveSheet()->mergeCells('N19:P19');
      $spreadsheet -> getActiveSheet()->mergeCells('Q19:T19');
      $spreadsheet -> getActiveSheet()->mergeCells('N20:P20');
      $spreadsheet -> getActiveSheet()->mergeCells('Q20:T20');
      $spreadsheet -> getActiveSheet()->mergeCells('N21:P21');
      $spreadsheet -> getActiveSheet()->mergeCells('Q21:T21');
      $spreadsheet -> getActiveSheet()->mergeCells('N22:P22');
      $spreadsheet -> getActiveSheet()->mergeCells('Q22:T22');
      $spreadsheet -> getActiveSheet()->mergeCells('N23:P23');
      $spreadsheet -> getActiveSheet()->mergeCells('Q23:T23');
      $spreadsheet -> getActiveSheet()->mergeCells('N24:P24');
      $spreadsheet -> getActiveSheet()->mergeCells('Q24:T24');

      $spreadsheet -> getActiveSheet()->mergeCells('X16:AH16');
      $spreadsheet -> getActiveSheet()->mergeCells('Y17:Z17');
      $spreadsheet -> getActiveSheet()->mergeCells('AB17:AC17');
      $spreadsheet -> getActiveSheet()->mergeCells('AE17:AF17');

      $spreadsheet -> getActiveSheet()->mergeCells('Y18:Z18');
      $spreadsheet -> getActiveSheet()->mergeCells('AB18:AC18');
      $spreadsheet -> getActiveSheet()->mergeCells('AE18:AF18');

      $spreadsheet -> getActiveSheet()->mergeCells('Y19:Z19');
      $spreadsheet -> getActiveSheet()->mergeCells('AB19:AC19');
      $spreadsheet -> getActiveSheet()->mergeCells('AE19:AF19');

      $spreadsheet -> getActiveSheet()->mergeCells('Y20:Z20');
      $spreadsheet -> getActiveSheet()->mergeCells('AB20:AC20');
      $spreadsheet -> getActiveSheet()->mergeCells('AE20:AF20');

      $spreadsheet -> getActiveSheet()->mergeCells('Y21:Z21');
      $spreadsheet -> getActiveSheet()->mergeCells('AB21:AC21');
      $spreadsheet -> getActiveSheet()->mergeCells('AE21:AF21');

      $spreadsheet -> getActiveSheet()->mergeCells('X22:AF22');   

      $spreadsheet -> getActiveSheet()->mergeCells('B22:M22');
      $spreadsheet -> getActiveSheet()->mergeCells('B23:M23');
      $spreadsheet -> getActiveSheet()->mergeCells('B24:M24');
      $spreadsheet -> getActiveSheet()->mergeCells('B25:W25');
      $spreadsheet -> getActiveSheet()->mergeCells('B26:W26');
      $spreadsheet -> getActiveSheet()->mergeCells('B27:W27');

      $spreadsheet -> getActiveSheet()->mergeCells('X23:AC23');
      $spreadsheet -> getActiveSheet()->mergeCells('AD23:AH23');
      $spreadsheet -> getActiveSheet()->mergeCells('X24:AC27');
      $spreadsheet -> getActiveSheet()->mergeCells('AD24:AH27');

      $activeWorksheet->setCellValue('B1', '萬宇保全');
      $activeWorksheet->setCellValue('A2', '名稱');

      $activeWorksheet->setCellValue('K2', '2023');
      $activeWorksheet->setCellValue('M2', '年');
      $activeWorksheet->setCellValue('N2', '10');
      $activeWorksheet->setCellValue('P2', '月');
      $activeWorksheet->setCellValue('Q2', '輪值表');
      $activeWorksheet->setCellValue('B3', '日期');
      $activeWorksheet->setCellValue('B4', '值勤員');
      $activeWorksheet->setCellValue('AH3', '實勤');

      $today = date('Y-m-d');
      $activeWorksheet->setCellValueExplicit('AE2', "$today",\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('A15', '交代事項');   

      $activeWorksheet->setCellValue('B16', '管理服務主管：');
      $activeWorksheet->setCellValue('B17', '營運經理-');
      $activeWorksheet->setCellValue('D17', '黃浩瑋');

      $activeWorksheet->setCellValue('F17', '0000-000000');
      $activeWorksheet->setCellValue('B18', '勤務課長-');
      $activeWorksheet->setCellValue('D18', '劉品毅');

      $activeWorksheet->setCellValue('F18', '0000-000000');

      $activeWorksheet->setCellValue('N16', '組長');
      $activeWorksheet->setCellValue('N17', '王小明');
      $activeWorksheet->setCellValue('Q17', '0000-000000');

      $activeWorksheet->setCellValue('N18', '王小明');
      $activeWorksheet->setCellValue('Q18', '0000-000000');

      $activeWorksheet->setCellValue('N19', '王小明');
      $activeWorksheet->setCellValue('Q19', '0000-000000');

      $activeWorksheet->setCellValue('N20', '王小明');
      $activeWorksheet->setCellValue('Q20', '0000-000000');

      $activeWorksheet->setCellValue('N21', '王小明');
      $activeWorksheet->setCellValue('Q21', '0000-000000');

      $activeWorksheet->setCellValue('N22', '王小明');
      $activeWorksheet->setCellValue('Q22', '0000-000000');

      $activeWorksheet->setCellValue('N23', '王小明');
      $activeWorksheet->setCellValue('Q23', '0000-000000');

      $activeWorksheet->setCellValue('N24', '王小明');
      $activeWorksheet->setCellValue('Q24', '0000-000000');

      $activeWorksheet->setCellValue('X16', '排班代號說明');
      $activeWorksheet->setCellValue('X17', 'A');
      $activeWorksheet->setCellValueExplicit('Y17', '00:00',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA17', '-');
      $activeWorksheet->setCellValueExplicit('AB17', '02:00',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      
      $activeWorksheet->setCellValue('X18', 'B');
      $activeWorksheet->setCellValueExplicit('Y18', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA18', '-');
      $activeWorksheet->setCellValueExplicit('AB18', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('X19', 'C');
      $activeWorksheet->setCellValueExplicit('Y19', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA19', '-');
      $activeWorksheet->setCellValueExplicit('AB19', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('X20', 'D');
      $activeWorksheet->setCellValueExplicit('Y20', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA20', '-');
      $activeWorksheet->setCellValueExplicit('AB20', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('X21', 'E');
      $activeWorksheet->setCellValueExplicit('Y21', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AA21', '-');
      $activeWorksheet->setCellValueExplicit('AB21', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD17', 'F');
      $activeWorksheet->setCellValueExplicit('AE17', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG17', '-');
      $activeWorksheet->setCellValueExplicit('AH17', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD18', 'G');
      $activeWorksheet->setCellValueExplicit('AE18', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG18', '-');
      $activeWorksheet->setCellValueExplicit('AH18', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD19', 'H');
      $activeWorksheet->setCellValueExplicit('AE19', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG19', '-');
      $activeWorksheet->setCellValueExplicit('AH19', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD20', 'I');
      $activeWorksheet->setCellValueExplicit('AE20', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG20', '-');
      $activeWorksheet->setCellValueExplicit('AH20', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('AD21', 'J');
      $activeWorksheet->setCellValueExplicit('AE21', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValue('AG21', '-');
      $activeWorksheet->setCellValueExplicit('AH21', '',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      $activeWorksheet->setCellValue('X22', '每班時數');
      $activeWorksheet->setCellValue('AH22', '小時');
      $activeWorksheet->setCellValue('B22', '註解說明區');
      $activeWorksheet->setCellValue('B23', '註解說明區');
      $activeWorksheet->setCellValue('B24', '註解說明區');
      $activeWorksheet->setCellValue('B25', '註解說明區');
      $activeWorksheet->setCellValue('B26', '註解說明區');
      $activeWorksheet->setCellValue('B27', '註解說明區');
      $activeWorksheet->setCellValue('X23', '值班人員簽名');
      $activeWorksheet->setCellValue('AD23', '主管簽名');
      $activeWorksheet->setCellValue('AG22', '12');

      for ($i=5;$i<=14;$i++){
         $activeWorksheet->setCellValue("AH"."$i",'=(COUNTIF(C'.$i.':AG'.$i.',"A")+COUNTIF(C'.$i.':AG'.$i.',"B")+COUNTIF(C'.$i.':AG'.$i.',"C")+COUNTIF(C'.$i.':AG'.$i.',"D")+COUNTIF(C'.$i.':AG'.$i.',"E")+COUNTIF(C'.$i.':AG'.$i.',"F")+COUNTIF(C'.$i.':AG'.$i.',"G")+COUNTIF(C'.$i.':AG'.$i.',"H")+COUNTIF(C'.$i.':AG'.$i.',"I")+COUNTIF(C'.$i.':AG'.$i.',"J"))*AG22');
      };

      $activeWorksheet->setCellValue('AH15', '=SUM(AH5:AH13)');

      //echo everyday萬年曆
      $column= array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
               'AA','AB','AC','AD','AE','AF','AG','AH');
  
      $weekarray=array('日','一','二','三','四','五','六');

      $WhichMonth = $request->input('exportbymonth');

      $years=substr("$WhichMonth", 0,4); 
      $months=substr("$WhichMonth", -2); 

      $activeWorksheet->setCellValue('K2', "$years");
      $activeWorksheet->setCellValue('N2', "$months");
      $day=$weekarray[date("w",strtotime("$WhichMonth".'-01'))];

      $offset=implode([date("w",strtotime("$WhichMonth".'-01'))]);

      $days_per_month=implode([date("t",strtotime("$WhichMonth"))]);
      
      for ($i=0;$i<=$days_per_month-1;$i++){
         $activeWorksheet->setCellValue("$column[$i]".'3', $i+1);

         $activeWorksheet->setCellValue("$column[$i]".'4', "$weekarray[$offset]");
         $offset=($offset+1)%7;
      };//end萬年曆

      $writer = new Xlsx($spreadsheet);
      // redirect output to client browser
      $file_name = 'Schedules_'.date('Y_m_d_H_i_s');
      $styleCenterArray = [
         'borders' => [
            'allBorders' => [
               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
               'color' => ['argb' => '00000000'],
            ],
         ],

         'alignment' => [
            'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
         ]      
      ];

      $styleArray = [
         'borders' => [
            'allBorders' => [
               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
               'color' => ['argb' => '00000000'],
            ],
            'outline' => [
               'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
         ],
         
         'alignment' => [
               //'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
               'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
         ]
      ];

      $activeWorksheet->getStyle('A1:AH14')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('A15')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('X16')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('X23')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('AD23')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('AH15')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('AH22')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('X22')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('AG22')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('X17:AH21')->applyFromArray($styleCenterArray);
      $activeWorksheet->getStyle('A1:AH27')->applyFromArray($styleArray);

      $spreadsheet->getActiveSheet()->getStyle('A1:AH1')
      ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);

      $spreadsheet->getActiveSheet()->getStyle('B2:AD2')
      ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);

      $spreadsheet->getActiveSheet()->getStyle('A1:AH27')
      ->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

      $spreadsheet->getActiveSheet()->getStyle('B2:AD2')
      ->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
   


      $activeWorksheet->setCellValue('B5','名字');
      $activeWorksheet->setCellValue('A5', '客戶名稱');
      $activeWorksheet->setCellValueExplicit('Y17', '7:00',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AB17', '21:00',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('Y18', '21:00',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
      $activeWorksheet->setCellValueExplicit('AB18','07:00',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
      header('Cache-Control: max-age=0');

      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save('php://output');
   }
}