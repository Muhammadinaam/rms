<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionsForSalesTaxAndClosingAccounts extends Migration
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
            'name' => 'View Closing Accounts List',
            'slug' => 'view-closing-accounts-list',
            'sort' => '200',
        ]);

        DB::table('permissions')->insert([
            'group' => 'Settings',
            'name' => 'Add Closing Account',
            'slug' => 'add-closing-account',
            'sort' => '201',
        ]);

        DB::table('permissions')->insert([
            'group' => 'Settings',
            'name' => 'Edit Closing Account',
            'slug' => 'edit-closing-account',
            'sort' => '202',
        ]);

        DB::table('permissions')->insert([
            'group' => 'Settings',
            'name' => 'Delete Closing Account',
            'slug' => 'delete-closing-account',
            'sort' => '203',
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
                'view-closing-accounts-list',
                'add-closing-account',
                'edit-closing-account',
                'delete-closing-account',
            ])
            ->delete();
    }
}
