<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTosEdits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tos_edits', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('to_id');
            $table->boolean('is_table_changed')->nullable();
            $table->bigInteger('new_table_id')->nullable();

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
        //
        Schema::dropIfExists('tos_edits');
    }
}
