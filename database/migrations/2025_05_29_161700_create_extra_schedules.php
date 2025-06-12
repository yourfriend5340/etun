<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('emp_id');
            $table->datetime('start')->format('Y-m-d H:i');
            $table->datetime('end')->format('Y-m-d H:i');
            $table->string('leave_member');
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
        Schema::dropIfExists('extra_schedules');
    }
}
