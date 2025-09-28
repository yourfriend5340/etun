<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\extra_schedule;
use App\Models\Punch;
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

        $now = date('Y-m-t');
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
            ->whereBetween('twotime_table.created_at',array($lastYear,$now))
            ->orderby('twotime_table.start')
            ->get(array('twotime_table.*','employees.member_name'));

        return view('table',['employees'=>$employee,'customers'=>$customer,'leaves'=>$leave]);
    }

    public function showPunch(Request $request){
        $emp=DB::table('employees')->select('member_sn','member_name')->get();
        return view("show_punch_record",["user_info"=>$emp]);
    }
    
    //離職
    public function resign(Request $request){
        $inputName = $request->inputName;

        if($inputName === null)
        {
            $empId = $request->id;

        }
        else{
            $empId = DB::table('employees')->where('member_name',$inputName)->first();
            
            if(!isset($empId->id))
            {
                return back()->with('danger',$inputName.' 查無此人，請檢查有無錯字');
            }
            else{
                $empId = $empId->id;
            }
        }

        $query = DB::table('employees')->where('id',$empId)->first();
        $member_sn = $query->member_sn;
        $member_name = $query->member_name;

        $query = DB::table('twotime_table')->where([
                ['empid',$member_sn],
                ['type','離職'],
                ['status','Y']
            ])
            ->orderby('id','desc')
            ->first();
        if($query != null)
        {
            $filePath = $query->filePath;
        }
        else{
            return back()->with('danger',$member_name.' 查無審核通過的離職記錄');
        }

        $date = date('Y-m-d',strtotime($query->start));
        $path = 'public'.$query->filePath;

        $fileName = $query->empid.'_'.$date.'_離職單.pdf';
        $mimeType = Storage::mimeType($path);
        $headers = [['Content-Type' => $mimeType]];
        
        return Storage::download($path, $fileName, $headers);
        
        
        // // Create a new Spreadsheet object
        // $spreadsheet = new Spreadsheet();
        
        // //設定預設格式
        // $spreadsheet->getActiveSheet()->getPageSetup()
        // ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        // // Retrieve the current active worksheet
        // $sheet = $spreadsheet->getActiveSheet();
        // $sheet->getDefaultColumnDimension()->setWidth(17);//預設寬度
        // $sheet->getDefaultRowDimension()->setRowHeight(40);//預設高度
        // $sheet->getColumnDimension('A')->setWidth(18);

        // $sheet->getPageMargins()->setTop(0.5);
        // $sheet->getPageMargins()->setRight(1);        
        // $sheet->getPageMargins()->setLeft(1);
        // $sheet->getPageMargins()->setBottom(0.5);

        // $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(26);//设置字体加粗大小
        // $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(26);//设置字体加粗大小
        // for ($i=2;$i<=9;$i++){
        //     $sheet->getStyle("A$i")->getFont()->setBold(true)->setSize(18);
        //     $sheet->getStyle("B$i")->getFont()->setBold(true)->setSize(18);
        // }

        // for ($i=2;$i<=9;$i++){
        //     $sheet->mergeCells("B$i:D$i");
        // }
        // $sheet->mergeCells('A1:C1');
        // $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // //$sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // $sheet->setCellValue('A1', '萬宇股份有限公司');  
        // $sheet->setCellValue('D1', '離職單');   
        // $sheet->setCellValue('A3', '姓名：');
        // $sheet->setCellValue('A4', '身份字號：');
        // $sheet->setCellValue('A5', '職稱：');
        // $sheet->setCellValue('A6', '離職原因：');
        // $sheet->setCellValue('A7', '離職日期：');
        // $sheet->setCellValue('A9', '員工簽名：');    

        // if($request->input('inputName') == null)
        // {
        //     $DBname=DB::table('employees')->select('SSN','member_name')->where('id','=',$request->input('id'))->get()->first();
        //     $name=$DBname->member_name;
        //     $SSN=$DBname->SSN;
        // }
        // else
        // {
        //     $name=$request->input('inputName');
        //     $count = DB::table('employees')->where('member_name',$name)->count();
            
        //     if($count == 0){
        //         return back()->with('danger','查無此人!!');
        //     }
        //     $DBname=DB::table('employees')->select('SSN','member_name')->where('member_name','=',$name)->get()->first();
        //     $SSN=$DBname->SSN;
        // }

        // $sheet->setCellValue('B3', $name);  
        // $sheet->setCellValue('B4', $SSN);    

        // $file_name = '離職單_'.$name.'_'.date('Y_m_d');
        // // Write a new .xlsx file
        // $writer = new Xlsx($spreadsheet);


        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        // header('Cache-Control: max-age=0');

        // // Save the new .xlsx file
        // $writer->save('php://output');  
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

        if($request->input('exlist') == null){
            return back()->with('danger','請輸入資料');
        }
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

        if($request->signlist === null){
            return back()->with('danger','請輸入資料');
        }
        $inputArr =explode(',',$request->signlist);
        array_pop($inputArr);//因最後有一個逗號，會多一個元素
        $worksheetCount = intval(count($inputArr) / 2)-1;//因為分頁是從零起算，再減1
        $arrIndex = 0;

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
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        for($i=0;$i<=$worksheetCount;$i++){
            if($i >0)
            {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndex($i);
            
            //設定預設格式
            $spreadsheet->getActiveSheet($i)->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            
            // Retrieve the current active worksheet
            $sheet = $spreadsheet->getActiveSheet($i);
            $sheet->getDefaultColumnDimension()->setWidth(13.5);//預設寬度
            $sheet->getDefaultRowDimension()->setRowHeight(30);//預設高度
            $sheet->getRowDimension('1')->setRowHeight(32);
            for ($j=2;$j<=40;$j++){
                $sheet->getRowDimension("$j")->setRowHeight(20);
            }
            //自定欄寬
            $sheet->getColumnDimension('A')->setWidth(7);
            $sheet->getColumnDimension('B')->setWidth(15.5);
            $sheet->getColumnDimension('C')->setWidth(15.5);
            $sheet->getColumnDimension('D')->setWidth(12.5);
            $sheet->getColumnDimension('F')->setWidth(17.5);

            $sheet->getPageMargins()->setTop(0.5);
            //$sheet->getPageMargins()->setRight(0.25);        
            //$sheet->getPageMargins()->setLeft(0.25);
            $sheet->getPageMargins()->setBottom(0.25);

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

            
            $name=$inputArr[$arrIndex];
            if ($name != "")
            {
                $query = DB::table('employees')->where('member_name',$name)->first();
            }
            if($query == null){
                return back()->with('danger','你輸入的人名：'.$name.' 查無此人!!');
            }
            
            $year = date('Y',strtotime($inputArr[$arrIndex+1]));
            $month = date('m',strtotime($inputArr[$arrIndex+1]));
            $sheet->setTitle($name.$inputArr[$arrIndex+1]);
            $sheet->setCellValue('A1', $query->organize);  
            $sheet->setCellValue('E1', '執   勤   簽   到   簿');  
            $sheet->setCellValue('A2', '年份');
            $sheet->setCellValue('B2', $year);
            $sheet->setCellValue('C2', '月份');
            $sheet->setCellValue('D2', $month);
            $sheet->setCellValue('E2', '姓名');  
            $sheet->setCellValue("F2", $name);  
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


            $arrIndex = $arrIndex + 2;
        }
        $file_name = '簽到本_'.date('Y_m_d');
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
            $cus_id = $requestSchedule[$i]->customer_id;
            
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
                $todaySchedule[$i][$j]['customer_id'] = $cus_id;
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
            $cus_id = $requestSchedule[$i]->customer_id;
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
                $yesterdaySchedule[$i][$j]['customer_id'] = $cus_id;
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


    public function updateStatus($id,$status,$emp,$cus_id,ConvertPdfService $convertPdfService){
        $signPath = DB::table('twotime_table')->where('id',$id)->get()->pluck('filePath'); 
        $signPath = storage_path('app').'/'.$signPath[0];
        //$applicantId = DB::table('twotime_table')->where('id',$id)->first()->empid;

        if($emp != "NULL")//指請假單
        {
            if($status == 'Y')
            {
                //處理代理人額班表班新增
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
                        'cus_id'=>$cus_id,
                    ]);
                }
                $queryMaxID = extra_schedule::where('emp_id',$emp)->max('id');

                $result = $convertPdfService->convertTable($id,'請假',$queryMaxID);
                DB::table('twotime_table')->where('id',$id)->update(['status'=>$status,'extra_id'=>$queryMaxID,'filePath'=>$result]);  
            }
            elseif($status == 'N'){
                $result = "";
                DB::table('twotime_table')->where('id',$id)->update(['status'=>$status,'filePath'=>$result]);  
            }


        }
        else//指離職單
        {
            if($status == 'Y')
            {
                $queryMaxID = extra_schedule::where('emp_id',$emp)->max('id');
                $result = $convertPdfService->convertTable($id,'離職',$queryMaxID);
                DB::table('twotime_table')->where('id',$id)->update(['status'=>$status,'filePath'=>$result]);  
            }
            elseif($status == 'N'){
                $result = "";
                DB::table('twotime_table')->where('id',$id)->update(['status'=>$status,'filePath'=>$result]);  
            }
        }

        if(is_file($signPath)){
            unlink($signPath);
        }

        $query = DB::table('twotime_table')
            ->leftjoin('employees as e1','twotime_table.empid','e1.member_sn')
            ->leftjoin('extra_schedules','twotime_table.extra_id','extra_schedules.id')
            ->leftjoin('employees as e2','extra_schedules.emp_id','e2.member_sn')
            ->select('twotime_table.*','e1.member_name','e2.member_name AS coverMan')
            //->where([
            //    ['empid',$applicantId],
            //])
            ->orderby('updated_at','desc')
            ->paginate(20);

         return view("show_table",["results"=>$query]);

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
            ->leftjoin('employees as e1','twotime_table.empid','e1.member_sn')
            ->leftjoin('extra_schedules','twotime_table.extra_id','extra_schedules.id')
            ->leftjoin('employees as e2','extra_schedules.emp_id','e2.member_sn')
            ->select('twotime_table.*','e1.member_name','e2.member_name AS coverMan')
            ->where([
                ['type',$name],
                ['twotime_table.start','>=',$start],
                ['twotime_table.end','<=',$end],
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

    public function download($id){
        $time = date('Y_m_d_H_i');
        $queryPath = DB::table('twotime_table')->where('id',$id)->first()->filePath;
        
        $filePath = Storage::path('public'.$queryPath);

        $fileName ='download_'.$time.".pdf";
        $mimeType = Storage::mimeType('public'.$queryPath);
        $headers = [
            ['Content-Type' => $mimeType],
            ['Content-Disposition: attachment;filename="'.$fileName]
        ];

        return response()->download($filePath,$fileName,$headers);
    }

    public function tableresultAPI(Request $request){
        $employeeID = $request->EmployeeID;
        $month = date('Y-m').'-01';
        $nextmonth = date('Y-m-t',strtotime("+1 month"));
        $query = DB::table('twotime_table')
            ->select('type','start','end','reason','status')
            ->where('empid',$employeeID)
            ->whereBetween('created_at',array($month,$nextmonth))
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
    

    public function additional ($id){
        $query0 = DB::table('punch_record')
            ->join('employees','punch_record.employee_id','employees.member_sn')
            ->where('punch_record.id',$id)
            ->first(array('punch_record.*','employees.member_sn','employees.member_name'));

        $addTime = $query0->punchTime;
        $addEmp = $query0->employee_id;

        $queryYear = date('Y',strtotime($addTime));
        $queryMonth = intval(date('m',strtotime($addTime)));
        $queryDay = intval(date('d',strtotime($addTime)));
        
        $data = DB::table('punch_record')
            ->join('employees','punch_record.employee_id','employees.member_sn')
            ->where([
                ['employee_id',$addEmp],
                ['year',$queryYear],
                ['month',$queryMonth],
                ['additional',null],
            ])
            ->where(function ($query) use ($queryDay){
                $query->where('day',$queryDay)
                    ->orwhere('day',$queryDay-1);
            })
            ->orderby('punchTime','desc')
            ->get(array('punch_record.*','employees.member_sn','employees.member_name'));
        
        $temp = 'day'.$queryDay;
        $temp2 = 'day'.$queryDay-1;
        $schedule = DB::table('schedules')
            ->join('employees','schedules.employee_id','employees.member_sn')
            ->join('customers','schedules.customer_id','customers.customer_id')
            ->where([
                ['employee_id',$addEmp],
                ['year',$queryYear],
                ['month',$queryMonth]
            ])
            ->where(function ($query) use ($temp,$temp2){
                $query->where($temp,'!=','')
                    ->orwhere($temp2,'!=','');
            })
            ->get(array('customers.firstname','employees.member_name','schedules.id','schedules.year','schedules.month',"schedules.$temp","schedules.$temp2",
                        'schedules.A','schedules.A_end','schedules.B','schedules.B_end','schedules.C','schedules.C_end','schedules.D','schedules.D_end',
                        'schedules.E','schedules.E_end','schedules.F','schedules.F_end','schedules.G','schedules.G_end','schedules.H','schedules.H_end',
                        'schedules.I','schedules.I_end','schedules.J','schedules.J_end'
            ));

        $tempStr = "";  
        $tempArr = ['A','B','C','D','E','F','G','H','I','J'];         
 
        foreach($schedule as $s)
        {
            if($s->$temp != "")
            {
                $s->applyDate = $s->$temp;
                unset($s->$temp);
            }
            if($s->$temp2 != "")
            {
                $s->applyYesterday = $s->$temp2;
                unset($s->$temp2);
            }

            $tempArr2=[];  
            for($i=0;$i<=9;$i++){
                $class = $tempArr[$i];
                $class2 = $class.'_end';

                if($s->$class == ""){
                    unset($s->$class);
                    unset($s->$class2);
                }
                else{
                    $tempStr = $class.":".$s->$class."-".$s->$class2;
                    array_push($tempArr2,$tempStr);
                    //unset($s->$class);
                    //unset($s->$class2);
                }
            }
            $tempArr2 = array_unique($tempArr2);
            for($j=0;$j<count($tempArr2);$j+=2){
                $s->timeDefind = $tempArr2[$j].','.$tempArr2[$j+1];
                array_shift($tempArr2);
                array_shift($tempArr2);
            }

        }

        return view("edit_additional",["results"=>$query0,"history"=>$data,"schedules"=>$schedule]);
    }

    public function updateAdditional($id,$status){
        $punch = DB::table('punch_record')->where('id',$id)->update(['additional'=>$status]);

        $empId = DB::table('punch_record')->where('id',$id)->first()->employee_id;
        
        $query = DB::table('punch_record')
            ->join('employees','employees.member_sn','punch_record.employee_id')
            ->where([
                ['employee_id',$empId],
            ])
            ->where(function ($query){
                $query->where('additional','Y')
                    ->orwhere('additional',null);
            })
            ->orderby('punchTime','desc')
            ->get(array('punch_record.*','employees.member_sn','employees.member_name'));

        $emp=DB::table('employees')->select('member_sn','member_name')->get();
        return view('show_punch_record',['punch_records'=>$query,'user_info'=>$emp]);
    }

    public function requestPunch($name,$start,$end){

        $empId = DB::table('employees')->where('member_name',$name)->first();

        if(isset($empId->member_sn))
        {
            $empId = $empId->member_sn;
        }
        else{
            return back()->with('danger','查無此人！');
        }

        $query = DB::table('punch_record')
            ->join('employees','employees.member_sn','punch_record.employee_id')
            ->where([
                ['employee_id',$empId],
            ])
            ->where(function ($query){
                $query->where('additional','Y')
                    ->orwhere('additional',null);
            })
            ->whereBetween('punchTime',array($start,$end))
            ->orderby('punchTime','desc')
            ->get(array('punch_record.*','employees.member_sn','employees.member_name'));

        $emp=DB::table('employees')->select('member_sn','member_name')->get();

        return view('show_punch_record',['punch_records'=>$query,'user_info'=>$emp]);
    }

    public function additionalAPI(Request $request){

        $empId = $request->EmployeeID;  
        $type = ucwords($request->type);
        $addTime = date('Y-m-d H:i',strtotime($request->time));
        $queryYear = date('Y',strtotime($addTime));
        $queryMonth = intval(date('m',strtotime($addTime)));
        $queryDay = intval(date('d',strtotime($addTime)));
        $result = 0;

        $query = DB::table('schedules')
            ->where([
                ['employee_id',$empId],
                ['year',$queryYear],
                ['month',$queryMonth],
            ])
            ->orderby('customer_id','asc')
            ->get();

        //check申請時間，有無在當日的排班表中
        $result = TableController::checkTime($query,$addTime,$queryDay);
            
        //承前行，check申請時間，因可能是夜班，檢查申請日前一天，時間有無在該班區間
        if($result == 0){
            $result = TableController::checkTime($query,$addTime,($queryDay-1));
        }

        //若結果仍為0，輸出錯誤
        if($result == 0){
            return response('failure, 您的申請時間,沒有在排班區間內(上班時間前的二十分鐘，或是下班後的十分鐘),請確認時間');
        }
        
        $data = [
            'employee_id'=>$empId,
            'year'=>$queryYear,
            'month'=>$queryMonth,
            'day'=>$queryDay,
            'type'=>strtoupper($type),
            'additional'=>'A',
            'punchTime'=>$addTime
        ];

        $check = DB::table('punch_record')->where($data)->count();
        
        if($check == 0){
            Punch::create($data);
        }
        else{
            return response('failure,同樣的資料你已申請過');
        }

        return response('success,已上傳申請');
    }

    function checkTime($queryC,$aTime,$qDay)
    {
        $qYear = date('Y',strtotime($aTime));
        $qMonth = intval(date('m',strtotime($aTime)));

        foreach ($queryC as $q)
        {
            $temp = 'day'.$qDay;
            $class = $q->$temp;
            for($i=0;$i<strlen($class);$i++)//讀當天n個班
            {
                $tempClass = substr($class,$i,1);
                $tempClass_end = $tempClass.'_end';
                $startTime = $q->$tempClass;
                $endTime = $q->$tempClass_end;

                if(strtotime($endTime) < strtotime($startTime))//假加下班時間比上班時間小，有隔日狀況，故日期+1
                {
                    $endTime = $qYear.'-'.$qMonth.'-'.($qDay+1).' '.$q->$tempClass_end;
                }
                else{
                    $endTime = $qYear.'-'.$qMonth.'-'.$qDay.' '.$q->$tempClass_end;
                }
                $startTime = $qYear.'-'.$qMonth.'-'.$qDay.' '.$q->$tempClass;

                //加上前二十後十分的條件
                $startTime = date('Y-m-d H:i',strtotime("-20 minutes",strtotime($startTime)));
                $endTime = date('Y-m-d H:i',strtotime("+10 minutes",strtotime($endTime)));

                if(strtotime($aTime) <= strtotime($endTime)  && strtotime($aTime) >= strtotime($startTime)){
                    return 1;//有找到合理時間
                }
            }

        }
        return 0;//無找到
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
        if($request->exportbymonth == null){
            return back()->with('danger','請輸入資料');
        }
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
        if($request->exportbymonth == null){
            return back()->with('danger','請輸入資料');
        }
        $result = $accessTableService->export_extra_schedule($request->exportbymonth);
    }


}
