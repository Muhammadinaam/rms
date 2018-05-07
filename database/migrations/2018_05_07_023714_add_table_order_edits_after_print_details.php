<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableOrderEditsAfterPrintDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edits_after_print_details', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            
            $table->bigInteger('order_id');
            $table->string('edit_type');
            $table->string('remarks');
            $table->decimal('before_amount',20,5);
            $table->decimal('after_amount',20,5);
            $table->bigInteger('edited_by');
            $table->bigInteger('approved_by');

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
        Schema::dropIfExists('edits_after_print_details');
    }
}
