<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestaurantInformationInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            DB::table('settings')->insert([
                'slug' => 'restaurant_name',
                'group' => 'Restaurant Information',
                'setting' => 'Restaurant Name',
                'type' => 'text',
                'Value' => 'Restaurant Name'
            ]);

            DB::table('settings')->insert([
                'slug' => 'restaurant_address',
                'group' => 'Restaurant Information',
                'setting' => 'Restaurant Address',
                'type' => 'text',
                'Value' => 'Restaurant Address'
            ]);

            DB::table('settings')->insert([
                'slug' => 'restaurant_ntn',
                'group' => 'Restaurant Information',
                'setting' => 'NTN',
                'type' => 'text',
                'Value' => 'NTN'
            ]);

            DB::table('settings')->insert([
                'slug' => 'restaurant_stn',
                'group' => 'Restaurant Information',
                'setting' => 'STN',
                'type' => 'text',
                'Value' => 'STN'
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
        Schema::table('settings', function (Blueprint $table) {
            //
            DB::table('settings')->whereIn('slug', ['restaurant_name','restaurant_address','restaurant_ntn','restaurant_stn'])->delete();
        });
    }
}
