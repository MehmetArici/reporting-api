<?php
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
// Group routes for version of the project
Route::group(['prefix' => 'v3'], function () {
    // Group routes for merchant user of the project

    Route::group(['prefix' => 'merchant'], function (){
        Route::post('user/login', 'MerchantController@login')->name('user-login');
        // Following routes are just for testing
        Route::post('user/me', 'MerchantController@me');
        Route::post('user/logout', 'MerchantController@logout')->name('user-logout');
        Route::post('user/refresh', 'MerchantController@refresh')->name('user-refresh-token');
    });

    // Request for list of transaction.
    Route::post('transactions/report', 'TransactionController@report')->name('transaction-report');
    // Request for all information of transaction.
    Route::post('transaction/list', 'TransactionController@list')->name('transaction-list');
    // Request for all information of transaction.
    Route::post('transaction', 'TransactionController@transaction')->name('transaction');
    // Request for all information of client.
    Route::post('client', 'ClientController@clientInfo')->name('client');
});

