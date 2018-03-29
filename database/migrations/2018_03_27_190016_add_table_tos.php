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
            $table->datetime('served_datetime')->nullable();
            $table->bigInteger('table_id')->nullable();
            $table->string('deliver_to_name')->nullable();
            $table->string('deliver_to_phone')->nullable();
            $table->string('deliver_to_address')->nullable();
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
