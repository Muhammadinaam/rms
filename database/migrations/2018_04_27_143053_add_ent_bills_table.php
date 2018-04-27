<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::connection('db2')->create('ent_bills', function (Blueprint $table) {
            $this->upFunction($table);
        });
    }

    public function upFunction($table)
    {
        $table->engine = 'InnoDB';
		
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('order_id');
		    $table->integer('order_type_id');
		    $table->dateTime('order_datetime');
		    $table->dateTime('served_datetime')->default(null);
		    $table->bigInteger('table_id')->default(null);
		    $table->string('deliver_to_name', 191)->default(null);
		    $table->string('deliver_to_phone', 191)->default(null);
		    $table->string('deliver_to_address', 191)->default(null);
		    $table->integer('order_status_id');
		    $table->string('received_through', 191)->default(null);
		    $table->bigInteger('received_by')->default(null);
		    $table->dateTime('received_at')->default(null);
		    $table->decimal('order_amount_ex_st', 20, 5)->default(null);
		    $table->decimal('sales_tax', 10, 2)->default(null);
		    $table->decimal('order_amount_inc_st', 20, 5)->default(null);
		    $table->decimal('cover', 8, 2)->default(null);
		    $table->decimal('order_amount_before_discount', 20, 5)->default(null);
		    $table->decimal('discount', 20, 5)->default(null);
		    $table->integer('discount_allowed_by')->default(null);
		    $table->text('ent_remarks');
		
		    $table->timestamps();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('db2')->dropIfExists('ent_bills');
    }
}
