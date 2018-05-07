<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEditDiscountAfterPrintPermission extends Migration
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
                'group' => 'Orders Management',
                'name' => 'Edit / Discount after Print for Customer',
                'slug' => 'edit-discount-after-print',
                'sort' => '17',
            ]);

            //edits-after-print-report
            DB::table('permissions')->insert([
                'group' => 'Reports',
                'name' => 'Edits after Print Report',
                'slug' => 'edits-after-print-report',
                'sort' => '18',
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
                ->whereIn('slug', ['edit-discount-after-print', 'edits-after-print-report'])
                ->delete();

        });
    }
}
