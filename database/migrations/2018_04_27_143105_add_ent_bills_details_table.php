<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntBillsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( config('app.is_client_bad') == false )
        {
            Schema::create('ent_bills_details', function (Blueprint $table) {
                $this->upFunction($table);
            });
        }

        Schema::connection('db2')->create('ent_bills_details', function (Blueprint $table) {
            $this->upFunction($table);
        });
    }

    public function upFunction($table)
    {
        $table->engine = 'InnoDB';
		
		    $table->bigIncrements('id')->unsigned();
		    $table->bigInteger('ent_bill_id');
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
        Schema::connection('db2')->dropIfExists('ent_bills_details');
    }
}
