<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            //$table->id();
            $table->bigIncrements('customer_id');
            $table->integer('customer_group_id');
            $table->string('customer_sn');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('account');
            $table->string('password');
            $table->string('salt');
            $table->string('addr');
            $table->string('tel');
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->string('ip');
            $table->integer('status');
            $table->integer('active');
            $table->string('password_text',50)->nullable();
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
        Schema::dropIfExists('customers');
    }
}
