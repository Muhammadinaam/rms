<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClosingAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closing_accounts', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('order');
            $table->string('name')->unique();
            $table->decimal('sales_tax_rate')->default(0);
            $table->boolean('show_amount_received_input')->default(true);
            $table->string('additional_information_fields')->nullable();

            $table->timestamps();
        });

        \DB::table('closing_accounts')
            ->insert([
                [
                    "order" => 1,
                    "name" => 'Cash',
                    "sales_tax_rate" => '16',
                    "show_amount_received_input" => 1,
                    "additional_information_fields" => '',
                ],
                [
                    "order" => 2,
                    "name" => 'Card',
                    "sales_tax_rate" => '5',
                    "show_amount_received_input" => 0,
                    "additional_information_fields" => json_encode([
                        ['name' => 'Card Number', 'required' => '1'],
                        ['name' => 'Customer Name', 'required' => '0'],
                    ]),
                ],
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('closing_accounts');
    }
}
