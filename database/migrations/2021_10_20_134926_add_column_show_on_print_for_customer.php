<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnShowOnPrintForCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('closing_accounts', function (Blueprint $table) {
            $table->boolean('show_on_print_for_customer')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('closing_accounts', function (Blueprint $table) {
            $table->dropColumns(['show_on_print_for_customer']);
        });
    }
}
