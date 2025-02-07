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
    public function export()
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
        $sheet->getDefaultColumnDimension()->setWidth(9.5);//預設寬度
        $sheet->getDefaultRowDimension()->setRowHeight(50);//預設高度
        //$sheet->getRowDimension('1')->setRowHeight(26);
        // for ($i=2;$i<=40;$i++){
        //     $sheet->getRowDimension("$i")->setRowHeight(20);
        // }
        // $sheet->getColumnDimension('M')->setWidth(6);
        // $sheet->getColumnDimension('O')->setWidth(6);
        // $sheet->getColumnDimension('C')->setWidth(15.5);
        // $sheet->getColumnDimension('D')->setWidth(15.5);
        // $sheet->getColumnDimension('F')->setWidth(16.5);

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
        $sheet->getStyle("A2:AB23")->applyFromArray($styleCenterArray);
        // $sheet->getStyle("A35:A40")->applyFromArray($styleCenterArray);
        // $sheet->getStyle("B35:F40")->applyFromArray($styleArray);
        // $sheet->getStyle('B35:B40')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        // $sheet->getStyle('A2:F34')->getFont()->setBold(true)->setSize(12);//设置字体加粗大小
        // $sheet->getStyle('B35:B40')->getFont()->setBold(true)->setSize(10);//设置字体加粗大小

        //首行格式
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B1:AB1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:AB1')->getFont()->setBold(true)->setSize(22);//设置字体加粗大小
        $sheet->mergeCells('M1:N1');
        $sheet->mergeCells('P1:Q1');

        $sheet->setCellValue('A1', '萬宇股份有限公司');  
        $sheet->setCellValue('M1', '113');
        $sheet->setCellValue('O1', '年');
        $sheet->setCellValue('P1', '12');
        $sheet->setCellValue('R1', '月');
        $sheet->setCellValue('S1', '薪');
        $sheet->setCellValue('T1', '資');
        $sheet->setCellValue('U1', '明');
        $sheet->setCellValue('V1', '細');
        $sheet->setCellValue('W1', '表');  

        //第二行格式
        $sheet->mergeCells('D2:O2');
        $sheet->mergeCells('Q2:AA2');
        $sheet->setCellValue('D2', '加項');
        $sheet->setCellValue('Q2', '減項');  
        
        //第三行格式
        $sheet->setCellValue('A3', '序號');
        $sheet->setCellValue('B3', '姓名');
        $sheet->setCellValue('C3', '總時數');
        $sheet->setCellValue('P3', '應領薪資');
        $sheet->setCellValue('AB3', '總合計'); 
        $sheet->setCellValue('AC3', '勞投保薪');
        $sheet->setCellValue('AD3', '健投保薪');
        $sheet->setCellValue('AE3', '帳號');

        // $sheet->mergeCells('B37:F37');
        // $sheet->mergeCells('B38:F38');
        // $sheet->mergeCells('B39:F39');
        // $sheet->mergeCells('B40:F40');

   
        // $sheet->setCellValue('E1', '執   勤   簽   到   簿');  
        // $sheet->setCellValue('A2', '年份');

        
        $name = 'test';
        for ($i=4;$i<=10;$i++){
            $sheet->setCellValue("A$i", ($i-3));  
            $sheet->setCellValue("E$i", $name);  
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