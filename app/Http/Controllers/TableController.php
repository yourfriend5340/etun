<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\extra_schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Phpoffice\Phpspreadsheet\src\PhpSpreadsheet\Writer\Pdf\Mpdf;
use App\Services\AccessSalaryService;
use App\Services\AccessTableService;
use App\Services\ConvertPdfService;

class TableController extends Controller
{
    public function index(Request $request){

        $now = date('Y-m-d');
        $lastYear = date('Y-m-d',strtotime('-1 year',strtotime($now)));
        //$limit = $request->limit ?? 20;
        //dd($now,$lastYear,$limit);
        
        $employee = Employee::where('status','在職')->orderBy('id','desc')->get();
        $customer = Customer::orderBy('customer_id','asc')->get();
        $leave = DB::table('twotime_table')
            ->join('employees','employees.member_sn','twotime_table.empid')
            ->where([
                ['twotime_table.status','Y'],
                ['twotime_table.type','請假']
            ])
            ->whereBetween('twotime_table.start',array($lastYear,$now))
            ->orderby('twotime_table.id')
            ->get(array('twotime_table.*','employees.member_name'));

        return view('table',['employees'=>$employee,'customers'=>$customer,'leaves'=>$leave]);
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

        if($request->input('inputName') == null)
        {
            $DBname=DB::table('employees')->select('SSN','member_name')->where('id','=',$request->input('id'))->get()->first();
            $name=$DBname->member_name;
            $SSN=$DBname->SSN;
        }
        else
        {
            $name=$request->input('inputName');
            $count = DB::table('employees')->where('member_name',$name)->count();
            
            if($count == 0){
                return back()->with('danger','查無此人!!');
            }
            $DBname=DB::table('employees')->select('SSN','member_name')->where('member_name','=',$name)->get()->first();
            $SSN=$DBname->SSN;
        }

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

        $query = DB::table('twotime_table')->where('id',intval($request->id))->first();
        $date = date('Y-m-d',strtotime($query->start));
        $path = 'public'.$query->filePath;

        $fileName = $query->empid.'_'.$date.'_請假單.pdf';
        $mimeType = Storage::mimeType($path);
        $headers = [['Content-Type' => $mimeType]];
        
        return Storage::download($path, $fileName, $headers);
    
        /*
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

        if($request->input('inputName') == null)
        {
            $DBname=DB::table('employees')->select('SSN','member_name')->where('id','=',$request->input('id'))->get()->first();
            $name=$DBname->member_name;
            $SSN=$DBname->SSN;
        }
        else
        {
            $name=$request->input('inputName');
            $count = DB::table('employees')->where('member_name',$name)->count();
            
            if($count == 0){
                return back()->with('danger','查無此人!!');
            }
            $DBname=DB::table('employees')->select('SSN','member_name')->where('member_name','=',$name)->get()->first();
            $SSN=$DBname->SSN;
        }
        
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
    */
    }

    //salary
    public function salary(Request $request){

        $inputData = array_filter(explode(',',$request->input('exlist')));
        $sheetCount = count($inputData)/2;
        $offset = 0;
        $sheeti=0;

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        while($sheeti<$sheetCount)
        {
            $name = $inputData[$offset];
            $month = $inputData[$offset+1];

            //設定預設格式
            if($sheeti!=0){
                $spreadsheet->createSheet();
            }

            $spreadsheet->setActiveSheetIndex($sheeti);
            $sheet = $spreadsheet->getActiveSheet($sheeti);
            $sheet->getDefaultColumnDimension()->setWidth(20);//預設寬度
            $sheet->getDefaultRowDimension()->setRowHeight(25);//預設高度
            $sheet->SetTitle($month.$name);
            $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(22);//设置字体加粗大小
            $sheet->getStyle('A2:D16')->getFont()->setBold(true)->setSize(16);//设置字体加粗大小

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

            $query = DB::table('employees')->where('member_name',$name)->first();
            $queryId = $query->member_sn;
            $organizeName = $query->organize;
            $queryMonth = date('m',strtotime($month));
            $queryYear = date('Y',strtotime($month));
            $TotalData = $this->accessSalaryService->countTotalTime($queryYear,$queryMonth,$queryId);

            $querySalaryItem = DB::table('salary_items')->where([
                ['month',$month],
                ['empid',$queryId],
            ])->orderby('mark','asc')->get();

            $sheet->setCellValue('A1', $organizeName.'      薪資明細表');        
            $sheet->setCellValue('A2', '薪資計算月份');
            $sheet->setCellValue('A3', '姓名');
            $sheet->setCellValue('A4', '應領薪資');
            $sheet->setCellValue('C2', $month);
            $sheet->setCellValue('C3', $name);
            $sheet->setCellValue('C4',$TotalData[1]);
            
            $sheet->setCellValue('A5','加項');
            $startIndex = 6;
            $addCountIndex = $startIndex;
            for($i=0;$i<count($querySalaryItem);$i++)
            {
                if($querySalaryItem[$i]->mark == "add")
                {
                    $sheet->setCellValue('A'.$startIndex, '     '.$querySalaryItem[$i]->item);
                    $sheet->setCellValue('C'.$startIndex,$querySalaryItem[$i]->amount);
                    $startIndex++;
                }
            }

            if($startIndex == 6)
            {$startIndex++;}
            $addCountIndexEnd = $startIndex-1;

            $sheet->setCellValue('A'.$startIndex, '減項');
            $startIndex++;//8
            $subCountIndex = $startIndex;//8

            for($i=0;$i<count($querySalaryItem);$i++)
            {
                if($querySalaryItem[$i]->mark == "sub")
                {
                    $sheet->setCellValue('A'.$startIndex, '     '.$querySalaryItem[$i]->item);
                    $sheet->setCellValue('C'.$startIndex,$querySalaryItem[$i]->amount);
                    $startIndex++;
                }
            }

            if($subCountIndex == $startIndex)
            {
                $startIndex++;
            }
            $subCountIndexEnd = $startIndex-1;

            $sum = '=C4+SUM(C'.$addCountIndex.':C'.$addCountIndexEnd.')-SUM(C'.$subCountIndex.':C'.$subCountIndexEnd.')';

            $sheet->setCellValue('A'.$startIndex, '實際領取');
            $sheet->setCellValue('C'.$startIndex, $sum);
            $sheet->getStyle("A1:D$startIndex")->applyFromArray($styleArray);
            $sheet->getStyle("A1:D3")->applyFromArray($styleCenterArray);
            $sheet->getStyle('C3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('A1:D1');
            for ($j=2;$j<=$startIndex;$j++){
                $str="A$j:B$j";
                $str2="C$j:D$j";

                $sheet->mergeCells($str);
                $sheet->mergeCells($str2);
            }

            $sheeti++;
            $offset = $offset + 2;
        }

        // Write a new .xlsx file
        $file_name = 'Salary_'.date('Y_m_d');
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
        if($request->input('inputName') == null)
        {
            $name=DB::table('employees')->where('id',$request->input('emp_id'))->pluck('member_name')->first();
        }
        else
        {
            $name=$request->input('inputName');
            $count = DB::table('employees')->where('member_name',$name)->count();
            
            if($count == 0){
                return back()->with('danger','查無此人!!');
            }
        }
        
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
        exit;
        //檔案讀取器
        //$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        //$reader->setReadDataOnly(true);
        //$spreadsheet = $reader->load("test2.xlsx");

        //輸出pdf
        // $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
        // $writer->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        // $writer->setPreCalculateFormulas(false);
        
        //避免亂碼參數
        // $this->pdf = new Mpdf([
        //     'autoScriptToLang' => true,
        //     'autoLangToFont'   => true,
        //     'useSubstitutions' => true,
        // ]);
        //$autoScriptToLang = true;
        //$autoLangToFont   = true;
        //$useSubstitutions = true;
        
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'.$file_name.'.pdf"');
        
        // header('Cache-Control: max-age=0');
        // $writer->save('php://output');
    }

    public function request($id){

        $request = DB::table('twotime_table')
                    ->join('employees','employees.member_sn','twotime_table.empid')
                    ->where('twotime_table.id',$id)
                    ->orderby('twotime_table.id')
                    ->first((array(('twotime_table.*'),'employees.member_name')));

        $todaySchedule =[];
        $yesterdaySchedule =[];
        $empid = $request->empid;
        $startTime = $request->start;
        $endTime = $request->end;

        $year = date('Y',strtotime($startTime));
        $month = date('m',strtotime($startTime));
        $day = intval(date('d',strtotime($startTime)));
        $queryDay = "day$day";
        
        $requestSchedule = DB::table('schedules')
            ->join('employees','schedules.employee_id','employees.member_sn')
            ->join('customers','schedules.customer_id','customers.customer_id')
            ->where([
                ['year',$year],
                ['month',$month],
                ['employee_id',$empid],
                [$queryDay,'!=',""]
            ])->get()->toarray();

        for($i=0;$i<count($requestSchedule);$i++){
            $class = $requestSchedule[$i]->$queryDay;
            $cus = $requestSchedule[$i]->firstname;

            
            for($j=0;$j<strlen($class);$j++)
            {
                $subClass = substr($class,$j,1);

                $classEnd = $subClass."_end";
                $start = $requestSchedule[$i]->$subClass;
                $end = $requestSchedule[$i]->$classEnd;

                if(strtotime($end) < strtotime($start))
                {
                    $endDay = $day + 1;
                }
                else{
                    $endDay = $day;
                }

                if($day < 10)
                {
                    $day = '0'.$day;
                }
                if($endDay < 10)
                {
                    $endDay = '0'.$endDay;
                }

                $todaySchedule[$i][$j]['customer'] = $cus;
                $todaySchedule[$i][$j]['class'] = $subClass;
                $todaySchedule[$i][$j]['start'] = $year.'-'.$month.'-'.$day.' '.$start;
                $todaySchedule[$i][$j]['end'] = $year.'-'.$month.'-'.$endDay.' '.$end;
            }
        }
        $yesterdayYear = date('Y',strtotime("-1 day",strtotime($startTime)));
        $yesterdayDate = intval(date('d',strtotime("-1 day",strtotime($startTime))));
        $yesterdayMonth = date('m',strtotime("-1 day",strtotime($startTime)));
        $queryYesterdayDay = "day$yesterdayDate";

        $requestSchedule = DB::table('schedules')
            ->join('employees','schedules.employee_id','employees.member_sn')
            ->join('customers','schedules.customer_id','customers.customer_id')
            ->where([
                ['year',$yesterdayYear],
                ['month',$yesterdayMonth],
                ['employee_id',$empid],
                [$queryYesterdayDay,'!=',""]
            ])->get()->toarray();

        //巢狀array，第一層是幾個客戶
        for($i=0;$i<count($requestSchedule);$i++){
            $class = $requestSchedule[$i]->$queryYesterdayDay;
            $cus = $requestSchedule[$i]->firstname;

            //該客戶幾個班
            for($j=0;$j<strlen($class);$j++)
            {
                $subClass = substr($class,$j,1);

                $classEnd = $subClass."_end";

                $start = $requestSchedule[$i]->$subClass;
                $end = $requestSchedule[$i]->$classEnd;

                if(strtotime($end) < strtotime($start))
                {
                    $endDay = $yesterdayDate + 1;
                }
                else{
                    $endDay = $yesterdayDate;
                }

                if($yesterdayDate < 10)
                {
                    $yesterdayDate = '0'.$yesterdayDate;
                }
                if($endDay < 10)
                {
                    $endDay = '0'.$endDay;
                }

                $yesterdaySchedule[$i][$j]['customer'] = $cus;
                $yesterdaySchedule[$i][$j]['class'] = $subClass;
                $yesterdaySchedule[$i][$j]['start'] = $yesterdayYear.'-'.$yesterdayMonth.'-'.$yesterdayDate.' '.$start;
                $yesterdaySchedule[$i][$j]['end'] = $yesterdayYear.'-'.$yesterdayMonth.'-'.$endDay.' '.$end;
            }
        }

        $empList = DB::table('employees')
        ->where([
            ['status','在職'],
            ['member_sn','!=',$empid]
        ])->get();

        //dd($request,$todaySchedule,$yesterdaySchedule,$empList);
        return view("edit_table",["results"=>$request,"today"=>$todaySchedule,"yesterday"=>$yesterdaySchedule,"empList"=>$empList]);
    }


    public function updateStatus($id,$status,$emp,ConvertPdfService $convertPdfService){
       
        $signPath = DB::table('twotime_table')->where('id',$id)->get()->pluck('filePath'); 
        $signPath = storage_path('app').'/'.$signPath[0];

        if($status == 'Y')
        {
            $result = $convertPdfService->convertTable($id);
            DB::table('twotime_table')->where('id',$id)->update(['status'=>$status,'filePath'=>$result]);  

            //請假單處理完畢，以下處理代理人額班表班新增
            $request = DB::table('twotime_table')->where('id',$id)->first();
            $leaveMan = $request->empid;
            $start = $request->start;
            $end = $request->end;

            $request2 = extra_schedule::where([
                ['emp_id',$emp],
                ['start',$start],
                ['end',$end],
                ['leave_member',$leaveMan],
            ])->first();

            //dd($id,$status,$emp,$request,$request2,$leaveMan,$start,$end);
            if($request2 === null )
            {
                extra_schedule::create([
                    'emp_id'=>$emp,
                    'start'=>$start,
                    'end'=>$end,
                    'leave_member'=>$leaveMan,
                ]);
            }
        }
        elseif($status == 'N'){
            $result = "";
            DB::table('twotime_table')->where('id',$id)->update(['status'=>$status,'filePath'=>$result]);  
        }
        if(is_file($signPath)){
            unlink($signPath);
        }


       return redirect()->route("home");
    }

    public function overview(Request $request){
        return view("show_table");
    }

    public function requestoverview(Request $request){
        $name = $request->name;
        $start = $request->start_time;
        $end = $request->end_time;

        if($name == '請假')
        {
            $query = DB::table('twotime_table')
            ->join('employees','twotime_table.empid','employees.member_sn')
            ->select('twotime_table.*','employees.member_name')
            ->where([
                ['type',$name],
                ['start','>=',$start],
                ['end','<=',$end],
            ])
            ->orderby('start','asc')
            ->paginate(20);
        }
        elseif($name == '離職'){
            $query = DB::table('twotime_table')
            ->join('employees','twotime_table.empid','employees.member_sn')
            ->select('twotime_table.*','employees.member_name')
            ->where('type',$name)
            ->whereBetween('start',[$start,$end])
            ->orderby('start','asc')
            ->paginate(20);
        }
         return view("show_table",["results"=>$query]);
    }

    public function tableresultAPI(Request $request){
        $employeeID = $request->EmployeeID;
        $month = date('Y-m').'-01';
        $nextmonth = date('Y-m-t',strtotime("+1 month"));
        $query = DB::table('twotime_table')
            ->select('type','start','end','reason','status')
            ->where('empid',$employeeID)
            ->whereBetween('start',array($month,$nextmonth))
            ->get()
            ->map(function($item){
                if($item->type == '離職'){
                    $item->start = date('Y-m-d',strtotime($item->start));
                }
                
                if($item->status == 'Y'){
                    $item->status =  '通過';
                }
                elseif($item->status == 'N'){
                    $item->status = '駁回';
                }
                else{
                    $item->status = '尚未審核';
                }

                if($item->end == null){
                    $item->end = '-';
                }

                return $item;
            });

            if(count($query) == 0){
                $query = '無任何紀錄';
            }
            return response(['result'=>$query]);
    }
    
    //構造注入service
    /** @var AccessSalaryService */
    protected $accessSalaryService;
    /**
     * @param AccessSalaryService 
    */
    public function __construct(AccessSalaryService $accessSalaryService)
    {
        $this->accessSalaryService = $accessSalaryService;
    }

    //薪資試算表匯出
    public function export_access_salary(Request $request){
        $this->accessSalaryService->export($request);
    }

    //試算薪資表匯入
    public function import_access_salary(Request $request){
        $result = $this->accessSalaryService->import($request);

        if($result !=1)
        {
            return back()->with('danger',$result);
        }
        else{
            return back()->with('success','己成功匯入資料！');
        }
    }

    //方法注入service
    /** @var AccessTableService */
    protected $accessTableService;
    /**
     * @param AccessTableService $emailService
     */
    //API處理請假單
    public function leaveAPI(AccessTableService $accessTableService,Request $request)
     {
        $result = $accessTableService->leaveStore($request);
        return response()->json(['message' => $result]);
    }

    //API處理離職單
    public function resignAPI(AccessTableService $accessTableService,Request $request)
    {
        $result = $accessTableService->resignStore($request);
        return response()->json(['message' => $result]);
    }

    public function export_extra_schedule(AccessTableService $accessTableService,Request $request)
    {
        $result = $accessTableService->export_extra_schedule($request->exportbymonth);
    }


}
