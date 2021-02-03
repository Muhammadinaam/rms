<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSalesTaxFromSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->whereIn('slug', ['sales_tax_rate'])->delete();
        DB::table('permissions')
        ->where('name', 'Change Settings')
        ->update(['name' => 'Change General Settings']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->insert([
            'slug' => 'sales_tax_rate',
            'group' => 'General Settings',
            'setting' => 'Sales Tax Rate',
            'type' => 'number',
            'Value' => '16'
        ]);
    }
}
