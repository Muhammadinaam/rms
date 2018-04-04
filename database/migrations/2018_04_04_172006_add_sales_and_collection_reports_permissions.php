<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalesAndCollectionReportsPermissions extends Migration
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
                'group' => 'Reports',
                'name' => 'Sales Report',
                'slug' => 'sales-report',
                'sort' => '13',
            ]);

            DB::table('permissions')->insert([
                'group' => 'Reports',
                'name' => 'Collection Report',
                'slug' => 'collection-report',
                'sort' => '14',
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
        });
    }
}
