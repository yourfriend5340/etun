<?php

namespace App\Exports;

use App\Models\Schedules;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Schedule2Export implements FromCollection
{
    private $agegreaterthan;

    public function __construct($age=0) 
    {
        $this->agegreaterthan = $age;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        ## 3. Conditional export and customize result
        $records = Schedules::select('*')->where('age','>',$this->agegreaterthan)->get();

        $result = array();
        foreach($records as $record){
           $result[] = array(
              //'id'=>$record->id,
              //'username' => $record->username,
              'name' => $record->name,
              //'email' => $record->email,
              'age' => $record->age,
              //'status' => 1 // Custom data
           );
        }

        return collect($result);
    }

    public function headings(): array
    {
       return [
         //'#',
         //'Username',
         'Name',
         //'Email',
         'Age',
         //'Status'
       ];
    }
}
