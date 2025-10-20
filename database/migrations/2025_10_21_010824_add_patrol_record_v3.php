<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPatrolRecordV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patrol_records', function (Blueprint $table) {
            $table->decimal('lng', 10, 7)->after('patrol_RD_Code');
            $table->decimal('lat', 10, 7)->after('lng');
            $table->string('abnormal')->nullable()->after('lat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patrol_records', function (Blueprint $table) {
            $table->dropColumn(['lng', 'lat', 'abnormal']);
        });
    }
}
