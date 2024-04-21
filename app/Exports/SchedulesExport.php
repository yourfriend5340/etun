<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\Schedules;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


class SchedulesExport implements FromCollection
{
 /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        ## 1. Export all data
        // return Employees::all();

        ## 2. Export specific columns
        return Schedules::select('name','age')->get();

    }

    public function headings(): array
    {
        return [
          //'#',
          'name',
          'age'
        ];
    }

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
      $activeWorksheet->setCellValue('A5', '台南好市多');
      $activeWorksheet->setCellValue('A15', '交代事項');   
      $activeWorksheet->setCellValue('B5', '陳小明1');
      $activeWorksheet->setCellValue('B6', '陳小明2');
      $activeWorksheet->setCellValue('B7', '陳小明3');
      $activeWorksheet->setCellValue('B8', '陳小明4');
      $activeWorksheet->setCellValue('B9', '陳小明5');
      $activeWorksheet->setCellValue('B10', '陳小明6');
      $activeWorksheet->setCellValue('B11', '陳小明7');
      $activeWorksheet->setCellValue('B12', '陳小明8');
      $activeWorksheet->setCellValue('B13', '陳小明9');
      $activeWorksheet->setCellValue('B14', '陳小明10');
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

   /*
      $activeWorksheet->setCellValue('T18', 'A');
      $activeWorksheet->setCellValue('V18', '0000-000000');

      $activeWorksheet->setCellValue('T18', 'A');
      $activeWorksheet->setCellValue('V18', '0000-000000');

      $activeWorksheet->setCellValue('T19', 'A');
      $activeWorksheet->setCellValue('V19', '0000-000000');

      $activeWorksheet->setCellValue('T20', 'A');
      $activeWorksheet->setCellValue('V20', '0000-000000');

      $activeWorksheet->setCellValue('T21', 'A');
      $activeWorksheet->setCellValue('V21', '0000-000000');

      $activeWorksheet->setCellValue('T22', 'A');
      $activeWorksheet->setCellValue('V22', '0000-000000');

      $activeWorksheet->setCellValue('T23', 'A');
      $activeWorksheet->setCellValue('V23', '0000-000000');
   */

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

      //echo everyday
      $column= array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
               'AA','AB','AC','AD','AE','AF','AG','AH');
  
      $weekarray=array('日','一','二','三','四','五','六');
      //$day=$weekarray[date("w",strtotime("2023-4-1"))];
      //dd($column[1]);

      $WhichMonth = $request->input('exportbymonth');
      $years=substr("$WhichMonth", 0,4); 
      $months=substr("$WhichMonth", -2); 
      //dd($years,$months);

      $activeWorksheet->setCellValue('K2', "$years");
      $activeWorksheet->setCellValue('N2', "$months");
      $day=$weekarray[date("w",strtotime("$WhichMonth".'-1'))];

      $offset=implode([date("w",strtotime("$WhichMonth".'-01'))]);
      //dd($day,gettype($offset),$offset);
      $days_per_month=implode([date("t",strtotime("$WhichMonth"))]);
      //dd($days_per_month);
      
      for ($i=0;$i<=$days_per_month-1;$i++){
         $activeWorksheet->setCellValue("$column[$i]".'3', $i+1);

         $activeWorksheet->setCellValue("$column[$i]".'4', "$weekarray[$offset]");
         $offset=($offset+1)%7;
      };

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

      
      

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
      header('Cache-Control: max-age=0');

      $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
      $writer->save('php://output');
   }
}
