<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoicesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices_details', function (Blueprint $table) {
            $this->upFunction($table);
        });

        Schema::connection('db2')->create('invoices_details', function (Blueprint $table) {
            $this->upFunction($table);
        });
    }

    public function upFunction($table)
    {
        $table->engine = 'InnoDB';
		
		    $table->bigIncrements('id')->unsigned();
		    $table->bigInteger('invoice_id');
		    $table->bigInteger('item_id');
		    $table->decimal('qty', 10, 3);
		    $table->decimal('rate', 10, 2);
            $table->decimal('amount', 25, 5);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices_details');
        Schema::connection('db2')->dropIfExists('invoices_details');
    }
}
