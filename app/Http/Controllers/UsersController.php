<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use DB;
use Hash;
use Illuminate\Validation\Rule;


class UsersController extends Controller
{
    //

    public $all_menus = [
        [
            'name' => 'Settings',
            'icon' => 'fa fa-gears',
            'url' => 'settings',
            'permission' => 'change_settings',
        ],
        [
            'name' => 'Users', 
            'icon' => 'fa fa-users', 
            'children' => [
                [
                    'name' => 'Users List', 
                    'icon' => 'fa fa-users', 
                    'url'=> 'users', 
                    'permission' => 'view-users-list',
                ],
                [
                    'name' => 'Add User', 
                    'icon' => 'fa fa-plus-circle', 
                    'url'=> 'user', 
                    'permission' => 'add-new-user',
                ],
            ],
        ],
        [
            'name' => 'Tables', 
            'icon' => 'fa fa-table', 
            'children' => [
                [
                    'name' => 'Tables List', 
                    'icon' => 'fa fa-table', 
                    'url'=> 'tables', 
                    'permission' => 'view-tables-list',
                ],
                [
                    'name' => 'Add Table', 
                    'icon' => 'fa fa-plus-circle', 
                    'url'=> 'table', 
                    'permission' => 'add-new-table',
                ],
            ],
        ],
        [
            'name' => 'Items', 
            'icon' => 'fa fa-list', 
            'children' => [
                [
                    'name' => 'Items List', 
                    'icon' => 'fa fa-list', 
                    'url'=> 'items', 
                    'permission' => 'view-items-list',
                ],
                [
                    'name' => 'Add Item', 
                    'icon' => 'fa fa-plus-circle', 
                    'url'=> 'item', 
                    'permission' => 'add-new-item',
                ],
            ],
        ],
        [
            'name' => 'Reports', 
            'icon' => 'fa fa-bar-chart', 
            'children' => [
                [
                    'name' => 'Sales By Item', 
                    'icon' => 'fa fa-line-chart', 
                    'url'=> 'sales-by-item-report', 
                    'permission' => 'sales-report',
                ],
                [
                    'name' => 'Sales By Order', 
                    'icon' => 'fa fa-line-chart', 
                    'url'=> 'sales-by-order-report', 
                    'permission' => 'sales-report',
                ],
                [
                    'name' => 'Edits After Print', 
                    'icon' => 'fa fa-money', 
                    'url'=> 'edits-after-print-report', 
                    'permission' => 'edits-after-print-report',
                ],
                [
                    'name' => 'Cancelled Orders', 
                    'icon' => 'fa fa-money', 
                    'url'=> 'cancelled-orders-report', 
                    'permission' => 'cancelled-orders-report',
                ],
                [
                    'name' => 'Invoices Printing', 
                    'icon' => 'fa fa-print', 
                    'url'=> 'invoices-printing', 
                    'permission' => 'invoices-printing',
                ],
                [
                    'name' => 'Top and Least Items', 
                    'icon' => 'fa fa-print', 
                    'url'=> 'top-least-selling-items-report', 
                    'permission' => 'top-least-selling-items-report',
                ],
                // [
                //     'name' => 'X Report', 
                //     'icon' => 'fa fa-print', 
                //     'url'=> 'x-report', 
                //     'permission' => 'x-report',
                // ],
                // [
                //     'name' => 'Collection Report', 
                //     'icon' => 'fa fa-money', 
                //     'url'=> 'collection-report', 
                //     'permission' => 'collection-report',
                // ],
            ],
        ],
    ];

    

    public function getLoggedInUserInfo()
    {
        

        return User::where('id', Auth::user()->id)->first();

    }

    public function getMenus()
    {
        // abort(500, 'hello');
        

        if(Auth::user()->is_admin == 1)
        {
            return $this->all_menus;
        }
        else
        {
            $menus = array();
            foreach ($this->all_menus as $menu)
            {
                if(isset($menu['children']) == false )
                {
                    if(isset($menu['permission']) == false)
                        abort('500', "Permission for non-parent root menu has not been specified");
                    else
                    {
                        if( $this->hasPermission2($menu['permission'], Auth::user()->id) )
                        {
                            $menus[] = $menu;
                        }
                    }

                }
                else
                {

                    $any_child_has_permission = false;
                    $new_menu = $menu;
                    $new_menu['children'] = array();
                    foreach ($menu['children'] as $child_menu) {

                        if( $this->hasPermission2($child_menu['permission'], Auth::user()->id) )
                        {
                            $any_child_has_permission = true;
                            $new_menu['children'][] = $child_menu;

                        }
                        
                    }



                    if($any_child_has_permission == true)
                        $menus[] = $new_menu;
                }
            }

            
            return $menus;
        }
    }

    public function changePassword()
    {
        $old_password = request()->old_password;
        $new_password = request()->new_password;

        $existing_old_password = DB::table('users')
                                    ->select('password')
                                    ->where('id', Auth::user()->id)
                                    ->first()->password;

        

        if (Hash::check($old_password, $existing_old_password) == false) {
            // The passwords match...
            return ['success' => false, 'message' => 'Old password is not correct'];
        }

        DB::table('users')
            ->where('id', Auth::user()->id)
            ->update(['password' => Hash::make($new_password) ]);

        return ['success' => true, 'message' => 'Password changed successfully'];
    }

    public function getPermissions()
    {
        $user_id = request()->has('user_id') ? request()->user_id : null;

        if( $user_id != null )
        {
            $user_permission_ids = DB::table('user_permissions')->where('user_id', $user_id)->get()->pluck('permission_id');

            $user_permissions = DB::table('permissions')
                                    ->whereIn('id', $user_permission_ids)
                                    ->select('permissions.*', DB::raw('"1" as access'))
                                    ->get();

            $other_permissions = DB::table('permissions')
                                    ->whereNotIn('id', $user_permission_ids)
                                    ->select('permissions.*', DB::raw('"0" as access'))
                                    ->get();

            $merged = $user_permissions->merge($other_permissions);

            return $merged->sortBy('sort')->groupBy('group')->toArray();
        }
        else
        {
            $permissions = DB::table('permissions')
                                    ->select('permissions.*', DB::raw('"0" as access'))
                                    ->get();


            return $permissions->sortBy('sort')->groupBy('group')->toArray();
        }


    }

    public function hasPermission()
    {
        $permission = request()->permission;
        $user_id = request()->has('user_id') ? request()->user_id : Auth::user()->id;

        return ['has_permission' => $this->hasPermission2($permission, $user_id)];

    }

    public function hasPermission2($permission_slug, $user_id)
    {
        $user = DB::table('users')->where('id', $user_id)->first();

        if( $user == null )
            return false;
            

        if($user->is_admin == 1)
            return true;

        $permission = DB::table('user_permissions')
                        ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
                        ->where('permissions.slug', $permission_slug)
                        ->where('user_permissions.user_id', $user_id)
                        ->first();

        if($permission == null)
            return false;
        else
            return true;
    }

    public function index()
    {

        return User::paginate(10);
    }

    public function edit($id)
    {
        return User::where('id', $id)->first();
    }

    public function store()
    {
        //return request()->all();
        $this->validate(request(), [
            'email' => 'required|unique:users',
            'password' => 'required',
            'name' => 'required',
        ]);

        $user = new User;

        return $this->saveUserFromRequest($user);

        
    }

    public function update($id)
    {
        // return request()->all();
        $this->validate(request(), [
            'email' => [ 'required', Rule::unique('users')->ignore($id), ],
            'name' => 'required',
        ]);

        $user = User::find($id);

        return $this->saveUserFromRequest($user);
    }

    public function saveUserFromRequest($user)
    {
        try {

            DB::beginTransaction();
            

            $user->name = request()->name;
            $user->email = request()->email;

            if(request()->password != '')
                $user->password = Hash::make(request()->password);
            
            $user->is_activated = request()->is_activated == "true" || request()->is_activated == "1" ? 1 : 0;
            $user->is_admin = request()->is_admin == "true" || request()->is_admin == "1" ? 1 : 0;

            $user->save();

            DB::table('user_permissions')->where('user_id', $user->id)->delete();
            foreach (request()->permissions as $key => $value) {

                if( $value == 'true' )
                {

                    DB::table('user_permissions')
                        ->insert([
                            'user_id' => $user->id,
                            'permission_id' => $key,
                        ]);
                }
            }

            DB::commit();

            return ['success' => true, 'message' => 'Saved Successfully'];

        } catch (Exception $e) {
            
            DB::rollBack();
            return ['success' => false, 'message' => 'Some error occured. Error: ' . $ex->getMessage()];
        }
    }
}
