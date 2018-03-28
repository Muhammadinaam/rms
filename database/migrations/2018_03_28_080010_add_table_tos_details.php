<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTosDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tos_details', function (Blueprint $table) {
            
            $table->bigIncrements('id');

            $table->bigInteger('item_id');
            $table->decimal('qty',10,3);
            $table->decimal('rate',10,2);
            $table->decimal('amount',25,5);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tos_details');
    }
}
