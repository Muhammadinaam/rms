<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInTosTable extends Migration
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
            $table->decimal('cover')->before('deliver_to_name')->nullable();
            $table->decimal('order_amount_before_discount', 20,5)->before('order_amount_ex_st')->nullable();
            $table->decimal('discount', 20,5)->before('order_amount_ex_st')->nullable();
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
            $table->dropColumn(['cover','order_amount_before_discount','discount']);
        });
    }
}
