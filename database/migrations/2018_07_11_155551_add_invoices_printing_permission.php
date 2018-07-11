<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoicesPrintingPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //

            DB::table('permissions')->insert([
                'group' => 'Printing',
                'name' => 'Invoices Printing',
                'slug' => 'invoices-printing',
                'sort' => '16',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //

            DB::table('permissions')
                ->whereIn('slug', ['invoices-printing'])
                ->delete();

        });
    }
}
