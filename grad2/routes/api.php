<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
#===========================================SuperAdmin-auth======================================
Route::post('super-admin-login', 'App\Http\Controllers\Api\Auth\AdminController@login');

#===========================================ShopOwner-auth========================================


Route::group(['namespace' => 'App\Http\Controllers\Api\Auth', 'prefix' => 'auth-shop-owner'], function () {
    Route::post('login', 'ShopOwnerController@login');
    Route::post('register', 'ShopOwnerController@register');
    Route::get('profile', 'ShopOwnerController@profile');
    Route::post('logout', 'ShopOwnerController@logout');
});

#===========================================Customer-auth============================================
Route::group(['namespace' => 'App\Http\Controllers\Api\Auth', 'prefix' => 'auth-customer'], function () {
    Route::post('login', 'CustomerController@login');
    Route::post('register', 'CustomerController@register');
    Route::get('profile', 'CustomerController@profile');
    Route::post('logout', 'CustomerController@logout');
});
#===========================================ShopOwner=================================================
Route::group(['middleware'=>'auth.guard:shop_owner','namespace' => 'App\Http\Controllers\Api\ShopOwner', 'prefix' => 'shop-owner'], function () {
    Route::post('add-category', 'CategoryController@addCategory');
    Route::post('update-category/{id}', 'CategoryController@updateCategory');
    Route::get('delete-category/{id}', 'CategoryController@deletecat');
    Route::get('show-category', 'CategoryController@showcat');
    Route::post('add-product', 'CategoryController@addProduct');
    Route::post('update-Product/{id}', 'CategoryController@updateProduct');
    Route::post('delete-Product/{id}', 'CategoryController@deleteProduct');
    Route::get('show-Product', 'CategoryController@showProduct');
    Route::post('add-option', 'CategoryController@addoption');
    Route::post('update-option/{id}', 'CategoryController@updateoption');
    Route::post('delete-option/{id}', 'CategoryController@deleteoption');
    Route::get('show-option', 'CategoryController@showoption');
    Route::post('add-variant', 'CategoryController@addvariant');
    Route::post('update-variant/{id}', 'CategoryController@updatevariant');
    Route::post('delete-variant/{id}', 'CategoryController@deletevariant');
    Route::get('show-variant', 'CategoryController@showvariant');
#========================================Discount==================================================
    Route::post('add-discountcode', 'DiscountCodeController@adddiscountcode');
    Route::post('delete-discountcode/{id}', 'DiscountCodeController@deletediscount');
    Route::get('show', 'DiscountCodeController@showall');
    Route::post('update-discountcode/{id}', 'DiscountCodeController@update');
    Route::get('showone/{id}', 'DiscountCodeController@showone');
#========================================Discount==================================================
#========================================Theme=====================================================
    Route::post('chooseTheme', 'ThemeController@chooseTheme');
    Route::post('update', 'ThemeController@update');
#========================================Theme=====================================================
    Route::post('add-customer', 'CRUDCustomerController@addcustomer');
   // Route::get('show-customer', 'CRUDCustomerController@showcustomer');
    Route::get('show-customer/{id}', 'CRUDCustomerController@showcustomerwithid');
    Route::post('update-customer/{id}', 'CRUDCustomerController@update');
    Route::get('delete-customer/{id}', 'CRUDCustomerController@delete');

});
#===========================================general requests=================================================
Route::get('options','GeneralController@options');
Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('options','GeneralController@options');

});
