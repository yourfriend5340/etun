<?php

namespace App\Imports;


use App\Models\PatrolRecord;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class Patrol_Import implements ToModel,WithHeadingRow
{

   public function model(array $row){
   dd($row);
      return new PatrolRecord([
         'patrol_RD_DateB'    =>$row[0],
         'patrol_RD_Code'     =>$row[1],
         'patrol_upload_user' =>$row[2].'手動匯入',
         'patrol_RD_Name'     =>$row[3],
         'patrol_RD_DateB'    =>substr($row[4],0,8),

      ]);

   }
      


}