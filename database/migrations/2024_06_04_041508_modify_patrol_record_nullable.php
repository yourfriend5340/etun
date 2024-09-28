<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPatrolRecordNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patrol_records', function (Blueprint $table) {
            // Change column to nullable
            $table->string('patrol_RD_Code')->nullable()->change();
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
            // Change column to nullable
            $table->string('patrol_RD_Code')->nullable(false)->change();
        });
    }
}
