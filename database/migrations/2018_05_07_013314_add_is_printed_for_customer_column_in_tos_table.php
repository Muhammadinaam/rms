<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPrintedForCustomerColumnInTosTable extends Migration
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
            $table->boolean('is_printed_for_customer')->nullable()->default(0);
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
            $table->dropColumn(['is_printed_for_customer']);
        });

    }
}
