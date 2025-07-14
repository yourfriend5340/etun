<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPunchRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punch_record', function (Blueprint $table) {
            $table->dropColumn('customer_id');  
            $table->dropColumn('end');              
            $table->dropColumn('PunchInTime');
            $table->dropColumn('PunchOutTime');
            $table->dropColumn('class');
            $table->renameColumn('start', 'punchTime');
            $table->string('type')->after('class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('punch_record', function (Blueprint $table) {
            $table->renameColumn('punchTime', 'start');
            $table->string('customer_id')->after('employee_id');
            $table->dropColumn('type');

        });
        Schema::table('punch_record', function (Blueprint $table) {            
            $table->datetime('PunchInTime')->after('start');
            $table->string('class')->after('day');
            $table->datetime('PunchOutTime')->after('PunchInTime')->nullable();
            $table->datetime('end')->after('start');
            
        });
    }
}
