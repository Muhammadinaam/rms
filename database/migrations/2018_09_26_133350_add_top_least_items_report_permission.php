<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTopLeastItemsReportPermission extends Migration
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
                'name' => 'Top and Least Selling Items Report',
                'slug' => 'top-least-selling-items-report',
                'sort' => '21',
            ]);

            DB::table('permissions')->insert([
                'group' => 'Reports',
                'name' => 'X Report',
                'slug' => 'x-report',
                'sort' => '22',
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
                ->whereIn('slug', ['top-least-selling-items-report', 'x-report'])
                ->delete();

        });
    }
}
