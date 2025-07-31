<?php
namespace App\Services;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\salaryItem;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Phpoffice\Phpspreadsheet\src\PhpSpreadsheet\Writer\Pdf\Mpdf;

class AccessSalaryService
{
    public function export($request)
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

        $time = strtotime($request->input('exportbymonth'));
        $month = date("m",$time);
        $year = date("Y",$time);

        //首行格式
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B1:R1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('S1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A1:AF1')->getFont()->setBold(true)->setSize(22);//设置字体加粗大小
        $sheet->getStyle('A2:AF2')->getFont()->setBold(true)->setSize(16);//设置字体加粗大小
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', '萬宇股份有限公司');  
        $sheet->mergeCells('O1:P1');
        $sheet->setCellValue('O1', $year);
        $sheet->setCellValue('Q1', '年');
        
        $sheet->mergeCells('R1:S1');
        $sheet->setCellValue('R1', $month);
        $sheet->setCellValue('S1', '月');
        $sheet->mergeCells('T1:AF1');
        $sheet->setCellValue('T1', '薪資試算表');
        $sheet->mergeCells('AD2:AF2');

        //第二行格式
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('E2:P2');
        $sheet->mergeCells('R2:AB2');
        $sheet->setCellValue('E2', '加項');
        $sheet->setCellValue('R2', '減項');  
        
        //第三行格式
        $sheet->setCellValue('A3', '序號');
        $sheet->setCellValue('B3', '姓名');
        $sheet->setCellValue('C3','客戶');
        $sheet->setCellValue('D3', '總時數');
        $sheet->setCellValue('Q3', '應領薪資');
        $sheet->setCellValue('AC3', '總合計'); 
        $sheet->setCellValue('AD3', '勞投保薪');
        $sheet->setCellValue('AE3', '健投保薪');
        $sheet->setCellValue('AF3', '帳號');
        //$sheet->setCellValue('AE4','812-0000104540124452');

        //以下取得在職員工資料、當月班表、當月出勤表
        //在排班表中join客戶名稱進來以利寫入excel
        $empSchedule = DB::table('schedules')
          ->join('customers','schedules.customer_id','customers.customer_id')
          ->join('employees','schedules.employee_id','employees.member_sn')
          ->where([
              ['year',$year],
              ['month',$month]
            ])
          ->orderby('schedules.employee_id')
          ->get(array(('schedules.*'),'customers.firstname','employees.member_name','employees.labor_account','employees.health_account'));//只join firstname欄位

        $empPunch = DB::table('punch_record')
          ->where([
              ['year',$year],
              ['month',$month]
            ])
          ->orderby('employee_id')
          ->get();  
         //dd($empSchedule,$empPunch); 
        $empCount = count($empSchedule); 
        //$test=[];
        for ($i=0;$i<$empCount;$i++){
            //寫入序號
            $sheet->setCellValue('A'.($i+4),($i+1));

            //寫入名字及客戶名稱
            $sheet->setCellValue("B".($i+4),$empSchedule[$i]->member_name );    
            $sheet->setCellValue('C'.($i+4),$empSchedule[$i]->firstname);
            $sheet->setCellValue('AD'.($i+4),$empSchedule[$i]->labor_account);
            $sheet->setCellValue('AE'.($i+4),$empSchedule[$i]->health_account);
            
            //開始計算時數
            $empId = $empSchedule[$i]->employee_id;
            $emplName = $empSchedule[$i]->member_name;
            $cusId = $empSchedule[$i]->customer_id;
            $cusName = $empSchedule[$i]->firstname;
            $totalTime = 0;
            $totalSubTime = 0;
            //第幾天
            for($j=1;$j<=31;$j++){
              $day = 'day'.$j;

              if(isset($empSchedule[$i]->$day) && $empSchedule[$i]->$day != ""){
                $class = $empSchedule[$i]->$day;

                if($class != ""){
                  $lenCount = strlen($class);

                    //取每一天的班，並取班別計算上班時間(一天可多班)
                    for($k=0;$k<$lenCount;$k++){
                      $temp = substr($class,$k,1);
                      $tempEnd = $temp.'_end';
                      $classStart = $empSchedule[$i]->$temp;
                      $classEnd = $empSchedule[$i]->$tempEnd;

                      //排班表該班時數
                      $scheduleTime = (strtotime($classEnd) - strtotime($classStart)) / 60 / 60;

                      $startTime = "";
                      $endTime = "";
                      $punchInTime = "";
                      $punchOutTime = "";
                      //查詢出勤表中時數
                      for($m=0;$m<count($empPunch);$m++){

                        //查詢出勤表中，符合員工id、客戶id、幾號的哪一班的資訊
                        if($empPunch[$m]->employee_id == $empId && $empPunch[$m]->customer_id == $cusId 
                        && $empPunch[$m]->class == $temp && $empPunch[$m]->day == $j){
                            
                            $startTime = $empPunch[$m]->start;
                            $endTime = $empPunch[$m]->end;
                            $punchInTime = $empPunch[$m]->PunchInTime;
                            $punchOutTime = $empPunch[$m]->PunchOutTime;

                            //忘了下班卡的狀況，無法計薪，故設為打上班卡時間
                            if($punchOutTime == null || $punchOutTime == "" )
                            {
                              $punchOutTime = $punchInTime;
                            }

                            //遲到容許時間20分鐘
                            $allowLatePunchInTime = date('Y-m-d H:i:s',strtotime("+ 20 minute",strtotime($startTime)) );
                            $allowLatePunchOutTime = date('Y-m-d H:i:s',strtotime("- 10 minute",strtotime($endTime)) );
                            if(strtotime($punchInTime) < strtotime($allowLatePunchInTime)){
                                $punchInTime = $startTime;
                            }

                            //容許提前10分鐘下班
                            if(strtotime($punchOutTime) > strtotime($allowLatePunchOutTime) ){
                              $punchOutTime = $endTime;
                            }
                            $workTime = round(((strtotime($punchOutTime) - strtotime($punchInTime)) / 60 / 60),1);
                            $totalTime = $totalTime + $workTime;
                            //dd($empPunch,$empSchedule,$day,$temp,$empId,$cusId,$scheduleTime,$punchInTime,$punchOutTime,$allowLatePunchInTime,$allowLatePunchOutTime,$workTime);
                            
                        }
                      }
                      //查無打卡紀錄
                      if($punchInTime == "" && $punchOutTime == ""){
                          $totalSubTime = $totalSubTime + $scheduleTime;
                          //dd($empPunch,$empSchedule,$day,$temp,$cusId,$empId);
                      }
                    }
                }
              }
            }
            $sheet->setCellValue("D".($i+4),$totalTime);
            $sheet->getStyle("Q".($i+4))->getNumberFormat()->setFormatCode('0');
            
            //最後一次迴圈時，合計欄也要設定數字格式
            if($i == ($empCount-1))
            {
              $sheet->getStyle("Q".($i+5))->getNumberFormat()->setFormatCode('0');
            }
            //$salary = 1;
            //if($totalSubTime != 0)
            //{

              $querySalary = DB::table('clock_salary')->where([
                ['member_name',$emplName],
                ['customer',$cusName]
              ])->first();

              if($querySalary == null){
                $salary = 1;
                $type = '時薪';

                $sheet->getStyle('A'.($i+4))->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('ff0000');
              }
              else{
                $salary = $querySalary->salary;
                $type = $querySalary->salaryType;
              }
              //時薪算法
              if($type == '時薪'){
                $countTotalTime = $totalTime;
                $subTotal = $salary.'*'. $countTotalTime;
              }
              //月薪算法
              elseif($type == '月薪')
              {
                $countTotalTime = $totalTime;
                $subTotal = $salary .'/ 240 *'. $countTotalTime;
              }

              //$sheet->setCellValue("R".($i+4),$totalSubTime*$salary);

              //dd($salary,$subTotal,$SUMRANGE);
            //}

            $SUMRANGE = '=SUM(E'.($i+4).':'.'P'.($i+4).')+('.$subTotal.')';
            $sheet->setCellValue("Q".($i+4),$SUMRANGE);
        }

        //此三條參數會變動
        $filterRange = $empCount+3;
        $columnRange = $empCount+4;
        //設定filter範圍
        $sheet->setAutoFilter("A3:AE$filterRange");
        //設定框線的範圍
        $sheet->getStyle("A2:AC$columnRange")->applyFromArray($styleCenterArray);
        $sheet->getStyle("AD2:AF$columnRange")->applyFromArray($styleCenterArray);
        //最後一欄的合計
        $sheet->setCellValue('A'.($columnRange), '合計');
        $sheet->mergeCells('A'.($columnRange).':'.'C'.($columnRange));
        $sheet->getRowDimension($columnRange)->setRowHeight(30);     

        //自動換行
        $range = 'AE'.$filterRange;
        $sheet->getStyle("A4:$range")->getAlignment()->setWrapText(true);

        //加總函數寫入
        $sunColumn = ['D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC'];
        for($i=0;$i<count($sunColumn);$i++)
        {
          $SUMRANGE = '=SUBTOTAL(9,'.$sunColumn[$i].'4:'.$sunColumn[$i].$filterRange.')';

          $sheet->setCellValue($sunColumn[$i].$columnRange , $SUMRANGE);

          $sheet->getStyle($sunColumn[$i])->getNumberFormat()->setFormatCode('0');
        }

         $worksheet = $spreadsheet->getActiveSheet()->toArray();

        for($i=4;$i<=$filterRange;$i++)
        {
          //$SUMRANGE = '=SUM(E'.$i.':'.'P'.$i.')';
          //$sheet->setCellValue("Q$i",$SUMRANGE);

          $DEERANGE = '=Q'.$i.'-SUM(R'.$i.':'.'AB'.$i.')';
          $sheet->setCellValue("AC$i",$DEERANGE);

          $sheet->getStyle("AC$i")->getNumberFormat()->setFormatCode('0');
          $sheet->getRowDimension($i)->setRowHeight(30);  
          
        }
        $file_name = '薪資試算表_'.date('Y_m');



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
   {
      // 要求上傳的文件必須是表格格式
      $request->validate(['select_file'  => 'required|mimes:xls,xlsx']);

      // 如果是 POST 方法才讀文件
      if ($request->isMethod('POST')){
         $file = $request->file('select_file');

         // 判斷文件是否上傳成功
         if ($file->isValid()){
          // 原文件名
          $originalName = $file->getClientOriginalName();
          
          $realPath = $file->getRealPath();
          $extension = $file->extension();

          if('xls' == $extension) 
          {$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();} 
          else     
          {$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();}

          $spreadsheet = $reader->load($file);           
          $worksheet = $spreadsheet->getActiveSheet()->toArray();  // 獲取當前的工作表數據
          //dd($worksheet);

          // if (count($worksheet)!=27 || count($worksheet[0])!=34)
          // {
          //     return back()->with('danger', '資料格式有錯誤，沒有上傳表格');
          // }

          //檢查上傳時間，限制在隔月10前才可上傳班表
          // $today=date("Y-m-d");//TODAY
          // if(isset($worksheet[1][10]) && isset($worksheet[1][13]))
          // $excel_time=$worksheet[1][10].'-'.$worksheet[1][13];
          // $addColumn = ['E','F','G','H','I','J','K','L','M','N','O','P'];
          // $countAddColumn = count($addColumn);
          // $subColomn = ['R','S','T','U','V','W','X','Y','Z','AA','AB'];
          // $countSubColumn = count($subColomn);

          $month = date('Y-m',strtotime($worksheet[0][14].'-'.$worksheet[0][17]));  
          $pattern = "/\d+/";

          DB::table('salary_items')->where([
            ['month',$month],
          ])->delete();
          
          //最後一列資料不讀取
          $accessColomn = count($worksheet)-1;
          for($i=3;$i<$accessColomn;$i++){
            $empName = $worksheet[$i][1];
            $cusName = $worksheet[$i][2];
            $empID = DB::table('employees')->where('member_name',$empName)->get()->pluck('member_sn')->first();
            $cusID = DB::table('customers')->where('firstname',$cusName)->get()->pluck('customer_id')->first(); 

            $addMax = DB::table('salary_items')->where([
                  ['month',$month],
                  ['empid',$empID],
                  ['cusid',$cusID],
                  ['mark','add']
                ])->get()->max('serialNum');

            $subMax = DB::table('salary_items')->where([
                  ['month',$month],
                  ['empid',$empID],
                  ['cusid',$cusID],
                  ['mark','sub']
                ])->get()->max('serialNum');
            //dd($month,$empID,$cusID,$addMax,$subMax);

            if($addMax == null)
            {$addMax = 0;}

            if($subMax == null)
            {$subMax = 0;}


            //讀取加項
            for($j=4;$j<=15;$j++){
                //正規表達式檢查
                $test = preg_match($pattern, $worksheet[$i][$j]);


                if($worksheet[$i][$j] != null)
                {                
                  if($test == 1)
                  {
                    $amount = $worksheet[$i][$j];
                    $item = $worksheet[2][$j];
                    $addMax = $addMax +1 ;
                    
                    $data = [
                      'month'=>$month,
                      'empid'=>$empID,
                      'cusid'=>$cusID,
                      'mark'=>'add',
                      'serialNum'=>$addMax,
                      'item'=>$item,
                      'amount'=>$amount
                    ];

                    salaryItem::create($data);
                  }
                  else{
                    $message = '輸入加減項的數字區中，有發現非數字的資料，請輸入數字';
                    return $message;
                  }
                }

            }
            
            //讀取減項
            for($j=17;$j<=27;$j++){
                $test = preg_match($pattern, $worksheet[$i][$j]);

                if($worksheet[$i][$j] != null)
                {
                  if($test ==1)
                  {
                    $amount = $worksheet[$i][$j];
                    $item = $worksheet[2][$j];
                    $subMax = $subMax +1;

                    $data = [
                      'month'=>$month,
                      'empid'=>$empID,
                      'cusid'=>$cusID,
                      'mark'=>'sub',
                      'serialNum'=>$subMax,
                      'item'=>$item,
                      'amount'=>$amount
                    ];

                    salaryItem::create($data);
                  }
                  else{
                    $message = '輸入加減項的數字區中，有發現非數字的資料，請輸入數字';
                    return $message;
                  }
                }
            }
            
          }
        }
      }
      return 1;
    }

    public function countTotalTime($year,$month,$empId)
    {

        //以下取得在職員工資料、當月班表、當月出勤表
        //在排班表中join客戶名稱進來以利寫入excel
        $empSchedule = DB::table('schedules')
          ->join('customers','schedules.customer_id','customers.customer_id')
          ->join('employees','schedules.employee_id','employees.member_sn')
          ->where([
              ['year',$year],
              ['month',$month],
              ['employee_id',$empId]
            ])
          ->orderby('schedules.employee_id')
          ->get(array(('schedules.*'),'customers.firstname','employees.member_name','employees.labor_account','employees.health_account'));//只join firstname欄位

        $empPunch = DB::table('punch_record')
          ->where([
              ['year',$year],
              ['month',$month],
              ['employee_id',$empId]
            ])
          ->orderby('employee_id')
          ->get();  

        $all = 0;
        $allSalary = 0;
        $empCount = count($empSchedule); 
        for ($i=0;$i<$empCount;$i++){
            
            //開始計算時數
            $emplName = $empSchedule[$i]->member_name;
            $cusId = $empSchedule[$i]->customer_id;
            $cusName = $empSchedule[$i]->firstname;
            $totalTime = 0;
            $totalSubTime = 0;


            //第幾天
            for($j=1;$j<=31;$j++){
              $day = 'day'.$j;

              if(isset($empSchedule[$i]->$day) && $empSchedule[$i]->$day != ""){
                $class = $empSchedule[$i]->$day;

                if($class != ""){
                  $lenCount = strlen($class);

                    //取每一天的班，並取班別計算上班時間(一天可多班)
                    for($k=0;$k<$lenCount;$k++){
                      $temp = substr($class,$k,1);
                      $tempEnd = $temp.'_end';
                      $classStart = $empSchedule[$i]->$temp;
                      $classEnd = $empSchedule[$i]->$tempEnd;

                      //排班表該班時數
                      $scheduleTime = (strtotime($classEnd) - strtotime($classStart)) / 60 / 60;

                      $startTime = "";
                      $endTime = "";
                      $punchInTime = "";
                      $punchOutTime = "";
                      //查詢出勤表中時數
                      for($m=0;$m<count($empPunch);$m++){
                        if(isset($empPunch[$m]->employee_id))
                        {
                          //查詢出勤表中，符合員工id、客戶id、幾號的哪一班的資訊
                          if($empPunch[$m]->employee_id == $empId && $empPunch[$m]->customer_id == $cusId 
                          && $empPunch[$m]->class == $temp && $empPunch[$m]->day == $j){
                              
                              $startTime = $empPunch[$m]->start;
                              $endTime = $empPunch[$m]->end;
                              $punchInTime = $empPunch[$m]->PunchInTime;
                              $punchOutTime = $empPunch[$m]->PunchOutTime;

                              //忘了下班卡的狀況，無法計薪，故設為打上班卡時間
                              if($punchOutTime == null || $punchOutTime == "" )
                              {
                                $punchOutTime = $punchInTime;
                              }
                              //遲到容許時間20分鐘
                              $allowLatePunchInTime = date('Y-m-d H:i:s',strtotime("+ 20 minute",strtotime($startTime)) );
                              $allowLatePunchOutTime = date('Y-m-d H:i:s',strtotime("- 10 minute",strtotime($endTime)) );
                              if(strtotime($punchInTime) < strtotime($allowLatePunchInTime)){
                                  $punchInTime = $startTime;
                              }

                              //容許提前10分鐘下班
                              if(strtotime($punchOutTime) > strtotime($allowLatePunchOutTime) ){
                                $punchOutTime = $endTime;
                              }

                              $workTime = round(((strtotime($punchOutTime) - strtotime($punchInTime)) / 60 / 60),1);
                              $totalTime = $totalTime + $workTime;  
                              //dump($totalTime);   
                          }
                        }
                        else{
                          continue;
                        }
                      }//end loop m
                    }//end loop k
                }//end if
              }//end if
            }//end loop j

              $querySalary = DB::table('clock_salary')->where([
                ['member_name',$emplName],
                ['customer',$cusName]
              ])->first();

              if($querySalary == null){
                $salary = 1;
                $type = '時薪';
              }
              else{
                $salary = $querySalary->salary;
                $type = $querySalary->salaryType;
              }
              //時薪算法
              if($type == '時薪'){
                $countTotalTime = $totalTime;
                $subTotal = $salary*$countTotalTime;
              }
              //月薪算法
              elseif($type == '月薪')
              {
                $countTotalTime = $totalTime;
                $subTotal = $salary / 240 * $countTotalTime;
              }
                $allSalary = $allSalary + $subTotal;
                $all = $totalTime + $all;



        }// end loop i
        $allSalary = round($allSalary);
        
        return [$all,$allSalary];
    }
}