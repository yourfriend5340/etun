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
   public function convertTable($id)
   {

      $request = DB::table('twotime_table')
                           ->join('employees','twotime_table.empid','employees.member_sn')               
                           ->where('twotime_table.id',$id)
                           ->first((array(('twotime_table.*'),'employees.member_name','employees.organize','employees.SSN')));

      $empId = $request->empid;
      $empName = $request->member_name;
      
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
         $file_name = '請假單_'.$request->empid.'_'.date('Y-m-d_H-i',strtotime($request->start)).'.pdf';
      }
      elseif($request->type == '離職')
      {
         $sheet->setCellValue('A1', $request->organize.'  離職單');  
         $sheet->setCellValue('A7','離職日期：  '.date('Y-m-d',strtotime($request->start))); 
         $file_name = '離職單_'.$request->empid.'_'.date('Y-m-d',strtotime($request->start)).'.pdf';
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

      //輸出pdfÍÍ
         //$pdfWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
         $pdfWriter = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);

         $pdfWriter->setConfig([
               'format' => 'A4',
               'autoScriptToLang' => true,
               'autoLangToFont' => true,
               'useSubstitutions' => true,
         ]);

      //header(Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      //header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
      //header('Content-Disposition: attachment;filename="'.$file_name.'.pdf"');
      //header('Cache-Control: max-age=0');
      //$pdfWriter->save('php://output');

      //檢查資料是否存在
      $storagePath = public_path();
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

      $pdfWriter->save($path.$file_name);

      return $subPath.$file_name;

   }

}