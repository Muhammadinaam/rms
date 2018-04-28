<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntReportPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('permissions', function (Blueprint $table) {
            //
            DB::table('permissions')->insert([
                'group' => 'Reports',
                'name' => 'Ent Report',
                'slug' => 'ent-report',
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
        //
        Schema::table('permissions', function (Blueprint $table) {
            //

            DB::table('permissions')
                ->whereIn('name', ['ent-report'])
                ->delete();

        });
    }
}
