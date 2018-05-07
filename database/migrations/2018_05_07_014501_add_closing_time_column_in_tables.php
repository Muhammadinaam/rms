<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClosingTimeColumnInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('tos', function (Blueprint $table) {
            $this->upFunction($table);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $this->upFunction($table);
        });

        Schema::table('ent_bills', function (Blueprint $table) {
            $this->upFunction($table);
        });



        Schema::connection('db2')->table('invoices', function (Blueprint $table) {
            $this->upFunction($table);
        });

        Schema::connection('db2')->table('ent_bills', function (Blueprint $table) {
            $this->upFunction($table);
        });
    

    }

    public function upFunction($table)
    {
        $table->datetime('closing_time')->nullable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tos', function (Blueprint $table) {
            $this->downFunction($table);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $this->downFunction($table);
        });

        Schema::table('ent_bills', function (Blueprint $table) {
            $this->downFunction($table);
        });



        Schema::connection('db2')->table('invoices', function (Blueprint $table) {
            $this->downFunction($table);
        });

        Schema::connection('db2')->table('ent_bills', function (Blueprint $table) {
            $this->downFunction($table);
        });
    }

    public function downFunction($table)
    {
        $table->dropColumn('closing_time');
    }
}
