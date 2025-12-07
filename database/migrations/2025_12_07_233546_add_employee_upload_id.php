<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeUploadId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->tinyInteger('upload_id_control')
                ->default(0)
                ->nullable()
                ->after('pic_route4');

            $table->string('upload_pic_route1')->nullable()->after('upload_id_control');
            $table->string('upload_pic_route2')->nullable()->after('upload_pic_route1');
            $table->string('upload_pic_route3')->nullable()->after('upload_pic_route2');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'upload_id_control',
                'upload_pic_route1',
                'upload_pic_route2',
                'upload_pic_route3'
            ]);
        });
    }
}
