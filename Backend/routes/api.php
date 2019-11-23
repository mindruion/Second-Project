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


Route::resource('cook', 'cookList');
Route::group(['middleware' => ['web']], function () {
    Route::get('cart', 'CartController@index');
    Route::post('cart', 'CartController@add_cart');
    Route::delete('cart/{id}', 'CartController@destroy');
});
