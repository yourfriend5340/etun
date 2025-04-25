<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Customer;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Phpoffice\Phpspreadsheet\src\PhpSpreadsheet\Writer\Pdf\Mpdf;
use App\Services\AccessSalaryService;

class TableController extends Controller
{
    public function index(Request $request){
        $limit = $request->limit ?? 20;

        $employee = Employee::where('status','在職')->orderBy('id','desc')->get();
        $customer = Customer::orderBy('customer_id','asc')->get();
        
        return view('table',['employees'=>$employee,'customers'=>$customer]);
    }

    //離職
    public function resign(Request $request){
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        //設定預設格式
        $spreadsheet->getActiveSheet()->getPageSetup()
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        // Retrieve the current active worksheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(17);//預設寬度
        $sheet->getDefaultRowDimension()->setRowHeight(40);//預設高度
        $sheet->getColumnDimension('A')->setWidth(18);

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

        for ($i=2;$i<=9;$i++){
            $sheet->mergeCells("B$i:D$i");
        }
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        //$sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A1', '萬宇股份有限公司');  
        $sheet->setCellValue('D1', '離職單');   
        $sheet->setCellValue('A3', '姓名：');
        $sheet->setCellValue('A4', '身份字號：');
        $sheet->setCellValue('A5', '職稱：');
        $sheet->setCellValue('A6', '離職原因：');
        $sheet->setCellValue('A7', '離職日期：');
        $sheet->setCellValue('A9', '員工簽名：');    

        $DBname=DB::table('employees')->select('SSN','member_name')->where('id','=',$request->input('id'))->get()->first();
        $name=$DBname->member_name;
        $SSN=$DBname->SSN;

        $sheet->setCellValue('B3', $name);  
        $sheet->setCellValue('B4', $SSN);    

        $file_name = '離職單_'.$name.'_'.date('Y_m_d');
        // Write a new .xlsx file
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Save the new .xlsx file
        $writer->save('php://output');  
    }

    //請假
    public function leave(Request $request){
    // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        //設定預設格式
        $spreadsheet->getActiveSheet()->getPageSetup()
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        // Retrieve the current active worksheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(17);//預設寬度
        $sheet->getDefaultRowDimension()->setRowHeight(40);//預設高度
        $sheet->getColumnDimension('A')->setWidth(18);

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

        $sheet->mergeCells('A1:C1');
        for ($i=2;$i<=9;$i++){
            $sheet->mergeCells("B$i:D$i");
        }
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        //$sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A1', '萬宇股份有限公司');  
        $sheet->setCellValue('D1', '請假單');   
        $sheet->setCellValue('A3', '姓名：');
        $sheet->setCellValue('A4', '職稱：');
        $sheet->setCellValue('A5', '假別：');
        $sheet->setCellValue('A6', '請假事由：');
        $sheet->setCellValue('A7', '請假日期：');
        $sheet->setCellValue('A9', '員工簽名：');    

        $DBname=DB::table('employees')->select('SSN','member_name')->where('id','=',$request->input('id'))->get()->first();
        $name=$DBname->member_name;
        $SSN=$DBname->SSN;
        
        $sheet->setCellValue('B3', $name);   
        $sheet->setCellValue('B4', $SSN);   
        
        // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        // $drawing->setName('sign');
        // $drawing->setDescription('sign');
        // $drawing->setPath('/Users/hsi/sites/etun/storage/app/patrolPIC/帛漢/莊忠諺/sign.png'); // put your path and image here
        // $drawing->setCoordinates('B9');
        // $drawing->setHeight(300);
        // $drawing->setWidth(400);


                //$drawing->setOffsetX(110);
                //$drawing->setRotation(25);
                //$drawing->getShadow()->setVisible(true);
                //$drawing->getShadow()->setDirection(45);
        //$drawing->setWorksheet($spreadsheet->getActiveSheet());

        $file_name = '請假單_'.$name.'_'.date('Y_m_d');
        // Write a new .xlsx file
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Save the new .xlsx file
        $writer->save('php://output');  
    }

    //salary
    public function salary(Request $request){
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        //設定預設格式
        $spreadsheet->getActiveSheet()->getPageSetup()
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        // Retrieve the current active worksheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);//預設寬度
        $sheet->getDefaultRowDimension()->setRowHeight(25);//預設高度

        //$sheet->getPageMargins()->setTop(0.25);
        //$sheet->getPageMargins()->setRight(0.25);        
        //$sheet->getPageMargins()->setLeft(0.25);
        //$sheet->getPageMargins()->setBottom(0.25);

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
               //'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
         ]
      ];

              
        $sheet->getStyle("A1")->applyFromArray($styleCenterArray);
        $sheet->getStyle("A1:D16")->applyFromArray($styleArray);
        $sheet->getStyle('C3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A1:D1');
        for ($i=2;$i<=16;$i++){
            $str="A$i:B$i";
            $str2="C$i:D$i";
            //dd($str);
            $sheet->mergeCells($str);
            $sheet->mergeCells($str2);

        }

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(22);//设置字体加粗大小
        $sheet->getStyle('A2:D16')->getFont()->setBold(true)->setSize(16);//设置字体加粗大小
        $sheet->setCellValue('A1', '萬宇股份有限公司');        
        $sheet->setCellValue('A2', '薪資計算月份');
        $sheet->setCellValue('A3', '姓名');
        $sheet->setCellValue('A4', '薪資');
        $sheet->setCellValue('A5', '禮金');
        $sheet->setCellValue('A6', '津貼');
        $sheet->setCellValue('A8', '應領');
        $sheet->setCellValue('A9', '勞、健、團險');
        $sheet->setCellValue('A10', '補充保費');
        $sheet->setCellValue('A11', '行政費用');
        $sheet->setCellValue('A12', '預支');
        $sheet->setCellValue('A13', '救助金');
        $sheet->setCellValue('A14', '服裝');
        $sheet->setCellValue('A15', '實領');

        $lastmonth = date("Y年m月",mktime(0, 0, date("Y"), date("m")-1));
        $salary=DB::table('employees')->select('salary','member_name')->where('id','=',$request->input('name'))->get()->first();
        $DB_salary=$salary->salary;
        $name=$salary->member_name;
        //dd($salary->salary);
        $sheet->setCellValue('C2', $lastmonth);
        $sheet->setCellValue('C3', $name);
        $sheet->setCellValue('C4', $DB_salary);
        $sheet->setCellValue('C15',($DB_salary-1000) );




        $file_name = 'Salary_'.$name.'_'.date('Y_m_d');
        // Write a new .xlsx file
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Save the new .xlsx file
        $writer->save('php://output');  
    }
    //簽到單
    public function attendance(Request $request){
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        //避免亂碼參數
        $this->pdf = new Mpdf([
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'useSubstitutions' => true,
        ]);
        //設定預設格式
        $spreadsheet->getActiveSheet()->getPageSetup()
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        // Retrieve the current active worksheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(13.5);//預設寬度
        $sheet->getDefaultRowDimension()->setRowHeight(30);//預設高度
        $sheet->getRowDimension('1')->setRowHeight(32);
        for ($i=2;$i<=40;$i++){
            $sheet->getRowDimension("$i")->setRowHeight(20);
        }
        //自定欄寬
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15.5);
        $sheet->getColumnDimension('C')->setWidth(15.5);
        $sheet->getColumnDimension('D')->setWidth(20.5);
        $sheet->getColumnDimension('F')->setWidth(17.5);

        $sheet->getPageMargins()->setTop(0.5);
        //$sheet->getPageMargins()->setRight(0.25);        
        //$sheet->getPageMargins()->setLeft(0.25);
        $sheet->getPageMargins()->setBottom(0.25);

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

        //$sheet->getStyle("A1")->applyFromArray($styleCenterArray);
        $sheet->getStyle("A1:F40")->applyFromArray($styleCenterArray);
        $sheet->getStyle("A35:A40")->applyFromArray($styleCenterArray);
        $sheet->getStyle("B35:F40")->applyFromArray($styleArray);
        $sheet->getStyle('B35:B40')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A2:F34')->getFont()->setBold(true)->setSize(12);//设置字体加粗大小
        $sheet->getStyle('B35:B40')->getFont()->setBold(true)->setSize(10);//设置字体加粗大小
        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(20);//设置字体加粗大小
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('E1:F1');
        $sheet->mergeCells('A35:A40');
        $sheet->mergeCells('B35:F35');
        $sheet->mergeCells('B36:F36');
        $sheet->mergeCells('B37:F37');
        $sheet->mergeCells('B38:F38');
        $sheet->mergeCells('B39:F39');
        $sheet->mergeCells('B40:F40');

        //$attendance=DB::table('customers')->select('firstname')->where('customer_id','=',$request->input('customer_id'))->get()->first();
        $name=DB::table('employees')->where('id',$request->input('emp_id'))->pluck('member_name')->first();
        
        $sheet->setCellValue('A1', '萬宇股份有限公司');  
        $sheet->setCellValue('E1', '執   勤   簽   到   簿');  
        $sheet->setCellValue('A2', '年份');
        $sheet->setCellValue('C2', '月份');
        $sheet->setCellValue('E2', '姓名');  
        $sheet->setCellValue('A3', '日期');
        $sheet->setCellValue('B3', '簽到時間');
        $sheet->setCellValue('C3', '簽退時間');
        $sheet->setCellValue('D3', '應勤');
        $sheet->setCellValue('E3', '值勤哨點');
        $sheet->setCellValue('F3', '簽名欄');   
        $sheet->setCellValue('A35', '備註');
        $sheet->setCellValue('B35', '※休息(用餐時間統一於以下：一、12:00-13:00  二、17:00-18:00  三、23:00-00:00  四、05:00-06:00)');       
        $sheet->setCellValue('B36', '※請確實記載出勤情形至分鐘為止。');
        $sheet->setCellValue('B37', '※請確實穿著公司規定服裝，嚴禁喝酒、嚼檳榔、滑手機、打瞌睡、私自調班，違者依公司規定懲處');
        $sheet->setCellValue('B38', '※值班表請核對，如有錯誤，請立即告知，謝謝。此班表於次月初交回或傳回給公司，以利核薪作業');
        
        $sheet->setCellValue("F2", $name);  
        
        // for ($i=4;$i<=34;$i++){
        //     $sheet->setCellValue("A$i", ($i-3));  
        //     $sheet->setCellValue("E$i", $name);  
        // }
        $file_name = '簽到本_'.$name.'_'.date('Y_m_d');
       
       
        // Write a new .xlsx file
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Save the new .xlsx file
        $writer->save('php://output');  

        //檔案讀取器
        //$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        //$reader->setReadDataOnly(true);
        //$spreadsheet = $reader->load("test2.xlsx");

        //輸出pdf
        // $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        // $writer->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // $writer->setPreCalculateFormulas(false);
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'.$file_name.'.pdf"');
        
        // header('Cache-Control: max-age=0');
        // $writer->save('php://output');
    }

    //注入service
    /** @var AccessSalaryService */
    protected $accessSalaryService;

    /**
     * UserController constructor.
     * @param AccessSalaryService $emailService
     */
    public function __construct(AccessSalaryService $accessSalaryService)
    {
        $this->accessSalaryService = $accessSalaryService;
    }

    //薪資試算表匯出
    public function export_access_salary(Request $request){
        $this->accessSalaryService->export();
    }

    //試算薪資表匯入
    public function import_access_salary(Request $request){

    }
}
