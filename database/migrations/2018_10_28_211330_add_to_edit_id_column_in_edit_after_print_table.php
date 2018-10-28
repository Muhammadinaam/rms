<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToEditIdColumnInEditAfterPrintTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('edits_after_print_details', function (Blueprint $table) {
            //
            $table->bigInteger('to_edit_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edits_after_print_details', function (Blueprint $table) {
            //
            $table->dropColumn(['to_edit_id']);
        });
    }
}
