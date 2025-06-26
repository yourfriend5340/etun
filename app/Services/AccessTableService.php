<?php
namespace App\Services;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\twotime_table;
use App\Models\extra_schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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


   public function export_extra_schedule($time)
   {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        //設定預設格式
        $spreadsheet->getActiveSheet()->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        $spreadsheet->getActiveSheet()->getPageSetup()->setScale(40);
        
        // Retrieve the current active worksheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(9);//預設寬度
        $sheet->getDefaultRowDimension()->setRowHeight(30);//預設高度

        $sheet->getColumnDimension('AF')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getRowDimension('2')->setRowHeight(20);  
        $sheet->getRowDimension('3')->setRowHeight(48);     
   
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);
        $sheet->getPageMargins()->setBottom(0.5);

        $styleCenterArray = [
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
               //'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
         ]
      ];

      //$sheet->getStyle("A1:AD23")->applyFromArray($styleCenterArray);
      //$sheet->getStyle("A2:AB44")->applyFromArray($styleCenterArray);
      // $sheet->getStyle("A35:A40")->applyFromArray($styleCenterArray);
      // $sheet->getStyle("B35:F40")->applyFromArray($styleArray);
      // $sheet->getStyle('B35:B40')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
      // $sheet->getStyle('A2:F34')->getFont()->setBold(true)->setSize(12);//设置字体加粗大小
      // $sheet->getStyle('B35:B40')->getFont()->setBold(true)->setSize(10);//设置字体加粗大小


      $month = date("m",strtotime($time));
      $year = date("Y",strtotime($time));
      $EndDay = date('Y-m-t', strtotime($time));
      $startDay = date('Y-m-01', strtotime($time));  

      //首行格式
      $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
      $sheet->getStyle('B1:S1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
      $sheet->getStyle('S1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
      $sheet->getStyle('A1:AF1')->getFont()->setBold(true)->setSize(22);//设置字体加粗大小
      $sheet->getStyle('A2:AF2')->getFont()->setBold(true)->setSize(16);//设置字体加粗大小
      $sheet->mergeCells('A1:N1');
      $sheet->setCellValue('A1', '萬宇股份有限公司');  
      $sheet->mergeCells('O1:P1');
      $sheet->setCellValue('O1', $year);
      $sheet->setCellValue('Q1', '年');
      $sheet->setCellValue('R1', $month);
      $sheet->setCellValue('S1', '月');
      $sheet->setCellValue('T1','代班人員紀錄表');

      

      $query = DB::table('extra_schedules')
               ->leftjoin('employees as t1','extra_schedules.emp_id','=','t1.member_sn')
               ->leftjoin('employees as t2','extra_schedules.leave_member','=','t2.member_sn')
               ->leftjoin('employees','employees.member_sn','=','extra_schedules.leave_member')
               ->select('extra_schedules.*','t1.member_name as emp_name','t2.member_name as leave_name')
               ->whereBetween(
                  "start",[$startDay,$EndDay],
               )->get();
      $serial = 0;            
      for($i=0;$i<count($query);$i++){
         $offset = $i +3 ;
         $serial ++;
         $sheet->setCellValue("A$offset",$serial);
         $sheet->setCellValue("B$offset",$query[$i]->emp_id);
         $sheet->setCellValue("C$offset",$query[$i]->emp_name);
         $sheet->setCellValue("D$offset",$query[$i]->start);
         $sheet->setCellValue("E$offset",$query[$i]->end);
         $sheet->setCellValue("F$offset",$query[$i]->leave_name);
      }

      $file_name = $time.'代班人員_'.date('Y_m_d H:i:s');

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
      header('Cache-Control: max-age=0');

      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save('php://output');
      exit;

   }

}