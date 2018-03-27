<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tos', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            
            $table->integer('order_type_id');
            $table->datetime('order_datetime');
            $table->bigInteger('table_id');
            $table->string('deliver_to_name');
            $table->string('deliver_to_phone');
            $table->string('deliver_to_address');
            $table->integer('order_status_id');

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
        Schema::dropIfExists('tos');
    }
}
