<?php

namespace App\Exports;

use App\models\PatrolRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class PatrolRecordExport implements FromCollection,Withheadings
{
    private $cusid;
    private $cusdate;

    public function __construct($id,$date) {
        $this->cusid=$id;
        $this->cusdate=$date;
    }

    public function collection()
    {
        //dd($this->cusid);
        $records=DB::table('patrol_records')->select('customer_id','patrol_RD_DateB','patrol_RD_TimeB','patrol_RD_Name','patrol_RD_Code')
                            ->where('customer_id',$this->cusid)
                            ->where('patrol_RD_DateB',$this->cusdate)
                            ->get();
        $cusname=DB::table('customers')->where('customer_id',$this->cusid)->pluck('firstname')->first();
                
        $result = array();
        
            foreach($records as $record){
                $result[] = array(
                    'date'=>$record->patrol_RD_DateB,
                    'num'=>$record->patrol_RD_Code,
                    'name'=>$cusname,
                    'place'=>$record->patrol_RD_Name,
                    'time'=>$record->patrol_RD_TimeB,
                    'situation'=>'',
            );
        }
        //dd($result);
        return collect($result);

    }

    public function headings():array{

        return ['日期','編號','所屬區域','巡邏點','巡邏時間','巡邏狀況'];
    }
}
