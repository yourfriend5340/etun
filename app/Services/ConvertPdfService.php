<?php
namespace App\Services;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\twotime_table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Mpdf\Mpdf;

class ConvertPdfService
{
   public function convertTable($id,$type,$extraId)
   {
      $request = DB::table('twotime_table')
                           ->join('employees','twotime_table.empid','employees.member_sn')               
                           ->where('twotime_table.id',$id)
                           ->first((array(('twotime_table.*'),'employees.member_name','employees.organize','employees.SSN','employees.position')));

      $requestName = DB::table('extra_schedules')
                     ->join('employees','extra_schedules.emp_id','employees.member_sn')               
                     ->where('extra_schedules.id',$extraId)
                     ->first()->member_name;                     

      $empId = $request->empid;
      $empName = $request->member_name;

      if($type = '離職')
      {
         $createTime = date('Y/m/d',strtotime($request->created_at));

         // Create a new Spreadsheet object
         $spreadsheet = new Spreadsheet();

         $styleOntline = [
            'borders' => [
                  'outline' => [
                     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                  ],
            ],

         ];

         $styleArray = [
            'alignment' => [
                  'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                  'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 
            ],    
            'borders' => [
                  // 'top' => [
                  //    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  // ],
                  'bottom' => [
                     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  ],
            ]  
         ];

         $styleCenterArray = [
            'alignment' => [
                  'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                  'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 
            ],    
            'borders' => [
                  // 'top' => [
                  //    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  // ],
                  'bottom' => [
                     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                  ],
            ]  
         ];
         
         //設定預設格式
         $spreadsheet->getActiveSheet()->getPageSetup()
         ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

         $spreadsheet->getActiveSheet()->getPageSetup()
         ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
         
         $sheet = $spreadsheet->getActiveSheet();
         $sheet->getPageMargins()->setTop(0.5);
         $sheet->getPageMargins()->setRight(1.2);        
         $sheet->getPageMargins()->setLeft(1.2);
         $sheet->getPageMargins()->setBottom(0.5);

         for ($i=6;$i<=12;$i++){
         $sheet->getRowDimension($i)->setRowHeight(30);//預設高度
         }
         $sheet->getRowDimension(1)->setRowHeight(30);
         $sheet->getRowDimension(3)->setRowHeight(10);
         $sheet->getRowDimension(4)->setRowHeight(20);
         $sheet->getRowDimension(5)->setRowHeight(20);
         $sheet->getRowDimension(6)->setRowHeight(2);
         
         $sheet->getColumnDimension('A')->setWidth(12);  
         $sheet->getColumnDimension('C')->setWidth(5);  
         $sheet->getColumnDimension('E')->setWidth(5);
         $sheet->getColumnDimension('F')->setWidth(5);  
         $sheet->getColumnDimension('G')->setWidth(6);
         $sheet->getColumnDimension('I')->setWidth(8);
         $sheet->getColumnDimension('H')->setWidth(12);
         $sheet->getColumnDimension('J')->setWidth(12);
         $sheet->getColumnDimension('K')->setWidth(14);
         $sheet->getColumnDimension('L')->setWidth(7);
         $sheet->getColumnDimension('M')->setWidth(8);

         $sheet->mergeCells("A1:M1");
         $sheet->mergeCells("A2:M2");
         $sheet->mergeCells("A7:D7");
         $sheet->mergeCells("B4:D4");
         $sheet->mergeCells("H4:J4");
         $sheet->mergeCells("B4:D4");
         $sheet->mergeCells("L5:M5");
         $sheet->mergeCells("B11:D11");
         $sheet->mergeCells("B12:L12");

         $sheet->mergeCells("K8:L8");
         $sheet->mergeCells("K9:L9");
         $sheet->mergeCells("H10:I10");

         //$sheet->mergeCells("A12:D12");
         $sheet->mergeCells("B13:C13");
         $sheet->mergeCells("E13:F13");

         $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(26);//设置字体加粗大小
         $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(18);//设置字体加粗大小
         $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(14);//设置字体加粗大小
         $sheet->getStyle('H4')->getFont()->setBold(true)->setSize(14);//设置字体加粗大小
         $sheet->getStyle('L4')->getFont()->setBold(true)->setSize(14);//设置字体加粗大小
         $sheet->getStyle('L5')->getFont()->setBold(true)->setSize(14);//设置字体加粗大小
         $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
         $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
         $sheet->getStyle('H4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
         $sheet->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

         for($i=7;$i<=13;$i++){
            $sheet->getStyle("A$i:M$i")->applyFromArray($styleArray);
         }

         // $sheet->getStyle("D8")->applyFromArray($styleCenterArray);
         // $sheet->getStyle("D9")->applyFromArray($styleCenterArray);
         // $sheet->getStyle("D10")->applyFromArray($styleCenterArray);
         // $sheet->getStyle("H8")->applyFromArray($styleCenterArray);
         // $sheet->getStyle("H9")->applyFromArray($styleCenterArray);
         // $sheet->getStyle("J8")->applyFromArray($styleCenterArray);
         // $sheet->getStyle("J9")->applyFromArray($styleCenterArray);
         $sheet->getStyle("A6:M13")->applyFromArray($styleOntline);
         
         //寫入資料
         $sheet->setCellValue('A1', $request->organize);
         $sheet->setCellValue('A2', '員工請假單');  
         $sheet->setCellValue('B4', '姓名：'.$request->member_name);
         $sheet->setCellValue('H4', '職稱：'.$request->position);
         $sheet->setCellValue('L4','假別：APP請假');  
         $sheet->setCellValue('L5', '申請日期：'.$createTime);

         $sheet->setCellValue('A7', '請  假  起  迄  時  間');
         $sheet->setCellValue('A8', '自民國');
         $sheet->setCellValue('C8', '年');
         $sheet->setCellValue('E8', '月');
         $sheet->setCellValue('G8', '日');
         $sheet->setCellValue('I8', '時');
         $sheet->setCellValue('k8', '分起');

         $sheet->setCellValue('A9', '至民國');
         $sheet->setCellValue('C9', '年');
         $sheet->setCellValue('E9', '月');
         $sheet->setCellValue('G9', '日');
         $sheet->setCellValue('I9', '時');
         $sheet->setCellValue('k9', '分起');
         $sheet->setCellValue('A10', '合計共');
         $sheet->setCellValue('C10', '日');
         $sheet->setCellValue('E10', '時');
         $sheet->setCellValue('G10', '分');

         $sheet->setCellValue('A12', '請假事由:');   
         $sheet->setCellValue('B12',$request->reason);
         $sheet->setCellValue('A11', '代班人:');
         $sheet->setCellValue('B11',$requestName);
         $sheet->setCellValue('A13', '總經理:');
         $sheet->setCellValue('D13', '行政部:');
         $sheet->setCellValue('G13', '經理:');
         $sheet->setCellValue('I13', '管理部:');
         $sheet->setCellValue('K13', '申請人:');

         $startYear = date('Y',strtotime($request->start));
         $startMonth = date('m',strtotime($request->start));
         $startDay = date('d',strtotime($request->start));
         $startHour = date('H',strtotime($request->start));
         $startMinute = date('i',strtotime($request->start));

         $endYear = date('Y',strtotime($request->end));
         $endMonth = date('m',strtotime($request->end));
         $endDay = date('d',strtotime($request->end));
         $endHour = date('H',strtotime($request->end));
         $endMinute = date('i',strtotime($request->end));

         $subTime = strtotime($request->end)-strtotime($request->start);
         $subDay = floor($subTime /86400);
         $subHour = floor(($subTime % 86400) / 3600);
         $subMinute = intval((($subTime % 86400) % 3600) / 60);

         $sheet->setCellValue('B8', $startYear-1911);
         $sheet->setCellValue('D8', "   $startMonth");
         $sheet->setCellValue('F8', $startDay);
         $sheet->setCellValue('H8', "   $startHour");
         $sheet->setCellValue('J8', "   $startMinute");

         $sheet->setCellValue('B9', $endYear-1911);
         $sheet->setCellValue('D9', "   $endMonth");
         $sheet->setCellValue('F9', $endDay);
         $sheet->setCellValue('H9', "   $endHour");
         $sheet->setCellValue('J9', "   $endMinute");  

         $sheet->setCellValue('B10', $subDay);  
         $sheet->setCellValue('D10', " $subHour");
         $sheet->setCellValue('F10', " $subMinute");  
         
         $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
         $drawing->setName('sign');
         $drawing->setDescription('sign');
         $drawing->setPath(storage_path('app').'/'.$request->filePath); // put your path and image here
         $drawing->setCoordinates('L13');
         $drawing->setHeight(120);
         $drawing->setWidth(160);
         $drawing->setOffsetX(10);
         $drawing->setOffsetY(10);
         $drawing->setWorksheet($spreadsheet->getActiveSheet());

         $file_name = '請假單_'.$request->empid.'_'.date('Y-m-d',strtotime($request->start)).'.pdf';
         
      }

      elseif($type == '離職')
      {
         // Create a new Spreadsheet object
         $spreadsheet = new Spreadsheet();

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
            'alignment' => [
                  'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                  
            ]      
         ];
         //設定預設格式
         $spreadsheet->getActiveSheet()->getPageSetup()
         ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

         $spreadsheet->getActiveSheet()->getPageSetup()
         ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
         
         // Retrieve the current active worksheet
         $sheet = $spreadsheet->getActiveSheet();
         $sheet->getStyle("A1")->applyFromArray($styleCenterArray);
         $sheet->getStyle("B2:B9")->applyFromArray($styleArray);
         //$sheet->getDefaultColumnDimension()->setWidth(17);//預設寬度
         //$sheet->getDefaultRowDimension()->setRowHeight(40);//預設高度
         $sheet->getColumnDimension('A')->setWidth(60);
         //$sheet->getColumnDimension('B')->setWidth(50);
         $sheet->getPageMargins()->setTop(0.5);
         $sheet->getPageMargins()->setRight(1);        
         $sheet->getPageMargins()->setLeft(1);
         $sheet->getPageMargins()->setBottom(0.5);
         $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(26);//设置字体加粗大小
         $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(26);//设置字体加粗大小
         
         for ($i=2;$i<=9;$i++){
            $sheet->getStyle("A$i")->getFont()->setBold(true)->setSize(18);
            $sheet->getStyle("B$i")->getFont()->setBold(true)->setSize(18);
         }

         $sheet->mergeCells('A1:D1');
         for ($i=2;$i<=9;$i++){
            $sheet->mergeCells("B$i:D$i");
         }

         $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
         $sheet->setCellValue('A3', '姓名：     '.$request->member_name);
         $sheet->setCellValue('A4', '身份字號：  '.$request->SSN);
         $sheet->setCellValue('A6', '請假事由：  '.$request->reason);
         $sheet->setCellValue('A8', '員工簽名：  ');    


         if($request->type == '請假')
         {
            $sheet->setCellValue('A1', $request->organize.'  請假單');  
            $sheet->setCellValue('A7','請假日期：  '.date('Y-m-d H:i',strtotime($request->start)).' 至 '.date('Y-m-d H:i',strtotime($request->end)));    
            $file_name = '請假單_'.$request->empid.'_'.date('Y-m-d_H-i',strtotime($request->start));
         }
         elseif($request->type == '離職')
         {
            $sheet->setCellValue('A1', $request->organize.'  離職單');  
            $sheet->setCellValue('A7','離職日期：  '.date('Y-m-d',strtotime($request->start))); 
            $file_name = '離職單_'.$request->empid.'_'.date('Y-m-d',strtotime($request->start));
         }

         $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
         $drawing->setName('sign');
         $drawing->setDescription('sign');
         $drawing->setPath(storage_path('app').'/'.$request->filePath); // put your path and image here
         $drawing->setCoordinates('A9');
         $drawing->setHeight(300);
         $drawing->setWidth(400);
         $drawing->setOffsetX(100);
         $drawing->setWorksheet($spreadsheet->getActiveSheet());
      }

      //輸出pdfÍÍ
      //$pdfWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
      $pdfWriter = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);

      $pdfWriter->setConfig([
            'format' => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'useSubstitutions' => true,
      ]);

      //檢查資料是否存在
      $storagePath = storage_path().'/app/public';
         
      if($request->type =='請假')
      {
            if(!is_dir($storagePath."/請假"))
            {
               mkdir($storagePath."/請假",0777);
            }
            if(!is_dir($storagePath."/請假/$empId"))
            {
               mkdir($storagePath."/請假/$empId",0777);
            }
      }
      if($request->type == '離職')
      {
         if(!is_dir($storagePath.'/離職')){
            mkdir($storagePath."/離職",0777);
         }
         if(!is_dir($storagePath."/離職/$empId")){
            mkdir($storagePath."/離職/$empId",0777);
         }
      }

      $subPath = "/$request->type/$empId/";
      $path = $storagePath.$subPath;

      //header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      //header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
      // header("Content-type:application/pdf");
      // header('Content-Disposition: attachment;filename="'.$file_name.'.pdf');
      // header('Cache-Control: max-age=0');
      // $pdfWriter->save('php://output');

      $pdfWriter->save($path.$file_name);

      return $subPath.$file_name;

   }

}