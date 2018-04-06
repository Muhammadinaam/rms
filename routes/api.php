<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::group(['middleware'=>['auth:api']], function(){

    Route::post('change-password', 'UsersController@changePassword');

    Route::post('get-logged-in-user-info', 'UsersController@getLoggedInUserInfo');

    Route::get('get-menus', 'UsersController@getMenus');
    Route::get('get-permissions', 'UsersController@getPermissions');
    Route::get('has-permission', 'UsersController@hasPermission');


    Route::get('users', 'UsersController@index');
    Route::get('users/{id}/edit', 'UsersController@edit');
    Route::post('users', 'UsersController@store');
    Route::put('users/{id}', 'UsersController@update');

    Route::get('get-portions', 'TablesController@getPortions');
    Route::get('tables', 'TablesController@index');
    Route::get('tables/{id}/edit', 'TablesController@edit');
    Route::post('tables', 'TablesController@store');
    Route::put('tables/{id}', 'TablesController@update');

    Route::get('free_tables', 'TablesController@freeTables');

    Route::get('items', 'ItemsController@index');
    Route::get('items/{id}/edit', 'ItemsController@edit');
    Route::post('items', 'ItemsController@store');
    Route::put('items/{id}', 'ItemsController@update');

    Route::get('order_types', 'OrdersController@orderTypes');
    Route::get('orders/{id}/edit', 'OrdersController@edit');
    Route::post('orders', 'OrdersController@store');
    Route::put('orders/{id}', 'OrdersController@update');
    Route::post('change-order-status', 'OrdersController@changeOrderStatusApi');
    Route::post('close-order', 'OrdersController@closeOrder');
    Route::post('print-for-customer/{order_id}', 'OrdersController@printForCustomer');
    Route::post('reprint-for-kitchens/{order_id}', 'OrdersController@reprintForKitchens');

    Route::get('open-orders', 'OrdersController@openOrders');


    Route::get('show-settings', 'SettingsController@show');
    Route::post('save-settings', 'SettingsController@save');
    Route::get('get-setting-by-slug', 'SettingsController@getSettingBySlug');

    
    Route::get('sales-report', 'ReportsController@salesReport');
    Route::get('collection-report', 'ReportsController@collectionReport');

});
