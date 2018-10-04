<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRatingPermissions extends Migration
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
                'name' => 'Rating Report',
                'slug' => 'rating-report',
                'sort' => '23',
            ]);
            DB::table('permissions')->insert([
                'group' => 'Reports',
                'name' => 'Add/Edit Rating',
                'slug' => 'add-edit-rating',
                'sort' => '24',
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
                ->whereIn('slug', ['rating-report', 'add-edit-rating'])
                ->delete();
        });
    }
}
