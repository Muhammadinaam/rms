<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionsForPriceGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('permissions')->insert([
            'group' => 'Settings',
            'name' => 'View Price Groups List',
            'slug' => 'view-price-groups-list',
            'sort' => '304',
        ]);

        DB::table('permissions')->insert([
            'group' => 'Settings',
            'name' => 'Add Price Group',
            'slug' => 'add-price-group',
            'sort' => '305',
        ]);

        DB::table('permissions')->insert([
            'group' => 'Settings',
            'name' => 'Edit Price Group',
            'slug' => 'edit-price-group',
            'sort' => '306',
        ]);

        DB::table('permissions')->insert([
            'group' => 'Settings',
            'name' => 'Delete Price Group',
            'slug' => 'delete-price-group',
            'sort' => '307',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')
            ->whereIn('slug', [
                'view-price-groups-list',
                'add-price-group',
                'edit-price-group',
                'delete-price-group',
            ])
            ->delete();
    }
}
