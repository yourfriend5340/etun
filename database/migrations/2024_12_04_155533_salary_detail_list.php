<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SalaryDetailList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_detail_list', function (Blueprint $table) {
            $table->id();
            $table->date('define_date')->format('Y-m');
            $table->string('type');//加減項
            $table->tinyInteger('type_serial');//加減項流水號
            $table->string('detail_name');//項目名稱
 
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
        Schema::dropIfExists('salary_detail_list');
    }
}
