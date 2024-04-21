<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePunchRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punch_record', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            for ($i=1;$i<=10;$i++)
            {
                $table->string('PunchInTime'.$i);
                $table->string('scheduleStart'.$i);
                $table->string('PunchOutTime'.$i);
                $table->string('scheduleEnd'.$i);

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
        Schema::dropIfExists('punch_record');
    }
}
