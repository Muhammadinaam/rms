<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->bigIncrements('id');

            $table->string('portion')->nullable();
            $table->string('name')->nullable();
            $table->datetime('reserved_time_start')->nullable();
            $table->datetime('reserved_time_end')->nullable();
            $table->bigInteger('current_order_id')->nullable();

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
        Schema::dropIfExists('tables');
    }
}
