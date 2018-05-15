<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelledByColumnInTosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tos', function (Blueprint $table) {
            //
            $table->bigInteger('cancelled_by')->nullable();
            $table->text('cancellation_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tos', function (Blueprint $table) {
            //
            $table->dropColumn(['cancelled_by', 'cancellation_remarks']);
        });
    }
}
