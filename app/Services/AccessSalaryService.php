<?php
namespace App\Services;

use App\Models\Employee;
use App\Models\Customer;
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

        $employee = Employee::where('status','在職')->get();
        $empCount = count($employee);

        for ($i=0;$i<$empCount;$i++){
            $sheet->setCellValue('A'.($i+4),($i+1));
            $sheet->setCellValue("B".($i+4),$employee[$i]->member_name );    
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
        }

        for($i=4;$i<=$filterRange;$i++)
        {
          $SUMRANGE = '=SUM(E'.$i.':'.'P'.$i.')';
          $sheet->setCellValue("Q$i",$SUMRANGE);

          $DEERANGE = '=Q'.$i.'-SUM(R'.$i.':'.'AB'.$i.')';
          $sheet->setCellValue("AC$i",$DEERANGE);

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
}