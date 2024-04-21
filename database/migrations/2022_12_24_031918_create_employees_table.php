<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            //$table->id('patrol_member_id')->startingValue(401);
            $table->bigIncrements('id');
            //$table->bigInteger('uid')->length(20)->unsigned();
            $table->string('member_name',50); 
            $table->string('salt',100)->nullable();
            $table->string('member_sn',50)->nullable();
            $table->string('member_phone',50)->nullable();
            $table->string('member_account',50)->nullable();
            $table->string('member_password',100)->nullable();
            $table->string('member_password_text',50)->nullable();     
            $table->string('organize')->nullable();
            $table->string('area')->nullable();
            $table->string('SSN')->nullable();
            $table->string('Gender')->nullable();        
            $table->string('Blood_type')->nullable();
            $table->date('Birthday')->nullable();
            $table->integer('Height')->nullable();
            $table->integer('Weight')->nullable();
            $table->string('Branch')->nullable();
            $table->string('mobile')->nullable();   
            $table->string('mail')->nullable();
            $table->string('addr')->nullable();
            $table->string('current_addr')->nullable();
            $table->string('pic_route1')->nullable();
            $table->string('pic_route2')->nullable();
            $table->string('pic_route3')->nullable();
            $table->string('pic_route4')->nullable();
            $table->string('driver')->nullable();
            $table->string('language')->nullable();
            $table->string('school')->nullable();
            $table->string('department')->nullable();
            $table->string('graduate')->nullable();
            $table->string('status')->nullable();
            $table->string('work_place')->nullable();
            $table->string('position')->nullable();
            $table->integer('salary')->nullable();
            $table->date('register')->nullable();
            $table->date('leave')->nullable();
            $table->date('check_send')->nullable();
            $table->date('check_back')->nullable();
            $table->date('agreement_send')->nullable();
            $table->date('agreement_back')->nullable();
            $table->date('labor_date')->nullable();
            $table->integer('labor_account')->nullable();
            $table->integer('retirement_account')->nullable();
            $table->date('health_date')->nullable();
            $table->integer('health_account')->nullable();
            $table->date('life_date')->nullable();
            $table->date('group_date')->nullable();
            $table->date('care_date')->nullable();
            $table->date('checkup')->nullable();
            $table->string('memo')->nullable();                         
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
        Schema::dropIfExists('employees');
    }
}
