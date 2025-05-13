<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnetimeTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onetime_table', function (Blueprint $table) {
            $table->id();
            $table->string('empid');
            $table->string('type');
            $table->date('start')->format('Y-m-d');
            $table->string('reason');
            $table->string('status')->default('N');
            $table->string('filePath');

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
        Schema::dropIfExists('onetime_table');
    }
}
