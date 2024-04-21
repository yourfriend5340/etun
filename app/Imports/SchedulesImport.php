<?php

namespace App\Imports;

use App\Models\Schedules;
use Maatwebsite\Excel\Concerns\ToModel;


class SchedulesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

//dump($row);
        /*
    
        // Check age already exists檢查資料庫已經有n歲的data，若有return null
 
        //$count = Schedules::where('name',$row[0])->count();
        //dump($row);
        //if($count > 0){
        //    return null;
        //}
        //dump($row[4][2]);
        //return $row;    
        return new Schedules([
            //'username' => $row[0],
            'name' => $row[0], 
            //'age'  => $row[1],
            //'email' => $row[2],
        ]);

    */


}
    
}
