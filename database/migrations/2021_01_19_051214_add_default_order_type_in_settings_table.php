<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultOrderTypeInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            'slug' => 'default_order_type',
            'group' => 'General Settings',
            'setting' => 'Default order type',
            'type' => 'text',
            'Value' => 'Take Away'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->whereIn('slug', ['default_order_type'])->delete();
    }
}
