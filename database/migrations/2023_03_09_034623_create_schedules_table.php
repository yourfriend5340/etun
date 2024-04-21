<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {

            $schedule_time=array('A','A_end','B','B_end','C','C_end','D','D_end','E','E_end','F','F_end',
            'G','G_end','H','H_end','I','I_end','J','J_end');

            $table->id();
            $table->string('customer_id');
            $table->string('employee_id');
            $table->integer('year');
            $table->integer('month');
            //建立1到31日欄位
            for ($i=1;$i<=31;$i++)
            {
                $table->string('day'.$i);
            }

            for ($i=0;$i<=19;$i++)
            {
                $table->string($schedule_time[$i]);
            }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
