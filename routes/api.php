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

    Route::get('closing-accounts', 'ClosingAccountController@index');
    Route::get('closing-accounts/{id}/edit', 'ClosingAccountController@edit');
    Route::post('closing-accounts', 'ClosingAccountController@store');
    Route::put('closing-accounts/{id}', 'ClosingAccountController@update');
    Route::delete('closing-accounts/{id}', 'ClosingAccountController@destroy');

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
    Route::post('save-order-discount', 'OrdersController@saveOrderDiscount');
    Route::post('print-for-customer/{order_id}/{sales_tax_rate}', 'OrdersController@printForCustomer');
    Route::post('reprint-for-kitchens/{order_id}', 'OrdersController@reprintForKitchens');
    Route::get('get_order_edit/{id}', 'OrdersController@getOrderEdit');

    Route::get('open-orders', 'OrdersController@openOrders');


    Route::get('show-settings', 'SettingsController@show');
    Route::post('save-settings', 'SettingsController@save');
    Route::get('get-setting-by-slug', 'SettingsController@getSettingBySlug');
    Route::get('get-server-side-config', 'SettingsController@getConfig');

    
    Route::get('sales-report-by-item', 'ReportsController@salesReportByItem');
    Route::get('sales-report-by-order', 'ReportsController@salesReportByOrder');
    Route::get('edits-after-print-report', 'ReportsController@editsAfterPrintReport');
    Route::get('collection-report', 'ReportsController@collectionReport');
    Route::get('cancelled-orders-report', 'ReportsController@cancelledOrdersReport');
    Route::post('invoices-printing', 'OrdersController@invoicesPrinting');
    Route::get('top-least-selling-items-report', 'ReportsController@TopLeastSellingItemsReport');
    Route::get('get-invoice-data', 'ReportsController@getInvoiceData');

    Route::get('ratings-report', 'RatingsController@index');
    Route::post('save-rating', 'RatingsController@saveRating');

});
