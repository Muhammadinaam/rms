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
                'name' => 'Sales By Item Report',
                'slug' => 'sales-by-item-report',
                'sort' => '13',
            ]);

            DB::table('permissions')->insert([
                'group' => 'Reports',
                'name' => 'Sales By Order Report',
                'slug' => 'sales-by-order-report',
                'sort' => '14',
            ]);

            DB::table('permissions')->insert([
                'group' => 'Reports',
                'name' => 'Collection Report',
                'slug' => 'collection-report',
                'sort' => '15',
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
