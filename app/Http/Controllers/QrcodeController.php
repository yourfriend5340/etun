<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use App\Models\Qrcodes;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QrcodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        // QR code with text
        
        $data['qrcode'] = QrCode::encoding('UTF-8')->generate('customer_id:D08028,place:2');

        // Store QR code for download
        //QrCode::generate('customer_id:D08028,place:2', public_path('images/qrcode.svg') );

        $request=Customer::select('customer_id','firstname')->get();

        return view('qrcode',$data,["customers"=>$request]);
    }

    public function store(Request $request){

        try{
            $rules=[
                "cus_name" => "required",
                "patrol_place" => "required",
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "cus_name.required" => '"客戶名稱"為必選資料',
                "patrol_place.required"=>'"巡邏地點"為必填資料',
            ];

            $validResult = $request->validate($rules, $message);

        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
            return $errorMessage;
        }

        $id=$request->input('cus_name');
        $patrol_place=$request->input('patrol_place');

        $check=DB::table('qrcode')->where([
            ['customer_id','=',$id],
            ['patrol_RD_Name','=',$patrol_place],
            ])->count();

           //dd($name,$patrol_place,$check);

        if ($check==0)
        {    
            $max_patrol_RD_No=DB::table('qrcode')->where([
                ['patrol_RD_NO','!=',""],
                ['customer_id','=',$id]
                ])->max('patrol_RD_NO');
      

            $cus_sn=Customer::select('customer_sn')->where('customer_id','=',$id)->get()->first();
            $cus_sn=$cus_sn->customer_sn.'_';
            if($max_patrol_RD_No==null){
                $max_patrol_RD_No=$cus_sn.'000';                
            }
            //dd($max_patrol_RD_No);
            $str=str_replace("$cus_sn",'',$max_patrol_RD_No);

            $new_id=$cus_sn.str_pad($str+1,3,'0',STR_PAD_LEFT);
            //dd(gettype($new_id));
            //dd($max_patrol_RD_No.'-'.$cus_sn.'='.$str.'__new_ID is:'.$new_id); 
            $data=[                
                'customer_id'=>$id, 
                'patrol_RD_No'=>$new_id,
                'patrol_RD_Name'=>$patrol_place,
            ];
//dd($data);
            Qrcodes::create($data);

            return redirect('qrcode_desc/'.$id);
        }
        else{
            return back()->with('danger','此資料已經有建過，故無存進資料庫');
        }
    }

    public function delete(Request $request,$Delete_id){
        $id=DB::table('qrcode')->select('customer_id')->where('patrol_RD_No','=',$Delete_id)->get()->first();
        //dd($id->customer_id);
        $deleted=DB::table('qrcode')->where('patrol_RD_No','=',$Delete_id)->delete();
        //return response(null,Response::HTTP_NO_CONTENT);
        return redirect('qrcode_asc/'.$id->customer_id);
    }

    public function update(Request $request){
            try{
            $rules=[
            "qrcode_name" => "required",
            "qrcode_addr"=> "required"
            ];

            $message = [
                // 欄位名稱.驗證方法名稱
                "qrcode_name.required" => '"客戶名稱"為必填資料',
                "qrcode_addr.required"=>'"巡邏地點"為必填資料',
            ];
            $validResult = $request->validate($rules, $message);

        }
        catch (ValidationException $exception) {
            $errorMessage =$exception->validator->getMessageBag()->getMessages();
        return $errorMessage;
        }
        
        $name=$request->input('qrcode_name');
        $qrcode_id=$request->input('qrcode');
        $cus_id= DB::table('customers')->select('customer_id')->where('firstname','=',$name)->first();
        //dd($name);
        $addr = $request->input('qrcode_addr');

        //dd($cus_id->customer_id,$addr);
        $data=[
            'patrol_RD_Name'=>$addr,
        ];


        $qrcode=Qrcodes::where('patrol_RD_No','=',$qrcode_id)->update($data);
        return redirect('qrcode_asc/'.$cus_id->customer_id);
    }

    public function show()
    {
        $cus=DB::table('customers')->select('customer_id','firstname')->get();
        return view("show_qrcode",['cus_info'=>$cus]);

    }
   
        public function show_result_asc(Request $request,$id)
    {
  
        $cus=DB::table('customers')->select('customer_id','firstname')->get();
        $qrcode= DB::table('qrcode')
            ->join('customers','qrcode.customer_id','=','customers.customer_id')
            ->where('qrcode.customer_id','=',$id)
            ->where('patrol_RD_No','!=',"")
            ->orderBy('patrol_RD_No','asc')
            ->paginate(20);

        return view("show_qrcode",["qrcodes"=>$qrcode,'cus_info'=>$cus]);

    }

    public function show_result_desc(Request $request,$id)
    {
        //dd($id);
        $cus=DB::table('customers')->select('customer_id','firstname')->get();
        $qrcode= DB::table('qrcode')
            ->join('customers','qrcode.customer_id','=','customers.customer_id')
            ->where('qrcode.customer_id','=',$id)
            ->where('patrol_RD_No','!=',"")
            ->orderBy('patrol_RD_No','desc')
            ->paginate(20);

        return view("show_qrcode",["qrcodes"=>$qrcode,'cus_info'=>$cus]);


    }

    public function request(Request $request,$Request_id){

        $qrcode=DB::table('qrcode')
        ->join('customers','qrcode.customer_id','=','customers.customer_id')
        ->where('patrol_RD_No','=',$Request_id)
        ->orderBy('patrol_RD_Code','asc')->get();


        return view("edit_qrcode",["qrcodes"=>$qrcode]);

    }

    public function setprint(){
        $cus=Customer::select('customer_id','firstname')->get();


        return view("print_qrcode",['cus_info'=>$cus]);

    }

    public function print_asc(Request $request,$print_id){
        $cus=Customer::select('customer_id','firstname')->get();
        $pid=$print_id;

        $qr=DB::table('qrcode')
        ->join('customers','qrcode.customer_id','=','customers.customer_id')
        ->where('qrcode.customer_id','=',$pid)
        ->where('qrcode.patrol_RD_No','!=',"")
        ->orderby('qrcode.patrol_RD_No','asc')->get();

        return view("print_qrcode",['cus_info'=>$cus,'qr_info'=>$qr]);

    }

    public function print_desc(Request $request,$print_id){
        $cus=Customer::select('customer_id','firstname')->get();
        $pid=$print_id;

        $qr=DB::table('qrcode')
        ->join('customers','qrcode.customer_id','=','customers.customer_id')
        ->where('qrcode.customer_id','=',$pid)
        ->where('qrcode.patrol_RD_No','!=',"")
        ->orderby('qrcode.patrol_RD_No','desc')->get();

        return view("print_qrcode",['cus_info'=>$cus,'qr_info'=>$qr]);

    }

    public function print(Request $request){
    if ($request->input('chk')!=null){
        $count=count($request->input('chk'));


        //把列印狀態設0
        $dedata=['printQR'=>'0'];
        QRCodes::where('patrol_RD_No','!=',"")->update($dedata);

        //更新有選check的選項，列印狀態設on
        for ($i=0;$i<$count;$i++){
            $data=['printQR'=>'1'];
            QRCodes::where('id','=',$request->input("chk.$i"))->update($data);
        }
        //end 更新狀態

        //QRcode產生器
        $qrcode_id=DB::table('qrcode')->select('customer_id')->where('id','=',$request->input("chk.0"))->first();
        $cus_name=DB::table('customers')->select('firstname')->where('customer_id','=',$qrcode_id->customer_id)->first();
        $qrcode_data=QRcodes::select('patrol_RD_No','patrol_RD_Name')->where([
            ['customer_id','=',"$qrcode_id->customer_id"],
            ['printQR','=','1'],
            ['patrol_RD_No','!=',''],
            ])->orderby('patrol_RD_Name')
            ->get()->toArray();

        for ($i=0;$i<$count;$i++){
            $qr_file[$i]=QrCode::encoding('UTF-8')->format('png')->size(150)->errorCorrection('L')
            ->generate("id:".$qrcode_data[$i]['patrol_RD_No'].",place:".$qrcode_data[$i]['patrol_RD_Name'], public_path("images/temp/qrcode$i.png") );
        }//end QRcode產生器
    

        //EXCEL處理
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        //設定預設格式
        $spreadsheet->getActiveSheet()->getPageSetup()
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        // Retrieve the current active worksheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);//預設寬度
        $sheet->getDefaultRowDimension()->setRowHeight(25);//預設高度
        
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

        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(26);//设置字体加粗大小
        $sheet->setCellValue('A1', $cus_name->firstname);

        $max_row=2+(2*intval($count/4))+1;
        $max='D'.$max_row;
        //dd($max);
        $sheet->getStyle("A1:$max")->applyFromArray($styleCenterArray);

        for ($i=$max_row-1;$i>=2;$i-=2){
            $sheet->getRowDimension($i)->setRowHeight(127);
            //$sheet->getColumnDimension('A')->setWidth(20);
        }
        //end 表格準備

        //計算excel高度、哪一個橫向格,所需陣列
        $width_array=array('D','A','B','C');

        //第n條資料，其所屬欄位判斷,並寫入
        for ($i=1;$i<=$count;$i++){
            $width_quotient=intval($i/4);
            $width_remainder=$i % 4;

            if ($width_remainder==0){
            $width_quotient=$width_quotient-1;
            }
        
            $which_column=$width_array[$width_remainder];
            $which_row=2+(2*$width_quotient);//公式為2(2*商)+1

            $text_column=$which_column.$which_row+1;
            $sheet->setCellValue("$text_column", $qrcode_data[$i-1]['patrol_RD_Name']);

        // Prepare the drawing object
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

            
            // Set the picture name
            //$drawing->setName('logo');
            
            // Set the picture path
            $path='images/temp/qrcode'.($i-1).'.png';
            $drawing->setPath($path);
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWidth(150);//图片宽
            $drawing->setHeight(150);//图片高
        
            // Set the cell address where the picture will be inserted
            $drawing->setCoordinates("$which_column$which_row");        
        
            // Add the drawing to the worksheet
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
        }//END 欄位判斷


        $file_name = 'QRcode_'.date('Y_m_d');
        // Write a new .xlsx file
        $writer = new Xlsx($spreadsheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Save the new .xlsx file
        $writer->save('php://output');        
        }

    else { 
        return back()->with('danger','請至少點選一個要列印的資訊');
    }
    }


}
