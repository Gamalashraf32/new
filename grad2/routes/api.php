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

    Route::post('update-info', 'ShopOwnerInfoController@update');

#========================================category=====================================================
    Route::post('add-category', 'CategoryController@addCategory');
    Route::post('update-category/{id}', 'CategoryController@updateCategory');
    Route::get('delete-category/{id}', 'CategoryController@deletecat');
    Route::get('show-category', 'CategoryController@showcat');
#========================================category=====================================================

#========================================Product=====================================================
    Route::post('add-product', 'ProductController@addProduct');
    Route::post('update-Product/{id}', 'ProductController@updateProduct');
    Route::post('delete-Product/{id}', 'ProductController@deleteProduct');
    Route::get('show-Product', 'ProductController@showProduct');
#========================================Product=====================================================

#========================================option=====================================================
    Route::post('add-option', 'OptionController@addoption');
    Route::post('update-option/{id}', 'OptionController@updateoption');
    Route::post('delete-option/{id}', 'OptionController@deleteoption');
    Route::get('show-option', 'OptionController@showoption');
#========================================option=====================================================

#========================================Variant=====================================================
    Route::post('add-variant', 'VariantController@addvariant');
    Route::post('update-variant/{id}', 'VariantController@updatevariant');
    Route::post('delete-variant/{id}', 'VariantController@deletevariant');
    Route::get('show-variant', 'VariantController@showvariant');
#========================================Variant=====================================================

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

#========================================CRUDCustomer=====================================================
    Route::post('add-customer', 'CRUDCustomerController@addcustomer');
    Route::get('show-customer/{id}', 'CRUDCustomerController@showcustomerwithid');
    Route::post('update-customer/{id}', 'CRUDCustomerController@update');
    Route::get('delete-customer/{id}', 'CRUDCustomerController@delete');
#========================================CRUDCustomer=====================================================
});
#===========================================general requests=================================================
Route::get('options','GeneralController@options');
Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('options','GeneralController@options');

});
