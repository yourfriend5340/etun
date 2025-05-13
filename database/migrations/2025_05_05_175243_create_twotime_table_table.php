<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwotimeTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twotime_table', function (Blueprint $table) {
            $table->id();
            $table->string('empid');
            $table->string('type');
            $table->datetime('start')->format('Y-m-d H:i');
            $table->datetime('end')->format('Y-m-d H:i')->nullable();
            $table->string('reason');
            $table->string('status')->nullable();
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
        Schema::dropIfExists('twotime_table');
    }
}
