<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrcodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
  
        Schema::create('qrcode', function (Blueprint $table) {
            $table->id();
            $table->biginteger('customer_id');
            $table->string('patrol_RD_No')->nullable();
            $table->string('patrol_RD_Name');
            $table->string('patrol_RD_Code')->nullable();
            $table->tinyInteger('printQR')->default(1);
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
        Schema::dropIfExists('qrcode');
    }
}
