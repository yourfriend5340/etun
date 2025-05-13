<?php
namespace App\Services;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\twotime_table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessTableService
{
    //離職單api
    public function resignStore($request)
    {
      $json=json_decode($request->input('reg'));

      $file = $request->file('file');
      $time = date('Y-m-d',strtotime($json->day));

      if($json->EmployeeID != null)
      {

         $testID = DB::table('employees')->where('member_sn',$json->EmployeeID)->count();

         if($testID == 0){
            $message =  '查無此員工id';
            return $message;
         }

         if ($request->file('file')!=null){
            $imageName = $json->EmployeeID.'_'.$time.'.'.$request->file('file')->extension();
            $path = $request->file('file')->storeas('resign/'.$json->EmployeeID,$imageName);

            $test = DB::table('twotime_table')->where([
               ['empid',$json->EmployeeID],
               ['type','離職'],
               ['start',$time],
            ])->count();

            if($test == 0)
            {
               try{
                  $data=[
                     'empid'=>$json->EmployeeID,
                     'type'=>'離職',
                     'start'=>$time,
                     'end'=>null,
                     'reason'=>$json->reason,
                     'filePath'=>'resign/'.$json->EmployeeID.'/'.$imageName,
                  ];

                  twotime_table::create($data);
               }
               catch(Exception $e){
                  $message =  $e->getMessage();
               }
            }
            else
            {
               $data=[
                  'empid'=>$json->EmployeeID,
                  'type'=>'離職',
                  'start'=>$time,
                  'end'=>null,
                  'reason'=>$json->reason,
                  'filePath'=>'resign/'.$json->EmployeeID.'/'.$imageName,
               ];

               try{
                  twotime_table::where([
                     ['empid',$json->EmployeeID],
                     ['type','離職'],
                     ['start',$time],
                  ])->update($data);
               }
               catch(Exception $e){
                  $message =  $e->getMessage();
               }
            }
            $message = '已上傳成功，請待審核，審核後，結果會在下次登入時告知';
         }
         else{
            $message = '請附上簽名檔';
         }
      }
      else{
         $message = '請確認好JSON資料格式';
      }

      return $message;
   }
    

    //請假單api
    public function leaveStore(Request $request)
   {

      $json=json_decode($request->input('reg'));

      $file = $request->file('file');
      $startTime = date('Y-m-d H:i',strtotime($json->start));
      $endTime = date('Y-m-d H:i',strtotime($json->end));

      if(strtotime($endTime) < strtotime($startTime)){
         $message =  '結束時間不可能比開始時間還早發生';
         return $message;
      }
      
      if($json->EmployeeID != null)
      {
         $testID = DB::table('employees')->where('member_sn',$json->EmployeeID)->count();

         if($testID == 0){
            $message =  '查無此員工id';
            return $message;
         }

         if ($request->file('file')!=null){
            $imageName = $json->EmployeeID.'_'.$startTime.'.'.$request->file('file')->extension();
            $path = $request->file('file')->storeas('leave/'.$json->EmployeeID,$imageName);

            $test = DB::table('twotime_table')->where([
               ['empid',$json->EmployeeID],
               ['type','請假'],
               ['start',$startTime],
               ['end',$endTime],
               ['status',null]
            ])->count();

            if($test == 0)
            {
               $data=[
                  'empid'=>$json->EmployeeID,
                  'type'=>'請假',
                  'start'=>$startTime,
                  'end'=>$endTime,
                  'reason'=>$json->reason,
                  'filePath'=>'leave/'.$json->EmployeeID.'/'.$imageName,
               ];

               try{
                  twotime_table::create($data);
               }
               catch(Exception $e){
                  $message =  $e->getMessage();
               }
            }
            else
            {
               $data=[
                  'empid'=>$json->EmployeeID,
                  'type'=>'請假',
                  'start'=>$startTime,
                  'end'=>$endTime,
                  'reason'=>$json->reason,
                  'filePath'=>'leave/'.$json->EmployeeID.'/'.$imageName,
               ];

               try{
                  twotime_table::where([
                     ['empid',$json->EmployeeID],
                     ['type','請假'],
                     ['start',$startTime],
                     ['end',$endTime],
                  ])->update($data);
               }
               catch(Exception $e){
                  $message =  $e->getMessage();
               }
            }
            $message = '已上傳成功，請待審核，審核後，結果會在下次登入時告知';
         }
         else{
            $message = '請附上簽名檔';
         }
      }
      else{
         $message = '請確認好JSON資料格式';
      }

      return $message;
   }


}