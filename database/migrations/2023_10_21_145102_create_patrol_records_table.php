<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatrolRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patrol_records', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('patrol_RD_No');
            $table->string('patrol_RD_Name');
            $table->string('patrol_RD_Code');
            $table->string('patrol_RD_DateB');
            $table->string('patrol_RD_TimeB');
            $table->string('patrol_upload_user');
            $table->string('image_path')->nullable();
            $table->dateTime('patrol_upload_date');
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
        Schema::dropIfExists('patrol_records');
    }
}
