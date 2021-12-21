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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'App\Http\Controllers\apis'], function () {
    Route::get('upload-tags', 'HomePageApi@upload');
    //======================= selectors ========================= 
    Route::get('banners', 'BannersController@anyIndex');

    Route::get('languages', 'SelectsController@getLanguages');
    Route::get('languages/get-active', 'SelectsController@getActiveLang');
    Route::any('users/sms/send', 'AuthApi@sendSms');
    Route::any('users/sms/confirm', 'AuthApi@confirm');
    Route::any('users/checkphone', 'AuthApi@sendSms');
    Route::any('users/reset_password', 'AuthApi@resetPassword');
    //============================= Auth =============================
    Route::post('auth/signup', 'AuthApi@register');
    Route::post('auth/signin', 'AuthApi@login');
    Route::post('auth/login-social', 'AuthApi@loginSocial');
    Route::post('contact', 'HomePageApi@contact');
    Route::get('setting', 'HomePageApi@setting');

    Route::group(['middleware' => ['api']], function () {
        Route::get('home-page', 'HomePageApi@index');
        Route::get('about', 'HomePageApi@about');
        Route::get('cities', 'SelectsController@getCities');
        Route::get('categories', 'CategoriesApi@index');
        Route::get('sub-categories', 'CategoriesApi@sub');
        Route::get('categories/show', 'CategoriesApi@show');
        Route::get('categories/get', 'CategoriesApi@getCategory');
        Route::get('products', 'ProductsController@all');
        Route::get('products/show', 'ProductsController@show');

        Route::post('promo/check', 'CartsApi@checkPromo');
        Route::post('carts/edit-number', 'CartsApi@edit');
        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('users/myaccount', 'UsersAPI@myacount');
            Route::get('users/notifications', 'UsersAPI@notifications');
            Route::post('users/update-profile', 'UsersAPI@updateProfile');
            Route::get('users/update-device-id', 'UsersAPI@updateDeviceId');
            Route::post('users/update-password', 'UsersAPI@updatePassword');
            Route::get('profile/logout', 'UsersAPI@logout');
            Route::group(['middleware' => ['client']], function () {

                Route::any('carts/add', 'CartsApi@add');
                Route::any('carts/edit-number', 'CartsApi@edit');

                Route::get('carts', 'CartsApi@index');
                Route::get('carts/count', 'CartsApi@countCart');
                Route::any('carts/clear', 'CartsApi@clear');
                Route::get('carts/delete', 'CartsApi@delete');
                Route::get('checkout_cost', 'OrdersApi@cost');
                Route::post('checkout', 'OrdersApi@index');
                Route::get('orders', 'UsersAPI@orders');
                Route::get('orders/show', 'UsersAPI@showOrder');
                Route::get('orders/cancel', 'UsersAPI@cancelOrder');

                //============================Rating=======================
                Route::post('rating/add', 'RatingAPI@add');
                // ============== addresses =================
                Route::get('address/get', 'UsersAPI@getAdreesses');
                Route::post('address/add', 'UsersAPI@addAdreess');
                Route::post('address/update', 'UsersAPI@updateAdrress');
                Route::post('address/delete', 'UsersAPI@deleteAdrress');
                // ============== addresses =================
                Route::get('wishlists', 'WishlistApi@index');
                Route::get('wishlists/add', 'WishlistApi@add');
                Route::get('wishlists/delete', 'WishlistApi@delete');
            });

            Route::group(['middleware' => ['family', 'active']], function () {
                Route::get('family/products', 'ProductsController@index');
                Route::get('products/display', 'ProductsController@display');
                Route::post('products/create', 'ProductsController@create');
                Route::post('products/update', 'ProductsController@update');
                Route::get('products/delete', 'ProductsController@delete');
                Route::get('family/orders', 'OrdersApi@familyOrders');
                Route::get('family/orders/accept', 'OrdersApi@acceptOrder');
                Route::get('family/orders/finish', 'OrdersApi@finishOrder');
                Route::get('family/orders/refuse', 'OrdersApi@refuseOrder');
                Route::post('family/update-location', 'UsersAPI@updateFamilyLocation');
            });

            Route::group(['middleware' => ['delivery', 'active']], function () {
                Route::get('delivery/new-orders', 'OrdersApi@DeliveryNewOrders');
                Route::get('delivery/orders', 'OrdersApi@DeliveryOrders');
                Route::get('delivery/accept-order', 'OrdersApi@DeliveryacceptOrders');
                Route::get('delivery/receive-order', 'OrdersApi@DeliveryrecieveOrders');
                Route::get('delivery/finish-order', 'OrdersApi@DeliveryfinishOrders');
                Route::post('delivery/update-location', 'UsersAPI@updateDeliveryLocation');
                Route::get('delivery/change-status', 'UsersAPI@updateStatus');
            });
            Route::group(['middleware' => ['admin'], 'prefix' => '/backend'], function () {
                Route::get('products', 'ProductsController@all');
                Route::get('products/active', 'ProductsController@active');
                Route::get('orders', 'OrdersApi@all');
                //users
                Route::get('users', 'UsersAPI@all');
                 Route::get('users/show', 'UsersAPI@show');
                Route::get('users/active', 'UsersAPI@active');
                Route::get('users/special', 'UsersAPI@special');
                //categories
                Route::get('categories', 'CategoriesController@index');
                Route::post('categories/add', 'CategoriesController@add');
                Route::post('categories/edit', 'CategoriesController@edit');
                Route::get('categories/show', 'CategoriesController@display');
                Route::get('categories/delete', 'CategoriesController@delete');
                //cities
                Route::get('cities', 'CitiesController@index');
                Route::post('cities/add', 'CitiesController@add');
                Route::post('cities/edit', 'CitiesController@edit');
                Route::get('cities/show', 'CitiesController@display');
                Route::get('cities/delete', 'CitiesController@delete');
                //banners
                Route::get('banners', 'BannersController@index');
                Route::post('banners/add', 'BannersController@add');
                Route::post('banners/edit', 'BannersController@edit');
                Route::get('banners/show', 'BannersController@display');
                Route::get('banners/delete', 'BannersController@delete');
            });
        });
    });
});
$status = [
    0 => 'تم الطلب',
    1 => 'تم الموافقه',
    2 => 'تم طلب مندوب',
    3 => 'تم التعاقد مع المندوب',
    4 => 'تم الأستلام من الأسره ',
    5 => 'تم التسليم ',
    6 => 'تم رفض الطلب '
];
define('status', $status);
