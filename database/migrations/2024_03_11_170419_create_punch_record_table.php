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
            $table->string('customer_id');
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->string('class');
            $table->datetime('start');
            $table->datetime('end');
            $table->datetime('PunchInTime');
            $table->datetime('PunchOutTime')->nullable();

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
