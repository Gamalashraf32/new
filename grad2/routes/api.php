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
    Route::get('show-cat-id/{id}', 'CategoryController@showcatid');
#========================================category=====================================================

#========================================Product=====================================================
    Route::post('add-product', 'ProductController@addProduct');
    Route::post('update-product/{id}', 'ProductController@updateProduct');
    Route::post('delete-product/{id}', 'ProductController@deleteProduct');
    Route::get('show-product', 'ProductController@showProduct');
    Route::get('show-product/{id}', 'ProductController@showProductwithid');
#========================================Product=====================================================

#========================================option=====================================================
    Route::post('add-option', 'OptionController@addoption');
    Route::post('update-option/{id}', 'OptionController@updateoption');
    Route::post('delete-option/{id}', 'OptionController@deleteoption');
    Route::get('show-option', 'OptionController@showoption');
    Route::get('show-option/{id}', 'OptionController@showoptionwithid');
#========================================option=====================================================

#========================================Variant=====================================================
    Route::post('add-variant', 'VariantController@addvariant');
    Route::post('update-variant/{id}', 'VariantController@updatevariant');
    Route::post('delete-variant/{id}', 'VariantController@deletevariant');
    Route::get('show-variant/{id}', 'VariantController@showvariantwithid');
#========================================Variant=====================================================

#========================================Discount==================================================
    Route::post('add-discountcode', 'DiscountCodeController@adddiscountcode');
    Route::post('delete-discountcode/{id}', 'DiscountCodeController@deletediscount');
    Route::get('show-discounts', 'DiscountCodeController@showall');
    Route::post('update-discountcode/{id}', 'DiscountCodeController@update');
    Route::get('show-discount/{id}', 'DiscountCodeController@showone');
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
    Route::get('show-customer', 'CRUDCustomerController@showcustomer');
    Route::get('choose-plan', 'PlanController@choose');
#========================================CRUDCustomer=====================================================

#========================================shipping=====================================================
    Route::post('shipping-add', 'ShippingController@add');
    Route::post('shipping-update/{id}', 'ShippingController@update');
    Route::post('shipping-delete/{id}', 'ShippingController@delete');
    Route::get('shipping-show', 'ShippingController@show');
    Route::get('shipping-showid/{id}', 'ShippingController@showid');
#========================================shipping=====================================================

#========================================Order=====================================================
    Route::post('add-order', 'OrderController@add_order');
    Route::post('delete_order/{id}', 'OrderController@delete_order');
    Route::post('update_order/{id}', 'OrderController@update');
    Route::get('show-orders', 'OrderController@showall_orders');
    Route::get('show-order/{id}', 'OrderController@show_order');
#========================================Order=====================================================

#========================================plan=====================================================
    Route::post('choose-plan', 'PlanController@choose');
#========================================Search=====================================================
    Route::get('product-search/{name}', 'SearchController@productsearch');
    Route::get('customer-search/{name}', 'SearchController@searchcustomer');
    Route::get('shipping-search/{name}', 'SearchController@searchshipping');
});
#=========================================================================================================
Route::get('show-plan', 'App\Http\Controllers\Api\ShopOwner\PlanController@show');
Route::get('paymob-callback', 'App\Http\Controllers\Api\PaymobController@processedCallback');
#===========================================mailing=================================================
//Auth::routes(['verify' => true]);
//Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');

Route::group(['middleware'=>'auth.guard:api','namespace' => 'App\Http\Controllers\Api\Customer', 'prefix' => 'Customer'], function () {
    
    Route::get('show-cat', 'CategoryController@showcat');
    Route::get('show-cat-id/{id}', 'CategoryController@showcatid');
    Route::get('show-cat-Products', 'ProductsController@showCatProducts');
    Route::get('show-Product-id/{id}', 'ProductsController@showprouctid');
    Route::get('show-all-products', 'ProductsController@showallProducts');
    Route::get('search-product/{name}', 'ProductsController@searchproduct');


    Route::post('editinfo', 'ProfileController@editinfo');
    Route::get('all-orders', 'ProfileController@showallorders');
    Route::get('order/{id}', 'ProfileController@showoneorder');


});
#========================================Customer=====================================================

//Auth::routes(['verify' => true]);
//Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
#===========================================mailing=================================================

